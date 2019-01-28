<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

class Observer {

    public static function init(){
        add_action( 'admin_notices',                array( __CLASS__, 'admin_notice' ) );
        add_action( 'admin_menu',                   array( __CLASS__, 'admin_menu' ), 100 );
        add_filter( 'plugin_row_meta',              array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
    }

    public static function plugin_row_meta( $links, $file ) {
        if ( $file == BEANS_PLUGIN_FILENAME ) {

//            $synced = Helper::getConfig('synced');
//            if( empty($synced)){
//                if(self::synchronise()){
//                    Helper::setConfig('synced', BEANS_VERSION);
//                }
//            }

            $row_meta = array(
                'help'    => '<a href="http://help.trybeans.com/" title="Help">Help Center</a>',
                'support' => '<a href="mailto:hello@trybeans.com" title="Support">Contact Support</a>',
//                'api'       => '<a href="http://www.trybeans.com/doc/api/" title="Help">API doc</a>',
            );

            return array_merge( $links, $row_meta );
        }

        return (array) $links;
    }

    public static function admin_menu() {
        if ( current_user_can( 'manage_woocommerce' ) ) {
            add_submenu_page( 'woocommerce', 'Beans',
                'Beans', 'manage_woocommerce',
                'beans-woo', array( '\BeansWoo\Admin\Block', 'render_settings_page' ) );
        }
    }

    public static function admin_notice() {
        if ( ! Helper::isSetup() ) {
            echo '<div class="error" style="margin-left: auto"><div style="margin: 10px auto;"> Beans: ' .
                 __( 'Beans is not properly setup.', 'beans-woo' ) .
                 ' <a href="' . admin_url( 'admin.php?page=beans-woo' ) . '">' .
                 __( 'Set up', 'beans-woo' ) .
                 '</a>' .
                 '</div></div>';
        }
    }

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
}
