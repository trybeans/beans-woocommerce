<?php
/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Marketing Apps for WooCommerce.
 * Version: 3.1.2
 * Author: Beans
 * Author URI: https://www.trybeans.com/
 * Text Domain: beans-woo
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 3.7.*
 * @author Beans
 */

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) )
    exit;

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
    return;

define('BEANS_VERSION',                 '3.1.2');
define('BEANS_PLUGIN_FILENAME',         plugin_basename(__FILE__));
define('BEANS_PLUGIN_PATH',             plugin_dir_path(__FILE__));
define('BEANS_INFO_LOG',                BEANS_PLUGIN_PATH.'log.txt');

include_once( 'includes/beans.php' );
include_once( 'includes/helper.php' );

include_once( 'admin/init.php' );

include_once('front/liana/init.php');
include_once('front/snow/init.php');

use BeansWoo\Admin\Main as AdminMain;
use BeansWoo\Front\Liana\Main as LianaMain;
use BeansWoo\Front\Snow\Main as SnowMain;

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

            AdminMain::init();

            LianaMain::init();
            SnowMain::init();
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
