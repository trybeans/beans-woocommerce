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

Helper::resetSetup('liana');
Helper::resetSetup('bamboo');

delete_option(Helper::CONFIG_NAME);
delete_option(Helper::BEANS_ULTIMATE_DISMISSED);
delete_option('beans-liana-display-redemption-checkout');
try{
    delete_user_meta(get_current_user_id(), 'beans_liana_notice_dismissed');
    delete_user_meta(get_current_user_id(), 'beans_snow_notice_dismissed');
    delete_user_meta(get_current_user_id(), 'beans_poppy_notice_dismissed');
    delete_user_meta(get_current_user_id(), 'beans_bamboo_notice_dismissed');
    delete_user_meta(get_current_user_id(), 'beans_foxx_notice_dismissed');
    delete_user_meta(get_current_user_id(), 'beans_arrow_notice_dismissed');
    delete_user_meta(get_current_user_id(), 'beans_ultimate_notice_dismissed');
} catch (\Exception $e){}

Helper::postWebhookStatus('uninstalled');
