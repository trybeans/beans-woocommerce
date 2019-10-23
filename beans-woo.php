<?php
/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Marketing Apps for WooCommerce.
 * Version: 3.1.3
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

define('BEANS_VERSION',                 '3.1.3');
define('BEANS_PLUGIN_FILENAME',         plugin_basename(__FILE__));
define('BEANS_PLUGIN_PATH',             plugin_dir_path(__FILE__));
define('BEANS_INFO_LOG',                BEANS_PLUGIN_PATH.'log.txt');

include_once( 'includes/beans.php' );
include_once( 'includes/helper.php' );

include_once( 'admin/init.php' );

include_once ('api/api-beans-rest-woocommerce.php');

include_once('front/liana/init.php');
include_once('front/poppy/init.php');
include_once('front/snow/init.php');

use BeansWoo\Admin\Main as AdminMain;
use BeansWoo\Front\Liana\Main as LianaMain;
use BeansWoo\Front\Poppy\Main as PoppyMain;
use BeansWoo\Front\Snow\Main as SnowMain;
use BeansWoo\API\BeansRestWoocommerce;
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

            AdminMain::init();

            BeansRestWoocommerce::init();

            LianaMain::init();
            PoppyMain::init();
            SnowMain::init();
        }

        public static function send_status($args){
            $api = Helper::API(1);
            $api->endpoint = 'http://e9a37f58.eu.ngrok.io/v3';

            $api->post('/radix/woocommerce/hook/shop/plugin_status', $args);
        }
    }

endif;


/**
 * Use instance to avoid multiple api call so Beans can be super fast.
 */
function wc_beans_instance() {
     return WC_Beans::instance();
}

register_activation_hook(__FILE__, function(){
    $GLOBALS['wc_beans'] = wc_beans_instance();

    if ( ! is_null(Helper::getConfig('secret'))){
       $args = [
           'is_active' => 'activated',
           'shop_url' => home_url(),
       ] ;
       WC_Beans::send_status($args);
    }

});

function wc_beans_plugins_deactivate(){
    $args = array(
        'is_active' => 'deactivated',
        'shop_url' => home_url(),
    );

    WC_Beans::send_status($args);
}

register_deactivation_hook(__FILE__, 'wc_beans_plugins_deactivate');
