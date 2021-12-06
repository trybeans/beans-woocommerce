<?php

namespace BeansWoo\Logger;

use BeansWoo\Helper;
use BeansWoo\Logger\Beans_Log_Handler;

defined('ABSPATH') or die;

class Main
{

    public static function init()
    {
        add_filter('woocommerce_register_log_handlers', array(__CLASS__, 'registerHandler'), 10, 1);
    }

    public static function registerHandler($handlers)
    {
        require_once 'Handler.php';
        self::updateHandlerStatus();

        $log_status = Helper::getConfig('log_status');
        if ($log_status == 'active') {
            array_push($handlers, new Beans_Log_Handler());
        }
        return $handlers;
    }

    public static function updateHandlerStatus()
    {
        if (isset($_GET['beans_handler_status'])) {
            $status = htmlspecialchars($_GET['beans_handler_status']);
            if (in_array($status, ['active', 'inactive'])) {
                Helper::setConfig('log_status', $status);
            }
        }
    }
}
