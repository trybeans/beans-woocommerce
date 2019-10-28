<?php

namespace BeansWoo\Front\Bamboo;

use BeansWoo\Helper;

class Observer {
    public static $card;
    public static function init() {

        self::$card = Helper::getCard( 'bamboo' );
        if ( empty( self::$card ) || ! self::$card['is_active'] || !Helper::isSetupApp('bamboo')) {
            return;
        }

        add_filter( 'wp_logout', array( __CLASS__, 'clearSession' ), 10, 1 );
        add_filter( 'wp_login', array( __CLASS__, 'customerLogin' ), 10, 2 );
        add_filter( 'user_register', array( __CLASS__, 'customerRegister' ), 10, 1 );
        add_filter( 'profile_update', array( __CLASS__, 'customerRegister' ), 10, 1 );
    }

    public static function clearSession() {
        unset( $_SESSION['bamboo_token'] );
        unset( $_SESSION['bamboo_account'] );
    }

    public static function createBeansAccount( $email, $firstname, $lastname ) {
        try {
            return Helper::API()->post( '/bamboo/account', array(
                'email'      => $email,
                'first_name' => $firstname,
                'last_name'  => $lastname,
            ) );
        } catch ( \Beans\Error\BaseError $e ) {
            Helper::log( 'Unable to create account: ' . $e->getMessage() );
        }

        return null;
    }

    public static function customerLogin( $user_login, $user ) {
        self::customerRegister( $user->ID );
    }

    public static function customerRegister( $user_id ) {

        $user_data = get_userdata( $user_id );

        $first_name = get_user_meta( $user_id, 'first_name', true );
        if ( ! $first_name && isset( $_POST['first_name'] ) ) {
            $first_name = $_POST['first_name'];
        }
        if ( ! $first_name ) {
            $first_name = get_user_meta( $user_id, 'billing_first_name', true );
        }
        if ( ! $first_name ) {
            $first_name = get_user_meta( $user_id, 'shipping_first_name', true );
        }

        $last_name = get_user_meta( $user_id, 'last_name', true );
        if ( ! $last_name && isset( $_POST['last_name'] ) ) {
            $last_name = $_POST['last_name'];
        }
        if ( ! $last_name ) {
            $first_name = get_user_meta( $user_id, 'billing_last_name', true );
        }
        if ( ! $last_name ) {
            $first_name = get_user_meta( $user_id, 'shipping_last_name', true );
        }

        $email = $user_data->user_email;

        if ( $email ) {
            $account                   = self::createBeansAccount( $email, $first_name, $last_name );
            $_SESSION['bamboo_account'] = $account;
            if ( $account ) {
                try{
                    $_SESSION['bamboo_token'] = Helper::API()->post( '/bamboo/auth/consumer_token', array('account' => $account['id']) );
                }catch ( \Beans\Error\BaseError $e ) {
                    Helper::log( 'Getting Auth Token Failed: ' . $e->getMessage() );
                }
            }
        }
    }

    public static function updateSession() {
        $account = null;
        if ( ! empty( $_SESSION['bamboo_account'] ) ) {
            $account = $_SESSION['bamboo_account'];
        }
        if ( ! $account ) {
            return;
        }
        try {
            $_SESSION['bamboo_account'] = Helper::API()->get( 'bamboo/account/' . $account['id'] );
        } catch ( \Beans\Error\BaseError $e ) {
            Helper::log( 'Unable to get account: ' . $e->getMessage() );
        }
    }

}
