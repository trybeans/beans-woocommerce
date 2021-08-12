<?php

namespace BeansWoo\Server;

include_once('Hooks.php');

defined('ABSPATH') or die;

class Main
{
    public static function init()
    {
        BeansRestWoocommerce::init();
    }
}
