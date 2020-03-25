<?php

namespace BeansWoo\Admin\Connector;

use BeansWoo\Helper;

defined('ABSPATH') or die;

class UltimateConnector extends AbstractConnector {

	static public $app_name = 'ultimate';
    static public $has_install_asset = true;

	public static function init() {

	}

    protected static function _uninstallAssets()
    {
        delete_option(Helper::CONFIG_NAME);
    }

    protected static function updateInstalledApp(){
        $card = Helper::getCard(static::$app_name);

        if ($card){
            foreach ($card['apps'] as $app => $status){
                $app = strtolower($app);
                if( $status['is_installed'] ){
                    if(in_array($app, ['bamboo', 'liana'])) {
                        static::_installAssets($app);
                    }
                    Helper::setAppInstalled($app);
                }
            }
        }
    }
}