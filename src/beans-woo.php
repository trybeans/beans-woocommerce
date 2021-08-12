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

namespace BeansWoo;

// Exit if accessed directly
defined('ABSPATH') || exit;

// Check if WooCommerce is active
$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if (!in_array('woocommerce/woocommerce.php', $active_plugins)) {
    return;
}

include_once('config.php');
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
        protected static $_instance = null;


        function __construct()
        {
            include_once('constants.php');
            add_action('init', array(__CLASS__, 'init'), 10, 1);
        }

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
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
