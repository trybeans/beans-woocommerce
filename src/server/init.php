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

        register_activation_hook(__FILE__, function () {
            Helper::postWebhookStatus('activated');
        });

        register_deactivation_hook(__FILE__, function () {
            Helper::removeTransients();
            Helper::postWebhookStatus('deactivated');
        });
    }
}
