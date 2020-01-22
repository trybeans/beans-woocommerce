<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

class LotusConnector extends AbstractConnector {

	static public $app_name = 'lotus';
    static public $has_install_asset = false;

	public static function init() {
	}

    protected static function _uninstallAssets()
    {
        // TODO: Implement _uninstallAssets() method.
    }
}