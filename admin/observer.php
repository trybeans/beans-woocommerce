<?php

namespace BeansWoo\Admin;

defined('ABSPATH') or die;

//use BeansWoo\Helper;
use BeansWoo\Admin\Connector\FoxxConnector;
use BeansWoo\Admin\Connector\LianaConnector;
use BeansWoo\Admin\Connector\SnowConnector;
//use BeansWoo\Admin\Connector\BambooConnector;
//use BeansWoo\Admin\Connector\LotusConnector;

class Observer {

    public static $submenu_pages = [];

    public static function init(){

        static::$submenu_pages = [
            [
                'parent_slug' => BEANS_WOO_BASE_MENU_SLUG,
                'page_title' => ucfirst(LianaConnector::$app_name),
                'menu_title' => ucfirst(LianaConnector::$app_name),
                'capability' => 'manage_options',
                'menu_slug' => BEANS_WOO_BASE_MENU_SLUG,
                'callback' => '',

            ],

//            [
//                'parent_slug' => BEANS_WOO_BASE_MENU_SLUG,
//                'page_title' => ucfirst(BambooConnector::$app_name),
//                'menu_title' => ucfirst(BambooConnector::$app_name),
//                'menu_slug' =>  BEANS_WOO_BASE_MENU_SLUG . "-" . BambooConnector::$app_name,
//                'capability' => 'manage_options',
//                'callback' => ['\BeansWoo\Admin\Connector\BambooConnector', 'render_settings_page'],
//            ],
//
//            [
//                'parent_slug' => BEANS_WOO_BASE_MENU_SLUG,
//                'page_title' => ucfirst(LotusConnector::$app_name),
//                'menu_title' => ucfirst(LotusConnector::$app_name),
//                'menu_slug' =>  BEANS_WOO_BASE_MENU_SLUG . "-" . LotusConnector::$app_name,
//                'capability' => 'manage_options',
//                'callback' => ['\BeansWoo\Admin\Connector\LotusConnector', 'render_settings_page'],
//            ],

            [
                'parent_slug' => BEANS_WOO_BASE_MENU_SLUG,
                'page_title' => ucfirst(SnowConnector::$app_name),
                'menu_title' => ucfirst(SnowConnector::$app_name),
                'menu_slug' =>  BEANS_WOO_BASE_MENU_SLUG . "-" . SnowConnector::$app_name,
                'capability' => 'manage_options',
                'callback' => ['\BeansWoo\Admin\Connector\SnowConnector', 'render_settings_page'],
            ],

            [
                'parent_slug' => BEANS_WOO_BASE_MENU_SLUG,
                'page_title' => ucfirst(FoxxConnector::$app_name),
                'menu_title' => ucfirst(FoxxConnector::$app_name),
                'menu_slug' =>  BEANS_WOO_BASE_MENU_SLUG . "-" . FoxxConnector::$app_name,
                'capability' => 'manage_options',
                'callback' => ['\BeansWoo\Admin\Connector\FoxxConnector', 'render_settings_page'],
            ],

        ];

//        add_action( 'admin_notices',                array('\BeansWoo\Admin\Connector\LotusConnector', 'admin_notice' ) );
//        add_action( 'admin_notices',                array('\BeansWoo\Admin\Connector\BambooConnector', 'admin_notice' ) );
        add_action( 'admin_notices',                array('\BeansWoo\Admin\Connector\LianaConnector', 'admin_notice' ) );
	    add_action( 'admin_notices',                array('\BeansWoo\Admin\Connector\SnowConnector', 'admin_notice' ) );
	    add_action( 'admin_notices',                array('\BeansWoo\Admin\Connector\FoxxConnector', 'admin_notice' ) );
        add_action( 'admin_menu',                   array( __CLASS__, 'admin_menu' ));
        add_action( 'admin_enqueue_scripts',        array(__CLASS__, 'admin_style'));
    }

    public static function plugin_row_meta( $links, $file ) {
        if ( $file == BEANS_PLUGIN_FILENAME ) {

            $row_meta = array(
                'help'    => '<a href="http://help.trybeans.com/" title="Help">Help Center</a>',
                'support' => '<a href="mailto:hello@trybeans.com" title="Support">Contact Support</a>',
            );

            return array_merge( $links, $row_meta );
        }

        return (array) $links;
    }

    public static function admin_style(){
        wp_enqueue_style( 'admin-styles', plugins_url( 'assets/beans-admin.css' ,
            BEANS_PLUGIN_FILENAME ));
    }

    public static function admin_menu() {

        if ( current_user_can( 'manage_options' ) ) {
        	add_menu_page(
        	    'Beans',
                'Beans',
                'manage_options',
                BEANS_WOO_BASE_MENU_SLUG,
		        ['\BeansWoo\Admin\Connector\LianaConnector', 'render_settings_page'],
		        plugins_url('/assets/beans_wordpressIcon.svg', BEANS_PLUGIN_FILENAME),
                56);

        	foreach (static::$submenu_pages as $submenu_page){
        	   add_submenu_page(
        	       $submenu_page['parent_slug'],
                   $submenu_page['page_title'],
                   $submenu_page['menu_title'],
                   $submenu_page['capability'],
                   $submenu_page['menu_slug'],
                   $submenu_page['callback']
               ) ;
            }
        }
    }
/**
    private static function synchronise(){

        $estimated_account = 0;
        $contact_data = array();

        $users_count = count_users();
        if(isset($users_count['avail_roles']['customer'])){
            $estimated_account = $users_count['avail_roles']['customer'];
        }

        if (current_user_can('manage_woocommerce')){
            $admin = wp_get_current_user();
            $contact_data = array(
                'email' => $admin->user_email,
                'last_name' => $admin->user_lastname,
                'first_name' => $admin->user_firstname,
            );
        }

        $country_code = get_option('woocommerce_default_country');
        if($country_code && strpos($country_code, ':') !== false){
            try {
                $country_parts = explode( ':', $country_code );
                $country_code = $country_parts[0];
            }catch(\Exception $e){}
        }

        $data = array(
            'website' => get_site_url(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'company_name' => get_bloginfo('name'),
            'store_name' => get_bloginfo('name'),
            'country_code' => $country_code,
            'contact' => $contact_data,
            'estimated_account' => $estimated_account,
            'php_version' => phpversion(),
            'plugin_version' => BEANS_VERSION,
        );

        try{
            $response = Helper::API()->post('hook/integrations/woocommerce/synchronise', $data);
            if(isset($response['result'])) return $response['result'];
        }catch (\Exception $e) {
            Helper::log('Unable to sync: '.$e->getMessage());
        }

        return false;
    }
**/
}
