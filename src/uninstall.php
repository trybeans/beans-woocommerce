<?php

namespace BeansWoo;

defined('ABSPATH') or die;

// if uninstall not called from WordPress exit
defined('WP_UNINSTALL_PLUGIN') or die;

require_once 'includes/Beans.php';
require_once 'includes/Helper.php';
require_once 'server/Hooks.php';

use BeansWoo\Server;

Helper::resetSetup();
delete_option('beans-liana-display-redemption-checkout');

try {
    delete_user_meta(get_current_user_id(), 'beans_ultimate_notice_dismissed');
} catch (\Exception $e) {
}

Server\Hooks::postWebhookStatus('uninstalled');
