<?php

namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

class LotusConnector extends AbstractConnector {

	static public $app_name = 'lotus';

	static $config;

	public static function init() {
	}

	protected static function _installAssets() {
		// TODO: Implement _installAssets() method.
	}
}