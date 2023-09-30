<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class Auth
{
    const COOKIE_KEY = 'beans_cjs'; // public
    protected static $display_liana;
    protected static $display_bamboo;

    public static function init($display_liana, $display_bamboo)
    {
        self::$display_liana = $display_liana;
        self::$display_bamboo = $display_bamboo;

        add_action('wp_loaded', array(__CLASS__, 'saveBeansAccountToCookies'), 99);
        add_action('wp_logout', array(__CLASS__, 'onCustomerLogout'), 10, 1);
        add_action('wp_login', array(__CLASS__, 'onCustomerLogin'), 10, 2);
        add_action('user_register', array(__CLASS__, 'onCustomerRegister'), 10, 1);
    }

    public static function onCustomerRegister($user_id)
    {
        // Either the rewards program is active, or we are in live test mode
        if (!self::$display_liana['is_active'] && !isset($_SESSION['beans_mode'])) {
            Helper::log('Auth: Display is deactivated');
            return;
        }

        $user_data = get_userdata($user_id);

        $first_name = get_user_meta($user_id, 'first_name', true);
        if (!$first_name && isset($_POST['first_name'])) {
            $first_name = $_POST['first_name'];
        }
        if (!$first_name) {
            $first_name = get_user_meta($user_id, 'billing_first_name', true);
        }
        if (!$first_name) {
            $first_name = get_user_meta($user_id, 'shipping_first_name', true);
        }

        $last_name = get_user_meta($user_id, 'last_name', true);
        if (!$last_name && isset($_POST['last_name'])) {
            $last_name = $_POST['last_name'];
        }
        if (!$last_name) {
            $first_name = get_user_meta($user_id, 'billing_last_name', true);
        }
        if (!$last_name) {
            $first_name = get_user_meta($user_id, 'shipping_last_name', true);
        }

        $email = $user_data->user_email;

        if ($email) {
            BeansAccount::create($email, $first_name, $last_name, true);
        }
    }

    public static function onCustomerLogin($user_login, $user)
    {
        self::onCustomerRegister($user->ID);
    }

    public static function forceCustomerAuthentication()
    {
        if (BeansAccount::getSession()) {
            return;
        }

        if (!isset($_SESSION['beans_account_force']) and is_user_logged_in()) {
            $_SESSION['beans_account_force'] = 1;
            self::onCustomerRegister(get_current_user_id());
        }
    }

    public static function onCustomerLogout()
    {
        BeansAccount::clearSession();
    }

    public static function saveBeansAccountToCookies()
    {
        $account      = BeansAccount::getSession();
        $current_user = is_user_logged_in() ? wp_get_current_user() : null;

        if (!isset($current_user) and $account) {
            BeansAccount::clearSession();
            $account = BeansAccount::getSession();
        }

        if (isset($current_user) and !$account) {
            self::onCustomerRegister($current_user->ID);
            $account = BeansAccount::getSession();
        }

        $cart       = Helper::getCart();
        $cart       = isset($cart) ? $cart : null;
        $token      = BeansAccount::getSessionToken();
        $redemption = LianaObserver::getActiveRedemption(LianaObserver::REDEEM_COUPON_CODE);
        if (empty($redemption)) {
            $redemption = LianaObserver::getActiveRedemption(LianaObserver::REDEEM_LIFETIME_CODE);
        }

        $data = array(
            "user"    => array(
                "id"    => $current_user ? $current_user->ID : null,
                "email" => $current_user ? $current_user->user_email : null,
            ),
            "account" => array(
                "id"    => $account ? $account['id'] : null,
                "token" => $token ? $token['key'] : null,
            ),
            "redeem"  => array(
                "code"    => $redemption ? $redemption['code'] : null,
                "beans"   => $redemption ? $redemption['beans'] : null,
                "message" => null,
            ),
            "cart"    => array(
                "item_count"  => $cart ? $cart->cart_contents_count : null,
                "total_price" => $cart ? $cart->subtotal * 100 : null,
            ),
        );

        setcookie(
            self::COOKIE_KEY,
            (string)json_encode($data),
            strtotime("+15 minutes"),
            "/"
        );
    }
}
