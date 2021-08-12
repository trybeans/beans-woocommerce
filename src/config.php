<?php

defined('ABSPATH') or exit;


if (!defined('BEANS_VERSION')) {
    define('BEANS_VERSION', '3.2.4');
}

if (!defined('BEANS_PLUGIN_FILENAME')) {
    define('BEANS_PLUGIN_FILENAME', plugin_basename(__FILE__));
}

if (!defined('BEANS_PLUGIN_PATH')) {
    define('BEANS_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('BEANS_INFO_LOG')) {
    define('BEANS_INFO_LOG', BEANS_PLUGIN_PATH . 'log.txt');
}

/* Admin */
if (!defined('BEANS_WOO_BASE_MENU_SLUG')) {
    define('BEANS_WOO_BASE_MENU_SLUG', 'beans-woo');
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

/* Storefront */
if (!defined('BEANS_LIANA_COUPON_UID')) {
    define('BEANS_LIANA_COUPON_UID', 'redeem_points');
}


/* Server */
