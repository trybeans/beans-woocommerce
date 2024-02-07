<?php

use Beans\Beans;

defined('ABSPATH') or die;

defined('WP_UNINSTALL_PLUGIN') or die;  // if uninstall not called from WordPress exit

require_once 'beans-woocommerce.php';

BeansWoo\Helper::resetSetup();
BeansWoo\Preferences::clearAll();

try {
    delete_user_meta(get_current_user_id(), 'beans_ultimate_notice_dismissed');
} catch (\Exception $e) {
}

BeansWoo\Server\ConnectorRESTController::postWebhook('uninstalled');
