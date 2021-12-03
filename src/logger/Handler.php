<?php

namespace BeansWoo\Logger;

use BeansWoo\Helper;

use \WC_Log_Handler;

class Beans_Log_Handler extends WC_Log_Handler
{
    public function handle($timestamp, $level, $message, $context)
    {
        if (!$this->isActive()) return;

        $formated_log = self::format_entry($timestamp, $level, $message, $context);
        if (is_array($formated_log)) {
            $log = print_r($formated_log) . " \ncontext => " . print_r($context, 1);
        } else {
            $log = "$formated_log \ncontext => " . print_r($context, 1);
        }
        Helper::log(($log));
    }

    private function isActive()
    {
        $status = get_option('beans_handler_status', 'deactivate');
        return $status == 'active';
    }
}
