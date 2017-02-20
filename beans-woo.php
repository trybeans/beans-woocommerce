<?php
/**
 * Plugin Name: Beans
 * Plugin URI: https://business.trybeans.com/
 * Description: Reward your customers to grow your business.
 * Version: 0.9.2
 * Author: Beans
 * Author URI: https://business.trybeans.com/
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

define('BEANS_VERSION',                 '0.9.1');
define('BEANS_COUPON_UID',              'beans_redeem');
define('BEANS_PLUGIN_FILE',             plugin_basename(__FILE__));
define('BEANS_CSS_FILE',                plugin_dir_path(__FILE__).'local/beans.css');
define('BEANS_CSS_MASTER',              plugin_dir_path(__FILE__).'assets/beans.css');
define('BEANS_REWARD_PAGE',             plugin_dir_path(__FILE__).'includes/reward.php');
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
//            array('wp_enqueue_scripts',         'load_beans_script',        10, 1),
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

        function load_beans_script() {
            // Register the script like this for a plugin:

            // or
            // Register the script like this for a theme:
            //wp_register_script( 'custom-script', get_template_directory_uri() . '/js/custom-script.js' );

            // For either a plugin or a theme, you can then enqueue the script:
            //wp_enqueue_script( 'custom-script' );
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


