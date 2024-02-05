<?php

namespace BeansWoo;

defined('ABSPATH') or die;

// if uninstall not called from WordPress exit
defined('WP_UNINSTALL_PLUGIN') or die;

require_once 'beans-woocommerce.php';

use BeansWoo\Server;

Helper::resetSetup();

foreach (Helper::OPTIONS as $key => $params) {
    delete_option($params['handle']);
}

try {
    delete_user_meta(get_current_user_id(), 'beans_ultimate_notice_dismissed');
} catch (\Exception $e) {
}

Server\SystemHookController::postWebhookStatus('uninstalled');
