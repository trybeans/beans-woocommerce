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
        if ( ! Helper::isConfigured() ) {
            echo '<div class="error" style="margin-left: auto"><div style="margin: 10px auto;"> Beans: ' .
                 __( 'Beans is not properly configured.', 'beans-woo' ) .
                 ' <a href="' . admin_url( 'admin.php?page=beans-woo' ) . '">' .
                 __( 'Set up', 'beans-woo' ) .
                 '</a>' .
                 '</div></div>';
        }
    }

}
