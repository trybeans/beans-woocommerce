<?php

namespace BeansWoo\Admin;

defined('ABSPATH') or die;

define('BEANS_WOO_BASE_MENU_SLUG', 'beans-woo');
define('BEANS_WOO_API_ENDPOINT', get_site_url() . '/wp-json/wc/v3/');
define('BEANS_WOO_API_AUTH_ENDPOINT', get_site_url() . '/wc-auth/v1/authorize/');
define('BEANS_WOO_MENU_LINK', admin_url('?page=' . BEANS_WOO_BASE_MENU_SLUG));

require_once "Observer/Observer.php";
require_once "Connector/Connector.php";
require_once "Configurator/Configurator.php";

class Main
{

    public static function init()
    {
        if (!is_admin()) {
            return;
        }

        Connector\Connector::init();
        Observer\Observer::init();
    }
}
