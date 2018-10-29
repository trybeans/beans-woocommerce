<?php
/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Reward your customers to grow your business.
 * Version: 2.0.5
 * Author: Beans
 * Author URI: https://www.trybeans.com/
 * Text Domain: woocommerce-beans
 * Domain Path: /languages
 *
 * @author Beans
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
    return;

define('BEANS_VERSION',                 '2.0.5');
define('BEANS_COUPON_UID',              'beans_redeem');
define('BEANS_PLUGIN_FILE',             plugin_basename(__FILE__));
define('BEANS_INFO_LOG',                plugin_dir_path(__FILE__).'log.txt');

include_once(plugin_dir_path(__FILE__).'includes/beans.php');
include_once(plugin_dir_path(__FILE__).'includes/helper.php');
include_once(plugin_dir_path(__FILE__).'includes/block.php');
include_once(plugin_dir_path(__FILE__).'includes/observer.php');
include_once(plugin_dir_path(__FILE__).'includes/setup.php');

use BeansWoo\Helper;


if ( ! class_exists( 'WC_Beans' ) ) :

    class WC_Beans{
        protected static $_instance = null;

        function __construct(){
            add_filter('init',              array(__CLASS__, 'init'),         10, 1);
        }

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public static function init() {
            if ( ! session_id() ) {
                session_start();
            }

            \BeansWoo\Observer::init();
            \BeansWoo\Setup::init();
            \BeansWoo\Block::init();
        }
    }


endif;


/**
 * Use instance to avoid multiple api call so Beans can be super fast.
 */
function wc_beans_instance() {
    return WC_Beans::instance();
}

$GLOBALS['wc_beans'] = wc_beans_instance();


