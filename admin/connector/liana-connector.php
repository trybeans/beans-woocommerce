<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

use BeansWoo\Helper;

class LianaConnector extends AbstractConnector {
	const REWARD_PROGRAM_PAGE = 'beans_page_id';

	static public $app_name = 'liana';

	static $config;

	static public $has_install_asset = true;

	public static function init() {
	}

	protected static function _installAssets() {
		// Install Reward Program Page
		if ( ! get_post( Helper::getConfig( static::$app_name . '_page' ) ) ) {
			require_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php' );
			$page_id = wc_create_page( 'rewards-program', self::REWARD_PROGRAM_PAGE,
				'Rewards Program', '[beans_page]', 0 );
			Helper::setConfig( static::$app_name . '_page', $page_id );
		}
	}

    protected static function _uninstallAssets()
    {
        delete_option('beans-liana-display-redemption-checkout');
    }
}