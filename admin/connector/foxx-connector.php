<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

class FoxxConnector extends AbstractConnector {

	static public $app_name = 'foxx';
    static public $has_install_asset = false;

	public static function init() {
	}

	protected static function _installAssets() {
		// TODO: Implement _installAssets() method.
	}
}