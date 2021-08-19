<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;

require_once 'Hooks.php';

defined('ABSPATH') or die;

class Main
{

    public static function init()
    {
        Hooks::init();
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
