<?php

namespace BeansWoo;

use Beans;

class Helper
{
    const CONFIG_NAME = 'beans-config-3'; // private
    const LOG_FILE = BEANS_PLUGIN_PATH . 'log.txt'; // private

    /**
     * List of settings params that can be configured by the merchant in
     * the WordPress dashboard.
     */
    public const OPTIONS = array(
        'checkout-redeem' => array(
            'handle' => 'beans-liana-display-redemption-checkout',
            'label' => "Redemption on checkout",
            'help_text' => 'Display redemption on checkout page.',
        ),
        'product-points' => array(
            'handle' => 'beans-liana-display-product-points',
            'label' => "Product points",
            'help_text' => 'Display points info on product page.',
        ),
        'cart-notices' => array(
            'handle' => 'beans-liana-display-cart-notices',
            'label' => "Cart notices",
            'help_text' => 'Display points balance notice on cart page.',
        ),
        'account-nav' => array(
            'handle' => 'beans-liana-display-account-navigation',
            'label' => "Account navigation",
            'help_text' => 'Display links to rewards, referral pages in account navigation.',
        ),
        'subscription-redemption' => array(
            'handle' => 'beans-liana-display-subscription-redemption',
            'label' => "Subscription redemption",
            'help_text' => 'Allow customers to redeem points on their upcoming subscription renewal.',
        ),
        'manual-registration' => array(
            'handle' => 'beans-liana-manual-registration',
            'label' => "Manual registration",
            'help_text' => (
                'Check this box to force new and existing customers to manually ' .
                'opt in the rewards program by submitting a form. ' .
                '(This is an experimental feature)'
            ),
        ),
    );

    public static $key = null;

    public static function getDomain($sub)
    {
        $key     = "BEANS_DOMAIN_$sub";
        $domains = array(
            'WWW'     => 'https://www.trybeans.com',
            'CDN'     => 'https://cdn.trybeans.com',
            'BOILER'  => 'https://app.trybeans.com',
            'CONNECT' => 'https://connect.trybeans.com',
            'STEM'    => 'https://api.trybeans.com/v3/',
            'RADIX'   => 'https://trellis.trybeans.com/v3/',
        );
        return getenv($key) || $domains[$sub];
    }

    /**
     * Create an instance of the Beans REST API Wrapper
     *
     * @param string $domain: The API service to query: `STEM` (default) or `RADIX`
     * @return Beans\Beans: An instance of the API Wrapper
     *
     * @since 3.0.0
     */
    public static function API($domain = 'STEM')
    {
        if (!self::$key) {
            self::$key = self::getConfig('secret');
        }

        return new Beans\Beans(self::$key, self::getDomain($domain));
    }

    public static function requestTransientAPI($method, $path, $arg = null, $headers = null)
    {
        $transient_key = 'beans_' . str_replace('/', '_', $path);

        $object = get_transient($transient_key);

        if (!is_null($object) && $object !== false) {
            // self::log("TRANSIENT Use Cache: ${method} ${path} ${transient_key}");
            return $object;
        }

        try {
            $object = self::API()->makeRequest($path, $arg, strtoupper($method), $headers);
        } catch (Beans\BeansError $e) {
            // self::log("TRANSIENT Query Error: ${method} ${path} ${transient_key} : " . $e->getMessage());
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
     *  1. The woocommerce logger accessible the WordPress admin dashboard under
     *      WooCommerce > Status > Logs or by going to /wp-admin/admin.php?page=wc-status&tab=logs
     *  2. A file located at the root of the Beans plugin for WordPress folder:
     *      /wp-content/plugins/beans-woocommerce-loyalty-rewards/log.txt
     *      /wp-content/plugins/beans-woocommerce/log.txt
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

        if (file_exists(self::LOG_FILE) && filesize(self::LOG_FILE) > 100000) {
            unlink(self::LOG_FILE);
        }

        if (!file_exists(self::LOG_FILE)) {
            file_put_contents(self::LOG_FILE, '');
        }

        if (!is_writable(self::LOG_FILE)) {
            return false;
        }

        $log = date('Y-m-d H:i:s.uP') . " => " . $info . PHP_EOL;

        try {
            file_put_contents(self::LOG_FILE, $log, FILE_APPEND);
        } catch (\Exception $e) {
            return false;
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
}
