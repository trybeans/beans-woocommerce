<?php

namespace BeansWoo\Logger;

use BeansWoo\Helper;
use BeansWoo\Logger\Beans_Log_Handler;

defined('ABSPATH') or die;

class Main
{

    public static function init()
    {
        add_filter('woocommerce_register_log_handlers', array(__CLASS__, 'registerHandler'), 99, 1);
    }

    public static function registerHandler($handlers)
    {
        self::updateLogStatus();

        $log_status = Helper::getConfig('log_status');
        if ($log_status) {
            require_once 'Handler.php';
            array_push($handlers, new Beans_Log_Handler());
        }
        return $handlers;
    }

    public static function updateLogStatus()
    {
        if (isset($_GET['beans_handler_status'])) {
            $status = htmlspecialchars($_GET['beans_handler_status']);
            if (in_array($status, ['active', 'inactive'])) {
                Helper::setConfig('log_status', $status == 'active');
            }
        }
    }
}
