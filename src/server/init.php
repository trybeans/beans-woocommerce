<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;

require_once 'API.php';
require_once 'Hooks.php';

defined('ABSPATH') or die;

class Main
{

    public static function init()
    {
        Hooks::init();
        add_filter( 'woocommerce_rest_api_get_rest_namespaces', array(__CLASS__, 'register_api' ));

    }

    public static function register_api($controllers) {
        $controllers['beans/v1']['riper_version'] = 'APIRESTController';
        return $controllers;
    }

    public static function registerPluginActivationHooks()
    {
        register_activation_hook(BEANS_PLUGIN_FILENAME, function () {
            Hooks::postWebhookStatus('activated');
        });

        register_deactivation_hook(BEANS_PLUGIN_FILENAME, function () {
            Helper::clearTransients();
            Hooks::postWebhookStatus('deactivated');
        });
    }
}
