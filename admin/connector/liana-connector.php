<?php

namespace BeansWoo\Admin\Connector;

use BeansWoo\Helper;

class LianaConnector extends AbstractConnector {
	const REWARD_PROGRAM_PAGE = 'beans_page_id';

	static public $app_name = 'liana';

	static $config;

	static public $has_install_asset = true;

	public static function init() {
		// TODO: Implement init() method.
	}

	protected static function _installAssets() {
		// Install Reward Program Page
		if ( ! get_post( Helper::getConfig( 'page' ) ) ) {
			require_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php' );
			$page_id = wc_create_page( 'rewards-program', self::REWARD_PROGRAM_PAGE,
				'Rewards Program', '[beans_page]', 0 );
			Helper::setConfig( 'page', $page_id );
		}
	}
}