<?php

/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Marketing Apps for WooCommerce.
 * Version: 3.2.4
 * Author: Beans
 * Author URI: https://www.trybeans.com/
 * Text Domain: beans-woo
 * Domain Path: /languages
 * WC requires at least: 3.9
 * WC tested up to: 4.7.*
 * @author Beans
 */

// Exit if accessed directly
namespace BeansWoo;

if (! defined('ABSPATH')) {
    exit;
}

// Check if WooCommerce is active
if (
    ! in_array(
        'woocommerce/woocommerce.php',
        apply_filters('active_plugins', get_option('active_plugins'))
    )
) {
    return;
}

define('BEANS_VERSION', '3.2.4');
define('BEANS_PLUGIN_FILENAME', plugin_basename(__FILE__));
define('BEANS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BEANS_INFO_LOG', BEANS_PLUGIN_PATH . 'log.txt');

include_once('includes/Beans.php');
include_once('includes/Helper.php');

include_once('server/init.php');
include_once('admin/init.php');
include_once('storefront/init.php');

use BeansWoo\Server\Main as ServerMain;
use BeansWoo\Admin\Main as AdminMain;
use BeansWoo\StoreFront\Main as StoreFrontMain;

if (! class_exists('WC_Beans')) :
    class WC_Beans
    {
        protected static $instance = null;

        function __construct()
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
function wcBeansInstance()
{
     return WC_Beans::instance();
}

$GLOBALS['wc_beans'] = wcBeansInstance();


function wcBeansPluginActivate()
{
    Helper::postWebhookStatus('activated');
}

register_activation_hook(__FILE__, function () {
    wcBeansPluginActivate();
});


function wcBeansPluginDeactivate()
{
    Helper::removeTransients();
    Helper::postWebhookStatus('deactivated');
}

register_deactivation_hook(__FILE__, function () {
    wcBeansPluginDeactivate();
});
