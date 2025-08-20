<?php

/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Loyalty and Rewards program
 * Version: 4.0.7
 * Author: Beans
 * Author URI: https://www.trybeans.com/
 * Text Domain: beans-woocommerce
 * Domain Path: /i18n/
 * Requires PHP: 8.0
 * Requires at least: 6.3
 * WC requires at least: 8.0
 * WC tested up to: 10.0.*
 * @author Beans
 */

namespace BeansWoo;

// Exit if accessed directly
defined('ABSPATH') or exit;

/**
 * Is the WooCommerce plugin installed and active?
 *
 * @since 3.6.2
 * @return boolean true if the WooCommerce plugin is installed and active
 */
function beans_is_woocommerce_active()
{
    $active_plugins = apply_filters('active_plugins', get_option('active_plugins', array()));
    if (is_multisite()) {
        $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
    }
    return in_array('woocommerce/woocommerce.php', $active_plugins) ||
        array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}

if (!beans_is_woocommerce_active()) {
    return;
}

if (!defined('BEANS_PLUGIN_FILENAME')) {
    define('BEANS_PLUGIN_FILENAME', plugin_basename(__FILE__));
}

if (!defined('BEANS_PLUGIN_DIRNAME')) {
    define('BEANS_PLUGIN_DIRNAME', dirname(__DIR__));
}

if (!defined('BEANS_PLUGIN_PATH')) {
    define('BEANS_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('BEANS_PLUGIN_VERSION')) {
    define('BEANS_PLUGIN_VERSION', '4.0.7');
}

if (!defined('BEANS_TRELLIS_VERSION')) {
    define('BEANS_TRELLIS_VERSION', '20240529');
}

require_once 'includes/Beans.php';
require_once 'includes/Preferences.php';
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

        /**
         * Constructor
         *
         * @return void
         *
         * @since 1.0
         */
        protected function __construct()
        {
            add_action('init', array(__CLASS__, 'init'), 10, 1);

            ServerMain::registerPluginActivationHooks();

            /**
             * Declare compatibility with WooCommerce HPOS
             * https://woocommerce.com/document/high-performance-order-storage/
             */
            add_action('before_woocommerce_init', function () {
                if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
                    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                        'custom_order_tables',
                        __FILE__,
                        true
                    );
                }
            });
        }

        /**
         * Create an in-memory instance of the Beans app
         * to avoid re-initializing the app on each access.
         *
         * @return WC_Beans
         *
         * @since 1.0
         */
        public static function instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Initialize the plugin by directing the
         * request through the appropriate entrypoint.
         *
         * For example, requests made by admin, and request made
         * by shoppers are routed to different endpoints as
         * they serve different purposes.
         *
         * @return void
         *
         * @since 1.0
         */
        public static function init()
        {
            self::loadTranslation();

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

        /**
         * Verify is a request is made from AJAX or
         * more generally speaking by a JS script on the storefront
         *
         * @return bool true if REST request
         *
         * @since 3.3.0
         */
        private static function isRestRequest()
        {
            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            return strpos($uri, '/wp-json') !== false;
        }

        /**
         * Load the translation strings for the plugin.
         * This will first check for the user local and fallback to the website local
         *
         * This will first look for the translations in:
         * "wp-content/languages/plugins/beans-woocommerce-fr_FR.mo"
         * If not found, it will fallback to:
         * "wp-content/plugins/beans-woocommerce/i18n/beans-woocommerce-fr_FR.mo"
         *
         * @return void
         *
         * @since 3.6.0
         */
        private static function loadTranslation()
        {
            load_plugin_textdomain('beans-woocommerce', false, BEANS_PLUGIN_DIRNAME . '/i18n');
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
