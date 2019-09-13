<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

use BeansWoo\Helper;

class BambooConnector extends AbstractConnector {
	const REFERRAL_PROGRAM_PAGE = 'beans_bamboo_page_id';

	static public $app_name = 'bamboo';

	static $has_install_asset = true;

	public static function init() {
	}

	public static function _installAssets() {
		// Install Referral Program Page
		if ( ! get_post( Helper::getConfig( static::$app_name . '_page' ) ) ) {
			require_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php' );
			$page_id = wc_create_page( 'referrals-program', self::REFERRAL_PROGRAM_PAGE ,
				'Referrals Program', '[beans_bamboo_page]', 0 );
			Helper::setConfig( static::$app_name . '_page', $page_id );
		}
	}
}