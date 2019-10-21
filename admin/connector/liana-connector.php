<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

use BeansWoo\Helper;

class LianaConnector extends AbstractConnector {

	static public $app_name = 'liana';

	static public $has_install_asset = true;

	public static function init() {
	}


    protected static function _uninstallAssets()
    {
        delete_option('beans-liana-display-redemption-checkout');
    }
}