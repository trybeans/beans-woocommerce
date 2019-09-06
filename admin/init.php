<?php

namespace BeansWoo\Admin;

define( 'BEANS_WOO_API_ENDPOINT', get_site_url().'/wp-json/wc/v2/' );
define( 'BEANS_WOO_API_AUTH_ENDPOINT', get_site_url().'/wc-auth/v1/authorize/' );

include_once( "observer.php" );
include_once( "block-abstract.php" );

include_once ("block-liana.php");
include_once ("block-lotus.php");
include_once ("block-bamboo.php");
include_once ("block-snow.php");


class Main {

    public static function init() {

    	BlockLiana::init();
		BlockLotus::init();
		BlockSnow::init();
		BlockBamboo::init();

        Observer::init();
    }
}
