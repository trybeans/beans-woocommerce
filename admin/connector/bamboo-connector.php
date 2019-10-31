<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

//use BeansWoo\Helper;

class BambooConnector extends AbstractConnector {

	static public $app_name = 'bamboo';

	static public $has_install_asset = true;

	public static function init() {
	}


    protected static function _uninstallAssets()
    {
//        delete_option('beans-liana-display-redemption-checkout');
    }
}