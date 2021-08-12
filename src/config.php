<?php

const BEANS_VERSION = '3.2.4';

if (!defined('BEANS_PLUGIN_URL')) {
    define('BEANS_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('BEANS_INFO_LOG')) {
    define("BEANS_INFO_LOG", plugin_dir_path(__FILE__) . 'log.txt');
}

/** Admin constants */
if (!defined('BEANS_WOO_BASE_MENU_SLUG')) {
    define("BEANS_WOO_BASE_MENU_SLUG", 'beans-woo');
}

if (!defined('BEANS_WOO_API_ENDPOINT')) {
    define('BEANS_WOO_API_ENDPOINT', get_site_url() . '/wp-json/wc/v3/');
}

if (!defined('BEANS_WOO_API_AUTH_ENDPOINT')) {
    define('BEANS_WOO_API_AUTH_ENDPOINT', get_site_url() . '/wc-auth/v1/authorize/');
}

if (!defined('BEANS_WOO_MENU_LINK')) {
    define('BEANS_WOO_MENU_LINK', admin_url('?page=' . BEANS_WOO_BASE_MENU_SLUG));
}


/** Front constants */
if (!defined('BEANS_LIANA_COUPON_UID')) {
    define("BEANS_LIANA_COUPON_UID", 'redeem_points');
}
