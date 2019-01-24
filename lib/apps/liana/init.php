<?php

namespace BeansWoo\Liana;

include_once( plugin_dir_path( __FILE__ ) . 'observer.php' );
include_once( plugin_dir_path( __FILE__ ) . 'block.php' );


define( 'BEANS_LIANA_COUPON_UID', 'redeem_points' );


class Main {

    public static function init() {

        Observer::init();
        Block::init();

    }
}




