<?php

/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Marketing Apps for WooCommerce.
 * Version: 3.3.0
 * Author: Beans
 * Author URI: https://www.trybeans.com/
 * Text Domain: beans-woo
 * Domain Path: /languages
 * WC requires at least: 4.1
 * WC tested up to: 5.5.*
 * @author Beans
 */

namespace BeansWoo;

// Exit if accessed directly
defined('ABSPATH') or exit;

// Check if WooCommerce is active
$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if (!in_array('woocommerce/woocommerce.php', $active_plugins)) {
    return;
}

if (!defined('BEANS_PLUGIN_FILENAME')) {
    define('BEANS_PLUGIN_FILENAME', plugin_basename(__FILE__));
}

if (!defined('BEANS_PLUGIN_PATH')) {
    define('BEANS_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('BEANS_PLUGIN_VERSION')) {
    define('BEANS_PLUGIN_VERSION', '3.3.0');
}


require_once 'includes/Beans.php';
require_once 'includes/Helper.php';

require_once 'server/init.php';
require_once 'admin/init.php';
require_once 'storefront/init.php';

use BeansWoo\Server\Main as ServerMain;
use BeansWoo\Admin\Main as AdminMain;
use BeansWoo\StoreFront\Main as StoreFrontMain;

if (! class_exists('WC_Beans')) :

    class WC_Beans
    {
        protected static $instance = null;

        protected function __construct()
        {
            add_action('init', array(__CLASS__, 'init'), 10, 1);
        }

        public static function instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function init()
        {
            die("<h1>Here you go my brother</h1> " . BEANS_PLUGIN_FILENAME);
            if (! session_id()) {
                session_start();
            }

            AdminMain::init();
            StoreFrontMain::init();
            ServerMain::init();
        }
    }

endif;


/**
 * Use instance to avoid multiple api call so Beans can be super fast.
 */
function WC_getBeansInstance()
{
     return WC_Beans::instance();
}

$GLOBALS['wc_beans'] = WC_getBeansInstance();
