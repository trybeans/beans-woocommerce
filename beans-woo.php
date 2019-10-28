<?php
/**
 * Plugin Name: Beans
 * Plugin URI: https://www.trybeans.com/
 * Description: Marketing Apps for WooCommerce.
 * Version: 3.1.4
 * Author: Beans
 * Author URI: https://www.trybeans.com/
 * Text Domain: beans-woo
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 3.7.*
 * @author Beans
 */

// Exit if accessed directly
namespace BeansWoo;

if ( ! defined( 'ABSPATH' ) )
    exit;

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
    return;

define('BEANS_VERSION',                 '3.1.4');
define('BEANS_PLUGIN_FILENAME',         plugin_basename(__FILE__));
define('BEANS_PLUGIN_PATH',             plugin_dir_path(__FILE__));
define('BEANS_INFO_LOG',                BEANS_PLUGIN_PATH.'log.txt');

include_once( 'includes/beans.php' );
include_once( 'includes/helper.php' );

include_once( 'admin/init.php' );

include_once ('api/api-beans-rest-woocommerce.php');

include_once('front/liana/init.php');
include_once('front/bamboo/init.php');
include_once('front/poppy/init.php');
include_once('front/snow/init.php');

use BeansWoo\Admin\Main as AdminMain;
use BeansWoo\Front\Liana\Main as LianaMain;
use BeansWoo\Front\Bamboo\Main as BambooMain;
use BeansWoo\Front\Poppy\Main as PoppyMain;
use BeansWoo\Front\Snow\Main as SnowMain;
use BeansWoo\API\BeansRestWoocommerce;

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
            BambooMain::init();
            PoppyMain::init();
            SnowMain::init();
        }

        public static function send_status($args){
            $api = Helper::API(1);

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

$GLOBALS['wc_beans'] = wc_beans_instance();


function wc_beans_plugin_activate(){
    $args = array(
        'status' => 'activated',
        'shop_url' => home_url(),
    );

    WC_Beans::send_status($args);
}

register_activation_hook(__FILE__, function(){
  wc_beans_plugin_activate();
});


function wc_beans_plugin_deactivate(){
    $args = array(
        'status' => 'deactivated',
        'shop_url' => home_url(),
    );

    WC_Beans::send_status($args);
}

register_deactivation_hook(__FILE__, function (){
    wc_beans_plugin_deactivate();
});

