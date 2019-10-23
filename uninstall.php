<?php
namespace BeansWoo;


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

WC_Beans::send_status($args);
