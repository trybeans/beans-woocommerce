<?php

namespace BeansWoo\Logger;

use BeansWoo\Helper;
use BeansWoo\Logger\Beans_Log_Handler;

defined('ABSPATH') or die;

class Main
{

    public static function init()
    {
        add_action('wp_loaded', array(__CLASS__, 'updateLogStatus'), 10, 0);
        add_filter('woocommerce_register_log_handlers', array(__CLASS__, 'registerHandler'), 99, 1);
    }

    public static function registerHandler($handlers)
    {
        $log_status = Helper::getConfig('log_status');
        if ($log_status == 'active') {
            require_once 'Handler.php';
            array_push($handlers, new Beans_Log_Handler());
        }
        return $handlers;
    }

    public static function updateLogStatus()
    {
        if (isset($_GET['beans_log_status'])) {
            $status = htmlspecialchars($_GET['beans_log_status']);
            if (in_array($status, ['active', 'inactive'])) {
                Helper::setConfig('log_status', $status);
                Helper::log("Beans log status: Update beans to " . $status);
            }
        }
    }
}
