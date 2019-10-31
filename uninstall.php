<?php
namespace BeansWoo;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once ('includes/beans.php');
include_once( 'includes/helper.php' );

// if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option('beans-config-3');
delete_option('beans-liana-display-redemption-checkout');

Helper::postWebhookStatus('uninstalled');