<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;

require_once 'WebApi/ConnectorRESTController.php';
require_once 'WebApi/FilterRESTController.php';
require_once 'WebApi/LogRESTController.php';
require_once 'WebHook/ReviewHookController.php';
require_once 'WebHook/SystemHookController.php';

defined('ABSPATH') or die;

class Main
{
    public static function init()
    {
        add_filter('woocommerce_rest_api_get_rest_namespaces', array(__CLASS__, 'registerRESTRoutes'));

        SystemHookController::init();
        ReviewHookController::init();

        if (!Helper::isSetup()) {
            return;
        }

        $display = Helper::getDisplay();

        if (empty($display)) {
            Helper::log('Server: Display Liana is empty');
            return;
        }

        \BeansWoo\StoreFront\LianaSubscriptionObserver::init($display);
    }

    public static function registerRESTRoutes($controllers)
    {
        $controllers['wc-beans/v2']['connector'] = 'BeansWoo\Server\ConnectorRESTController';
        $controllers['wc-beans/v2']['filter'] = 'BeansWoo\Server\FilterRESTController';
        $controllers['wc-beans/v2']['log'] = 'BeansWoo\Server\LogRESTController';
        return $controllers;
    }

    public static function registerPluginActivationHooks()
    {
        register_activation_hook(BEANS_PLUGIN_FILENAME, function () {
            ConnectorRESTController::postWebhook('activated');
        });

        register_deactivation_hook(BEANS_PLUGIN_FILENAME, function () {
            ConnectorRESTController::postWebhook('deactivated');
        });
    }
}
