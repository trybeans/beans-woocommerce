<?php

namespace BeansWoo\Front\Bamboo;

include_once('observer.php');
include_once('block.php');

//define( 'BEANS_LIANA_COUPON_UID', 'redeem_points' );


class Main {

    public static function init() {

        Observer::init();
        Block::init();
    }
}
