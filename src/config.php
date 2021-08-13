<?php

defined('ABSPATH') or exit;


if (!defined('BEANS_VERSION')) {
    define('BEANS_VERSION', '3.2.4');
    define('BEANS_PLUGIN_FILENAME', plugin_basename(__FILE__));
    define('BEANS_PLUGIN_PATH', plugin_dir_path(__FILE__));
    define('BEANS_INFO_LOG', BEANS_PLUGIN_PATH . 'log.txt');

/* Admin */
    define('BEANS_WOO_BASE_MENU_SLUG', 'beans-woo');
    define('BEANS_WOO_API_ENDPOINT', get_site_url() . '/wp-json/wc/v3/');
    define('BEANS_WOO_API_AUTH_ENDPOINT', get_site_url() . '/wc-auth/v1/authorize/');
    define('BEANS_WOO_MENU_LINK', admin_url('?page=' . BEANS_WOO_BASE_MENU_SLUG));

/* Storefront */
    define('BEANS_LIANA_COUPON_UID', 'redeem_points');
}


/* Server */
