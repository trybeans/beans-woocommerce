<?php

namespace BeansWoo;

use Beans;

class Helper
{
    const CONFIG_NAME = 'beans-config-3'; // private
    const LOG_FILE = BEANS_PLUGIN_PATH . 'log.txt'; // private

    public static $key = null;

    public static function getDomain($sub)
    {
        $key     = "BEANS_DOMAIN_$sub";
        $domains = array(
            'NAME'    => 'trybeans.com',
            'API'     => 'api.trybeans.com',
            'CONNECT' => 'connect.trybeans.com',
            'WWW'     => 'www.trybeans.com',
            'CDN'     => 'cdn.trybeans.com',
            'RADIX'    => 'api.radix.trybeans.com',
        );
        $val     = getenv($key);

        return empty($val) ? $domains[$sub] : getenv($key);
    }

    public static function API($domain = 'API')
    {
        if (!self::$key) {
            self::$key = self::getConfig('secret');
        }

        $beans = new Beans\Beans(self::$key);

        $beans->endpoint = 'https://' . self::getDomain($domain) . '/v3/';

        return $beans;
    }

    public static function requestTransientAPI($method, $path, $arg = null, $headers = null)
    {
        $transient_key = '$beans_' . str_replace('/', '_', $path);

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

    public static function clearTransients()
    {
        self::log('Deleting transients');
        delete_transient('$beans_liana_display_current');
        delete_transient('$beans_core_user_current_loginkey');

        # This will help to remove old transients.
        # todo; remove
        delete_transient('beans_liana_display');
        delete_transient('beans_card');
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
        return Helper::getConfig('key') && Helper::getConfig('card') && Helper::getConfig('secret');
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

    public static function log($info)
    {
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

    public static function replaceTags($string, $tags, $force_lower = false)
    {
        return preg_replace_callback(
            '/\\{([^{}]+)\\}/',
            function ($matches) use ($force_lower, $tags) {
                $key = $force_lower ? strtolower($matches[1]) : $matches[1];
                return array_key_exists($key, $tags) ? $tags[$key] : '';
            },
            $string
        );
    }

    public static function getAssetURL($path)
    {
        return plugins_url($path, BEANS_PLUGIN_FILENAME);
    }
}
