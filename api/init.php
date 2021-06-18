<?php

namespace BeansWoo\API;

include_once ('api-beans-rest-woocommerce.php');

class Main {
    public static function init() {
        BeansRestWoocommerce::init();
    }
}
