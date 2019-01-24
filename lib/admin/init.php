<?php

namespace BeansWoo\Admin;

define( 'BEANS_WOO_API_ENDPOINT', get_site_url().'/wp-json/wc/v2/' );
define( 'BEANS_WOO_API_AUTH_ENDPOINT', get_site_url().'/wc-auth/v1/authorize/' );

include_once( plugin_dir_path( __FILE__ ) . 'setup.php' );


class Main {

    public static function init() {

    }
}




