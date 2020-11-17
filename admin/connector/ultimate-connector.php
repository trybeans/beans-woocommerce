<?php

namespace BeansWoo\Admin\Connector;

use BeansWoo\Helper;

defined('ABSPATH') or die;

class UltimateConnector extends AbstractConnector {

	static public $app_name = 'ultimate';
    static public $has_install_asset = false;

	public static function init() {
        add_action('admin_init', array(__CLASS__, 'installDefaultAssets'));
	}

    protected static function _uninstallAssets()
    {
        $card = Helper::getCard(static::$app_name);
        if ($card){
            foreach ($card['apps'] as $app => $status){
                $app = strtolower($app);
                if( $status['is_installed'] ){
                    if ( class_exists(__NAMESPACE__. '\\'.ucfirst($app). 'Connector')
                        && Helper::isSetupApp($app)){
                        Helper::resetSetup($app);
                        call_user_func([__NAMESPACE__. '\\'.ucfirst($app). 'Connector', '_uninstallAssets']);
                    }
                }
            }
        }
        delete_option(Helper::CONFIG_NAME);
    }

    protected static function updateInstalledApp(){
        $card = Helper::getCard(static::$app_name);

        if ($card){
            foreach ($card['apps'] as $app => $status){
                $app = strtolower($app);
                if( $status['is_installed'] ){
                    if ( class_exists(__NAMESPACE__. '\\'.ucfirst($app). 'Connector')
                        && ! Helper::isSetupApp($app)){
                        call_user_func([__NAMESPACE__. '\\'.ucfirst($app). 'Connector', '_installAssets'], $app);
                        Helper::setAppInstalled($app);
                    }
                }
            }
        }
    }
}