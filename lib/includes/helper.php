<?php

namespace BeansWoo;

use Beans\Beans;

class Helper {
    const CONFIG_NAME = 'beans-woo-config';

    public static $card = null;
    public static $key = null;

    public static function getDomain( $sub ) {
        $key = "BEANS_DOMAIN_$sub";
        $domains = array(
            'API' => 'api-3.trybeans.com',
            'CONNECT' => 'connect.trybeans.com',
            'WWW' => 'www.trybeans.com',
        );
        $val = getenv($key);
        return  empty($val) ? $domains[$sub] : getenv( $key );
    }

    public static function API() {
        self::setKey();
        $beans           = new Beans(self::$key);
        $beans->endpoint = 'https://'.self::getDomain('API').'/v3/';
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

    public static function isConfigured() {
        return Helper::getConfig( 'key' ) &&
               Helper::getConfig( 'card' ) &&
               Helper::getConfig( 'secret' );
    }

    public static function setConfig( $key, $value ) {
        $config         = get_option( self::CONFIG_NAME );
        $config[ $key ] = $value;
        update_option( self::CONFIG_NAME, $config );
    }

    private static function setKey() {
        if ( ! self::$key ) {
            self::$key = self::getConfig( 'secret' );
        }
    }

    public static function resetSetup() {
        self::setConfig( 'key', null );
        self::setConfig( 'card', null );
        self::setConfig( 'secret', null );
        self::$card = null;

        return true;
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

    public static function getCard() {
        if ( ! self::$card && self::isInstalled() ) {
            try {
                self::$card = self::API()->get( 'card/current' );
            } catch ( \Beans\Error\BaseError $e ) {
                if ( $e->getCode() < 400 ) {
                    self::resetSetup();
                }
                self::log( 'Unable to get card: ' . $e->getMessage() );
            }
        }

//        if(self::$card && self::getConfig('synced') != date('Y-m-d').BEANS_VERSION){
//            if(self::synchronise()){
//                self::setConfig('synced', date('Y-m-d').BEANS_VERSION);
//            }
//        }

        return self::$card;
    }

    public static function isInstalled() {
        self::setKey();

        return (bool) self::$key;
    }

    public static function isActive() {
        if ( ! self::getCard() ) {
            return false;
        }

        return self::$card['is_active'];
    }

    public static function updateSession() {
        $account = null;
        if ( ! empty( $_SESSION['beans_account'] ) ) {
            $account = $_SESSION['beans_account'];
        }
        if ( ! $account ) {
            return;
        }
        try {
            $account                   = self::API()->get( 'account/' . $account['id'] );
            $_SESSION['beans_account'] = $account;
        } catch ( \Beans\Error\BaseError $e ) {
            self::log( 'Unable to get account: ' . $e->getMessage() );
        }
    }

    public static function getCart() {
        global $woocommerce;

        if ( ! empty( $woocommerce->cart ) && empty( $woocommerce->cart->cart_contents ) ) {
            $woocommerce->cart->calculate_totals();
        }

        return $woocommerce->cart;
    }

}
