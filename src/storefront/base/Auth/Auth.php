<?php

namespace BeansWoo\StoreFront;

use Beans\BeansError;
use BeansWoo\Helper;

class Auth
{
    public const COOKIE_KEY = 'beans_cjs';

    public static function init()
    {
        add_action('wp_loaded', array(__CLASS__, 'saveBeansAccountToCookies'), 99);
        add_action('wp_logout', array(__CLASS__, 'onCustomerLogout'), 10, 1);
        add_action('wp_login', array(__CLASS__, 'onCustomerLogin'), 10, 2);
        add_filter('user_register', array(__CLASS__, 'onCustomerRegister'), 10, 1);
    }

    private static function onCustomerRegister($user_id)
    {
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
            BeansAccount::create($email, $first_name, $last_name);
        }
    }

    public static function onCustomerLogin($user_login, $user)
    {
        self::onCustomerRegister($user->ID);
    }

    public static function onCustomerLogout()
    {
        BeansAccount::clear();
    }

    public static function saveBeansAccountToCookies()
    {
        $account      = BeansAccount::get();
        $current_user = is_user_logged_in() ? wp_get_current_user() : null;

        if (!isset($current_user) and $account) {
            BeansAccount::clear();
            $account = BeansAccount::get();
        }

        if (isset($current_user) and !$account) {
            self::onCustomerRegister($current_user->ID);
            $account = BeansAccount::get();
        }

        $cart       = Helper::getCart();
        $cart       = isset($cart) ? $cart : null;
        $token      = BeansAccount::getToken();
        $redemption = LianaObserver::getActiveRedemption();

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
                "message" => $redemption ? $redemption['message'] : null,
            ),
            "cart"    => array(
                "item_count"  => $cart ? $cart->cart_contents_count : null,
                "total_price" => $cart ? $cart->subtotal * 100 : null,
            ),
        );

        // Todo, compress data, check size and save to cookies
        //  This will fix all caching issues faced with authenticated consumers.
        // The max size of a cookie is 4096B. With the all values we can't hit that limit.
        setcookie(
            self::COOKIE_KEY,
            (string)json_encode($data),
            strtotime("+15 minutes")
        );
    }
}
