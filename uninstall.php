<?php
namespace BeansWoo;

include_once( 'includes/beans.php' );
include_once( 'includes/helper.php' );

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option('beans-config-3');
delete_option('beans-liana-display-redemption-checkout');

$args = array(
    'status' => 'uninstalled',
    'shop_url' => home_url(),
);

$api = Helper::API(1);

$api->post('/radix/woocommerce/hook/shop/plugin_status', $args);
