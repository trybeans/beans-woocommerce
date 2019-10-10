<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

class SnowConnector extends AbstractConnector {

	static public $app_name = 'snow';
    static public $has_install_asset = false;

	public static function init() {
	}

	protected static function _installAssets() {
		// TODO: Implement _installAssets() method.
	}

    protected static function _uninstallAssets()
    {
        // TODO: Implement _uninstallAssets() method.
    }
}