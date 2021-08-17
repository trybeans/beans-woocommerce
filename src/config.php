<?php

defined('ABSPATH') or exit;


if (!defined('BEANS_PLUGIN_VERSION')) {
    define('BEANS_PLUGIN_VERSION', '3.3.0');
    define('BEANS_PLUGIN_FILENAME', plugin_basename(__FILE__));
    define('BEANS_PLUGIN_PATH', plugin_dir_path(__FILE__));

/* Admin */
    define('BEANS_WOO_BASE_MENU_SLUG', 'beans-woo');
    define('BEANS_WOO_MENU_LINK', admin_url('?page=' . BEANS_WOO_BASE_MENU_SLUG));
}


/* Server */
