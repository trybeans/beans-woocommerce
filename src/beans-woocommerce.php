<?php

/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Marketing Apps for WooCommerce.
 * Version: 3.3.8
 * Author: Beans
 * Author URI: https://www.trybeans.com/
 * Text Domain: beans-woo
 * Domain Path: /languages
 * Requires PHP: 7.1
 * Requires at least: 5.2
 * WC requires at least: 4.1
 * WC tested up to: 6.5.*
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
    define('BEANS_PLUGIN_VERSION', '3.3.8');
}


require_once 'includes/Beans.php';
require_once 'includes/Helper.php';

require_once 'server/init.php';
require_once 'admin/init.php';
require_once 'storefront/init.php';

use BeansWoo\Server\Main as ServerMain;
use BeansWoo\Admin\Main as AdminMain;
use BeansWoo\StoreFront\Main as StoreFrontMain;

if (!class_exists('WC_Beans')) :

    class WC_Beans
    {
        protected static $instance = null;

        protected function __construct()
        {
            add_action('init', array(__CLASS__, 'init'), 10, 1);

            ServerMain::registerPluginActivationHooks();
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
            if (is_admin()) {
                // If on Admin Dashboard
                AdminMain::init();

                return;
            } elseif (wp_doing_ajax()) {
                // For AJAX
                StoreFrontMain::initAjax();

                return;
            } elseif (wp_doing_cron()) {
                // For background process
                ServerMain::init();

                return;
            } elseif (self::isRestRequest()) {
                // For REST API request
                ServerMain::init();

                return;
            }

            // For classic web page visit

            StoreFrontMain::init();
        }

        private static function isRestRequest()
        {
            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            return strpos($uri, '/wp-json') !== false;
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

// \BeansWoo\Helper::log($_SERVER['REQUEST_URI']);

$GLOBALS['wc_beans'] = WC_getBeansInstance();
