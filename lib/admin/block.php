<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

class Block {


    public static function init(){
    }

    public static function render_settings_page() {

        if ( isset( $_GET['card'] ) && isset( $_GET['token'] ) ) {
            if ( self::_processSetup() ) {
                return wp_redirect( admin_url( 'admin.php?page=beans-woo' ) );
            }
        }

        if ( Helper::isConfigured() ) {
            return include( dirname( __FILE__ ) . '/block.info.php' );
        }

        return include( dirname( __FILE__ ) . '/block.connect.php' );
    }

}