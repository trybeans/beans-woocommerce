<?php

namespace BeansWoo\Front\Liana;

defined('ABSPATH') or die;

use BeansWoo\Helper;

class Observer {
    public static $display;
    public static $redemption;
    public static $i18n_strings;

    public static function init($display) {

        self::$display = $display;
        self::$redemption = $display['redemption'];
        self::$i18n_strings = self::$display['i18n_strings'];

        add_action( 'wp_logout', array( __CLASS__, 'clear_session' ), 10, 1 );
        add_action( 'wp_login', array( __CLASS__, 'customer_login' ), 10, 2 );

        add_action( 'wp_loaded', array( __CLASS__, 'handle_redemption_form' ), 30, 1 );

        add_filter( 'woocommerce_get_shop_coupon_data', array( __CLASS__, 'get_coupon' ), 10, 2 );

        add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'order_placed' ), 10, 1 );
    }

    public static function clear_session() {
        unset( $_SESSION['liana_token'] );
        unset( $_SESSION['liana_account'] );
        unset( $_SESSION['liana_coupon'] );
        unset( $_SESSION['liana_debit'] );
    }

    public static function create_beans_account($email, $firstname, $lastname ) {
        try {
            return Helper::API()->post( '/liana/account', array(
                'email'      => $email,
                'first_name' => $firstname,
                'last_name'  => $lastname,
            ) );
        } catch ( \Beans\Error\BaseError $e ) {
            Helper::log( 'Unable to create account: ' . $e->getMessage() );
        }

        return null;
    }

    public static function customer_login($user_login, $user ) {
        self::customer_register( $user->ID );
    }

    public static function customer_register($user_id ) {

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
            $account                   = self::create_beans_account( $email, $first_name, $last_name );
            $_SESSION['liana_account'] = $account;
            if ( $account ) {
                try{
                    $_SESSION['liana_token'] = Helper::API()->post( '/liana/auth/consumer_token', array('account' => $account['id']) );
                }catch ( \Beans\Error\BaseError $e ) {
                    Helper::log( 'Getting Auth Token Failed: ' . $e->getMessage() );
                }
            }
        }
    }

    public static function handle_redemption_form() {

        if ( ! isset( $_POST['beans_action'] ) ) {
            return;
        }

        $action = $_POST['beans_action'];

        if ( $action == 'apply' ) {
            self::apply_redemption();
        } else {
            self::cancel_redemption();
        }
    }

    public static function get_coupon($coupon, $coupon_code ) {

        if ( $coupon_code != BEANS_LIANA_COUPON_UID ) {
            return $coupon;
        }
        if ( ! isset( $_SESSION['liana_account'] ) ||
             ! isset( $_SESSION['liana_account']['beans'] )
        ) {
            return $coupon;
        }
        if ( ! isset( $_SESSION['liana_debit'] ) ||
             ! isset( $_SESSION['liana_debit']['beans'] )
        ) {
            return $coupon;
        }

        if ( isset( $_SESSION['liana_coupon'] ) &&
             $_SESSION['liana_coupon']
        ) {
            return $_SESSION['liana_coupon'];
        }

        $cart = Helper::getCart();
        if ( empty( $cart ) ) {
            return $coupon;
        }

        $coupon_data = array(
            'id'                          => 0,
            'amount'                      => $_SESSION['liana_debit']['value'],
            'date_created'                => strtotime( '-1 hour', time() ),
            'date_modified'               => time(),
            'date_expires'                =>  strtotime( '+1 day', time() ),
            'discount_type'               => 'fixed_cart',
            'description'                 => '',
            'usage_count'                 => 0,
            'individual_use'              => false,
            'product_ids'                 => array(),
            'excluded_product_ids'        => array(),
            'usage_limit'                 => 1,
            'usage_limit_per_user'        => 1,
            'limit_usage_to_x_items'      => null,
            'free_shipping'               => false,
            'product_categories'          => array(),
            'excluded_product_categories' => array(),
            'exclude_sale_items'          => false,
            'minimum_amount'              => '',
            'maximum_amount'              => '',
            'email_restrictions'          => array(),
            'used_by'                     => array(),
            'virtual'                     => true,
        );

        $_SESSION['liana_coupon'] = $coupon_data;

        return $coupon_data;
    }

    public static function apply_redemption() {

        if ( ! isset( $_SESSION['liana_account'] ) ) {
            return;
        }

        self::cancel_redemption();
        self::update_session();

        $account = $_SESSION['liana_account'];

        $cart = Helper::getCart();

        $max_amount = $cart->subtotal;
        if ( isset( self::$redemption ) && isset( self::$redemption['min_beans'] ) &&
            isset( self::$redemption['max_percentage']) ) {
            $min_beans =  self::$redemption['min_beans'];
            if ($account['beans']  < $min_beans){
                wc_add_notice( Helper::replaceTags(
                    self::$i18n_strings['redemption']['condition_minimum_points'],
                    array(
                        'quantity' => $min_beans,
                        "beans_name" => self::$display['beans_name'],
                    )) , 'notice' );
                return;
            }

            $percent_discount =  self::$redemption['max_percentage'];
            if ( $percent_discount < 100) {
                $max_amount = ( 1.0 * $cart->subtotal * $percent_discount ) / 100;
                if($max_amount < $account['beans_value']){
                    wc_add_notice( Helper::replaceTags(
                        self::$i18n_strings['redemption']['condition_maximum_discount'],
                        array(
                            'max_discount' => $percent_discount,
                        )), 'notice' );
                }
            }
        }

        $amount = min( $max_amount, $account['beans_value'] );
        $amount = sprintf( '%0.2f', $amount );

        $_SESSION['liana_debit'] = array(
            'beans' => $amount * self::$display['beans_rate'],
            'value' => $amount
        );
        $cart->apply_coupon( BEANS_LIANA_COUPON_UID );
    }

    public static function cancel_redemption() {

        Helper::getCart()->remove_coupon( BEANS_LIANA_COUPON_UID );

        unset( $_SESSION['liana_coupon'] );
        unset( $_SESSION['liana_debit'] );
    }

    public static function order_placed($order_id ) {
        $order = new \WC_Order( $order_id );

        $account = null;

        if ( isset( $_SESSION['liana_account'] ) ) {
            $account = $_SESSION['liana_account'];
        }

        if (! in_array(BEANS_LIANA_COUPON_UID, $order->get_coupon_codes())){
            return ;
        }

        if ( ! $account ) {
            wc_add_notice('Trying to redeem beans without beans account.', 'error');
            return ;
        }

        $coupon = new \WC_Coupon( BEANS_LIANA_COUPON_UID );
        $amount     = (double) $coupon->get_amount();
        $amount     = sprintf( '%0.2f', $amount );
        $amount_str =  sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $amount );

        $data = array(
            'quantity'    => $amount,
            'rule'        => strtoupper( get_woocommerce_currency() ),
            'account'     => $account['id'],
            'description' => "Debited for a $amount_str discount",
            'uid'         => 'wc_' . $order->get_id() . '_' . $order->get_order_key(),
            'commit'      => true
        );

        try {
            $debit = Helper::API()->post( '/liana/debit', $data );
        } catch ( \Beans\Error\BaseError $e ) {
            if ( $e->getCode() != 409 ) {
                Helper::log( 'Debiting failed: ' . $e->getMessage() );
                wc_add_notice('Beans debit failed: ' . $e->getMessage(), 'error');
                return ;
            }
        }
        self::cancel_redemption();
        self::update_session();
    }

    public static function update_session() {
        $account = null;
        if ( ! empty( $_SESSION['liana_account'] ) ) {
            $account = $_SESSION['liana_account'];
        }
        if ( ! $account ) {
            return;
        }
        try {
            $_SESSION['liana_account'] = Helper::API()->get( 'liana/account/' . $account['id'] );
        } catch ( \Beans\Error\BaseError $e ) {
            Helper::log( 'Unable to get account: ' . $e->getMessage() );
        }
    }

}
