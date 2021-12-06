<?php

namespace BeansWoo\Logger;

use WC_Log_Handler;
use BeansWoo\Helper;

defined('ABSPATH') or die;

class Beans_Log_Handler extends WC_Log_Handler
{
    public function handle($timestamp, $level, $message, $context)
    {
        $formated_log = self::format_entry($timestamp, $level, $message, $context);
        $log = "$formated_log \ncontext => " . print_r($context, 1);
        Helper::log($log, true);
        return true;
    }
}
