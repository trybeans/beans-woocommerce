<?php

namespace BeansWoo\Admin;

class Inspector
{
    public static $wc_endpoint_url_api;
    public static $wc_endpoint_url_auth;

    private const VERSIONING_SUPPORTED  = array(
        'wordpress' => '5.2',
        'woocommerce' => '4.1',
        'php' => '7.1',
    );

    public static $versioning_installed = array(
        'wordpress' => null,
        'woocommerce' => null,
        'php' => null,
    );

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

        self::$wc_endpoint_url_api = get_site_url() . '/wp-json/wc/v3/';
        self::$wc_endpoint_url_auth = get_site_url() . '/wc-auth/v1/authorize/';

        self::checkVersioning();
    }

    public static function checkVersioning()
    {
        global $wp_version;
        self::$versioning_installed = array(
        'php' => phpversion(),
        'wordpress' => $wp_version,
        'woocommerce' => self::pluginVersion('woocommerce'),
        );

        self::$php_is_supported = version_compare(
            self::$versioning_installed['php'],
            self::VERSIONING_SUPPORTED['php']
        ) >= 0;

        self::$wp_is_supported = version_compare(
            self::$versioning_installed['wordpress'],
            self::VERSIONING_SUPPORTED['wordpress']
        ) >= 0;

        self::$woo_is_supported = version_compare(
            self::$versioning_installed['woocommerce'],
            self::VERSIONING_SUPPORTED['woocommerce']
        ) >= 0;

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

    private static function pluginVersion($plugin_name)
    {
        if (!function_exists('get_plugins')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        $plugin_folder = get_plugins("/$plugin_name");

        return $plugin_folder["$plugin_name.php"]['Version'];
    }

    private static function checkWooApiUri(&$http_status, &$content_type)
    {
        $ch = curl_init();
        $curl_config = array(
            CURLOPT_URL => self::$wc_endpoint_url_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 80
        );
        curl_setopt_array($ch, $curl_config);
        curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        return in_array($http_status, [200, 503]);
    }

    private static function checkWooApiAuth(&$http_status, &$content_type)
    {
        $ch = curl_init();
        $curl_config = array(
            CURLOPT_URL => self::$wc_endpoint_url_auth,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 80
        );
        curl_setopt_array($ch, $curl_config);
        curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        return in_array($http_status, [401, 503]) && strpos($content_type, 'text/html') !== false;
    }
}