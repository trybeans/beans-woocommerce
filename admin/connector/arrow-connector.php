<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

class ArrowConnector extends AbstractConnector {

	static public $app_name = 'arrow';
    static public $has_install_asset = false;

	public static function init() {
	}

    protected static function _uninstallAssets()
    {
        // TODO: Implement _uninstallAssets() method.
    }
}