<?php

namespace BeansWoo;

use Beans\Beans;

class Helper {
    const CONFIG_NAME = 'beans-config-3';

    const BASE_LINK = 'admin.php?page=';

    private static $cards = array();
    public static $key = null;

    public static function getDomain( $sub ) {
        $key     = "BEANS_DOMAIN_$sub";
        $domains = array(
            'API'     => 'api-3.trybeans.com',
            'CONNECT' => 'connect.trybeans.com',
            'WWW'     => 'www.trybeans.com',
            'STATIC' => 'trybeans.s3.amazonaws.com'
        );
        $val     = getenv( $key );

        return empty( $val ) ? $domains[ $sub ] : getenv( $key );
    }

    public static function getApps() {
        return array(
            'liana' => array(
                'name' => 'Liana',
                'title' => 'Make your customers addicted to your shop',
                'description' =>'Get your customers to place a second order, a third, a forth and more.',
	            'link' => self::BASE_LINK . BEANS_WOO_BASE_MENU_SLUG,
            ),
            'snow' => array(
            	'name' => 'Snow',
	            'title' => 'Communicate with customers without disrupting their journey',
	            'description' => 'Automatically let customers know about new products and promotions in your shop.',
	            'link' => self::BASE_LINK . BEANS_WOO_BASE_MENU_SLUG . '-snow',
            ),

            /**'bamboo' => array(
	        	'name' => 'Bamboo',
		        'title' => 'Turn your customers into advocates of your brand',
		        'description' => 'Let your customers grow your business by referring you to their friends.',
		        'link' => self::BASE_LINK . BEANS_WOO_BASE_MENU_SLUG . '-bamboo',
	        ),
	        'lotus' => array(
	        	'name' => 'Lotus',
		        'title' => 'Save time managing social media for your shop',
		        'description' => 'Automatically let customers know about new products and promotions in your shop.',
		        'link' => self::BASE_LINK . BEANS_WOO_BASE_MENU_SLUG . '-lotus',
	        ) **/

        );
    }

    public static function API() {
        if ( ! self::$key ) {
            self::$key = self::getConfig( 'secret' );
        }
        $beans           = new Beans( self::$key );
        $beans->endpoint = 'https://' . self::getDomain( 'API' ) . '/v3/';

        return $beans;
    }

    public static function getAccountData( $account, $k, $default = null ) {
        if ( isset( $account[ $k ] ) ) {
            echo "$k:'" . $account[ $k ] . "',";
        } else if ( $default !== null ) {
            echo "$k: '',";
        }
    }

    public static function getConfig( $key ) {
        $config = get_option( self::CONFIG_NAME );
        if ( isset( $config[ $key ] ) ) {
            return $config[ $key ];
        }

        return null;
    }

    public static function setConfig( $key, $value ) {
        $config         = get_option( self::CONFIG_NAME );
        $config[ $key ] = $value;
        update_option( self::CONFIG_NAME, $config );
    }

    public static function isSetup() {
        return Helper::getConfig( 'key' ) &&
               Helper::getConfig( 'card' ) &&
               Helper::getConfig( 'secret' );
    }

    public static function resetSetup($app_name) {
    	$apps_installed = self::getConfig('apps');

    	if( in_array($app_name, $apps_installed) ){
    		unset($apps_installed[ $app_name ]);
            $app_page = self::getConfig($app_name . '_page');
            if (!is_null($app_page)){
				wp_delete_post($app_page, true);
				self::setConfig($app_name . '_page', null);
			}
            self::setConfig('apps', $apps_installed);
    	}

    	if (empty($apps_installed)){
			self::setConfig( 'key', null );
			self::setConfig( 'card', null );
			self::setConfig( 'secret', null );
			self::setConfig('apps', null);
			self::$cards = array();
		}

        return true;
    }

    public static function isSetupApp( $app_name){
    	return in_array($app_name, self::getConfig('apps'));
    }

    public static function log( $info ) {
        if ( file_exists( BEANS_INFO_LOG ) && filesize( BEANS_INFO_LOG ) > 100000 ) {
            unlink( BEANS_INFO_LOG );
        }

        if ( ! is_writable( BEANS_INFO_LOG ) ) {
            return false;
        }

        $log = date( 'Y-m-d H:i:s.uP' ) . " => " . $info . PHP_EOL;

        try {
            file_put_contents( BEANS_INFO_LOG, $log, FILE_APPEND );
        } catch ( \Exception $e ) {
            return false;
        }

        return true;
    }

    public static function getCard($app_name) {
        if ( ! isset(self::$cards[$app_name]) && self::isSetup() && self::isSetupApp($app_name)) {
            try {
                self::$cards[$app_name] = self::API()->get( "${app_name}/card/current" );
            } catch ( \Beans\Error\BaseError $e ) {
                self::log( 'Unable to get card: ' . $e->getMessage() );
            }
        }

        return isset(self::$cards[$app_name]) ? self::$cards[$app_name] : null;
    }

    public static function getCart() {
        global $woocommerce;

        if ( ! empty( $woocommerce->cart ) && empty( $woocommerce->cart->cart_contents ) ) {
            $woocommerce->cart->calculate_totals();
        }

        return $woocommerce->cart;
    }

	public static function setAppInstalled($app_name){
		$config         = get_option( self::CONFIG_NAME );
		if (isset($config['apps'])){
			if( !in_array($app_name, $config['apps']) ){
				$config['apps'][ $app_name] =  $app_name;
			}
		}else{
			$config['apps'] = array($app_name => $app_name, );
		}
		update_option( self::CONFIG_NAME, $config );
	}
}
