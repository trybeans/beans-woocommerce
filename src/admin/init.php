<?php

namespace BeansWoo\Admin;

defined('ABSPATH') or die;

include_once("observer.php");

include_once("connector/ultimate-connector.php");
include_once("check-config.php");

use BeansWoo\Admin\Connector\UltimateConnector;

class Main
{

    public static function init()
    {
        UltimateConnector::init();
        UltimateConnector::updateInstalledApps();

        if (! is_admin()) {
            return;
        }

        Observer::init();
    }
}
