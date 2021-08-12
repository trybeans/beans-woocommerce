<?php

namespace BeansWoo\Admin\Configurator;

class CheckConfig
{
    public static $wp_version_supported = '5.0';
    public static $woo_version_supported = '3.9';
    public static $php_version_supported = '5.6.0';

    public static $woo_version;
    public static $php_version;
    public static $woo_is_supported;
    public static $wp_is_supported;
    public static $php_is_supported;
    public static $curl_is_supported;
    public static $json_is_supported;
    public static $beans_is_supported;
    public static $woo_api_uri_is_up;
    public static $woo_api_auth_is_up;
    public static $permalink_is_supported;
    public static $wp_permalink_is_supported;
    public static $woo_api_uri_http_status = null;
    public static $woo_api_uri_content_type = null;
    public static $woo_api_auth_http_status = null;
    public static $woo_api_auth_content_type = null;

    public static function init()
    {
        self::$woo_version = self::pluginVersion('woocommerce');
        self::$woo_is_supported = version_compare(self::$woo_version, self::$woo_version_supported) >= 0;

        global $wp_version;
        self::$wp_is_supported = version_compare($wp_version, self::$wp_version_supported) >= 0;

        self::$php_version = phpversion();
        self::$php_is_supported = version_compare(self::$php_version, self::$php_version_supported) >= 0;

        self::$curl_is_supported = function_exists('curl_init');

        self::$json_is_supported = function_exists('json_decode');

        self::$permalink_is_supported = !is_null(get_option('permalink_structure'));

        self::$wp_permalink_is_supported = !is_null(get_option('permalink_structure'));

        self::$beans_is_supported = self::$woo_is_supported && self::$wp_is_supported
            && self::$php_is_supported && self::$curl_is_supported && self::$json_is_supported
            && self::$permalink_is_supported && self::$wp_permalink_is_supported;

        self::$woo_api_uri_is_up = self::$beans_is_supported ? self::checkWooApiUri(
            self::$woo_api_uri_http_status,
            self::$woo_api_uri_content_type
        ) : null;

        self::$beans_is_supported = self::$woo_api_uri_is_up;

        self::$woo_api_auth_is_up = self::$beans_is_supported ? self::checkWooApiAuth(
            self::$woo_api_auth_http_status,
            self::$woo_api_auth_content_type
        ) : null;

        self::$beans_is_supported = self::$woo_api_auth_is_up;
    }

    protected static function pluginVersion($plugin_name)
    {
        if (!function_exists('get_plugins')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        $plugin_folder = get_plugins("/$plugin_name");

        return $plugin_folder["$plugin_name.php"]['Version'];
    }

    protected static function checkWooApiUri(&$http_status, &$content_type)
    {
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL => BEANS_WOO_API_ENDPOINT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 80
        );
        curl_setopt_array($ch, $curlConfig);
        curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        return in_array($http_status, [200, 503]);
    }

    protected static function checkWooApiAuth(&$http_status, &$content_type)
    {
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL => BEANS_WOO_API_AUTH_ENDPOINT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 80
        );
        curl_setopt_array($ch, $curlConfig);
        curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        return in_array($http_status, [401, 503]) && strpos($content_type, 'text/html') !== false;
    }
}
