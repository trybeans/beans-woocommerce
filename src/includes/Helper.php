<?php

namespace BeansWoo;

use Beans;

class Helper
{
    const CONFIG_NAME = 'beans-config-3'; // private
    const LOG_FILE = BEANS_PLUGIN_PATH . 'log.txt'; // private

    public static $api_key = null;

    public static function getDomain($sub)
    {
        $domains = array(
            'WWW'     => 'https://www.trybeans.com',
            'CDN'     => 'https://cdn.trybeans.com',
            'STEM'    => 'https://api.trybeans.com',
            'TRELLIS' => 'https://trellis.trybeans.com',
            'CONNECT' => 'https://connect.trybeans.com',
            'BOILER'  => 'https://app.trybeans.com',
        );

        $domain_key     = "BEANS_DOMAIN_$sub";
        $val     = getenv($domain_key);

        return empty($val) ? $domains[$sub] : $val;
    }

    /**
     * Create an instance of the Beans REST API Wrapper
     *
     * @param string $domain: The API service to query: `STEM` (default) or `TRELLIS`
     * @return Beans\Beans: An instance of the API Wrapper
     *
     * @since 3.0.0
     */
    public static function API($domain = 'STEM', $version = 'v3')
    {
        if (!self::$api_key) {
            self::$api_key = self::getConfig('secret');
        }

        return new Beans\Beans(self::$api_key, self::getDomain($domain) . '/' . $version . '/');
    }

    public static function requestTransientAPI(
        $method,
        $path,
        $domain = 'STEM',
        $version = 'v3',
        $arg = null,
        $headers = null
    ) {
        $transient_key = 'beans_' . str_replace('/', '_', $path);

        $object = get_transient($transient_key);

        if (!is_null($object) && $object !== false) {
            // self::log("TRANSIENT Use Cache: {$method} {$path} {$transient_key}");
            return $object;
        }

        try {
            $object = self::API($domain, $version)->makeRequest($path, $arg, strtoupper($method), $headers);
        } catch (Beans\BeansError $e) {
            // self::log("TRANSIENT Query Error: {$method} {$path} {$transient_key} : " . $e->getMessage());
            $object = array();
        }

        set_transient($transient_key, $object, 15 * 60);

        return $object;
    }

    /**
     * Clear all transients (cache)
     *
     * @return void
     *
     * @since 3.3.0
     */
    public static function clearTransients()
    {
        self::log('Deleting transients');
        delete_transient('beans_bamboo_display_current');
        delete_transient('beans_liana_display_current');
        delete_transient('beans_core_user_current_loginkey');
    }

    public static function getConfig($key)
    {
        $config = get_option(self::CONFIG_NAME);
        if (isset($config[$key])) {
            return $config[$key];
        }

        return null;
    }

    public static function setConfig($key, $value)
    {
        $config       = get_option(self::CONFIG_NAME);
        $config[$key] = $value;
        update_option(self::CONFIG_NAME, $config);
    }

    public static function isSetup()
    {
        return Helper::getConfig('merchant') && Helper::getConfig('card') && Helper::getConfig('secret');
    }

    public static function resetSetup()
    {
        foreach (['liana', 'bamboo'] as $app_name) {
            $app_page = self::getConfig($app_name . "_page");
            if (!is_null($app_page)) {
                wp_delete_post($app_page, true);
            }
        }
        self::clearTransients();
        delete_option(Helper::CONFIG_NAME);
        return true;
    }

    /**
     * Log a message to:
     *  The woocommerce logger accessible the WordPress admin dashboard under
     *  WooCommerce > Status > Logs or by going to /wp-admin/admin.php?page=wc-status&tab=logs
     *
     * @param string $info the text to print in the log
     * @return bool
     *
     * @since 3.0.0
     */
    public static function log($info)
    {

        try {
            wc_get_logger()->debug($info, array( 'source' => 'beans' ));
        } catch (\Exception $e) {
        }

        return true;
    }

    public static function getCart()
    {
        global $woocommerce;

        if (!empty($woocommerce->cart) && empty($woocommerce->cart->cart_contents)) {
            $woocommerce->cart->calculate_totals();
        }

        return $woocommerce->cart;
    }

    public static function getBeansPages()
    {
        return [
            'liana'  => \BeansWoo\StoreFront\LianaPage::getPageReferences(),
            'bamboo' => \BeansWoo\StoreFront\BambooPage::getPageReferences(),
        ];
    }

    public static function getCurrentPage()
    {

        // This function is used in `LianaProductObserver.updateProductPrice` to update the currency of the price in
        // point when the Buy with point feature is active. We do not update the price
        // on the `shop` and` product` pages. Be careful when updating this function.
        if (is_front_page()) {
            return 'home';
        }
        if (is_product()) {
            return 'product';
        }

        $pages = [
            wc_get_cart_url()                               => 'cart',
            wc_get_checkout_url()                           => 'cart',
            # DON'T TOUCH: This helps to show the redeem button on the checkout page
            wc_get_page_permalink('shop')                   => 'shop',
            wc_get_page_permalink('myaccount')              => 'login',
            get_permalink(Helper::getConfig('liana_page'))  => 'reward',
            get_permalink(Helper::getConfig('bamboo_page')) => 'referral',
        ];

        $current_page = esc_url(home_url($_SERVER['REQUEST_URI']));
        $current_page = explode("?", $current_page)[0];

        return isset($pages[$current_page]) ? $pages[$current_page] : '';
    }

    public static function getAssetURL($path)
    {
        return plugins_url($path, BEANS_PLUGIN_FILENAME);
    }

    /**
     * Define if the request_user has permission to access the resource.
     *
     * @param \WP_REST_Request $request
     * @return bool True if user has permission
     *
     * @since 3.4.0
     */
    public static function checkAPIPermission($request)
    {

        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error(
                "beans_rest_cannot_access",
                "Sorry, you are not allowed to access this resource.",
                array('status' => rest_authorization_required_code())
            );
        }
        return true;
    }
}
