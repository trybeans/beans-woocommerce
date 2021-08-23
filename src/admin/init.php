<?php

namespace BeansWoo\Admin;

defined('ABSPATH') or die;

require_once "Router.php";
require_once "Connect/Connector.php";
require_once "Inspector/Inspector.php";

class Main
{

    public static function init()
    {
        if (!is_admin()) {
            return;
        }
        Router::init();
        Connector::init();
    }
}
