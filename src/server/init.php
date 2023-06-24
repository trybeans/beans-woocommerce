<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;

require_once 'Api.php';
require_once 'Hooks.php';

defined('ABSPATH') or die;

class Main
{
    public static function init()
    {
        add_filter('woocommerce_rest_api_get_rest_namespaces', array(__CLASS__, 'registerRESTRoutes'));

        Hooks::init();

        if (!Helper::isSetup()) {
            return;
        }

        $display_liana = Helper::requestTransientAPI('GET', 'liana/display/current');

        if (empty($display_liana)) {
            Helper::log('Server: Display Liana is empty');
            return;
        }

        \BeansWoo\StoreFront\LianaSubscriptionObserver::init($display_liana);
    }

    public static function registerRESTRoutes($controllers)
    {
        $controllers['wc-beans/v1']['connector'] = 'BeansWoo\Server\ConnectorRESTController';
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
