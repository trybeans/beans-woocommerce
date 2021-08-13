<?php

namespace BeansWoo\StoreFront;

use Beans\BeansError;
use BeansWoo\Helper;

class Auth
{

    public static function init()
    {
        add_action('wp_loaded', array(__CLASS__, 'loadUserData'), 99);
        add_action('wp_logout', array(__CLASS__, 'clearSession' ), 10, 1);
        add_action('wp_login', array( __CLASS__, 'customerLogin' ), 10, 2);
    }

    public static function loadUserData()
    {
        $current_user = is_user_logged_in() ? wp_get_current_user() : null;

        if (isset($current_user) and ! isset($_SESSION['liana_account'])) {
            self::customerRegister($current_user->ID);
        }

        if (! isset($current_user)) {
            self::clearSession();
        }

        $data = array(
            "beans_cjs_id"    => $current_user ? $current_user->ID : null,
            "beans_cjs_email" => $current_user ? $current_user->user_email : null,
        );

        $token = isset($_SESSION['liana_token']) ? $_SESSION['liana_token'] : [];
        $debit = isset($_SESSION['liana_debit']) ? $_SESSION['liana_debit'] : [];

        $data_liana = array(
            "accountToken" => isset($token['key']) ? $token['key'] : '',
        );
        if (isset($debit['beans'])) {
            $data_liana['debit'] = array(
                "beans"   => $debit['beans'],
                "message" => $debit['message'],
                "uid"     => BEANS_LIANA_COUPON_UID,
            );
        }
        $cart = Helper::getCart();
        if (isset($cart) and $cart->cart_contents_count != 0) {
            $data_liana['cart'] = array(
                'item_count'  => $cart->cart_contents_count,
                'total_price' => $cart->subtotal * 100,
            );
        }

        $data['liana'] = $data_liana;
        // Todo, compress data, check size and save to cookies
        //  This will fix all caching issues faced with authenticated consumers.
        // The max size of a cookie is 4096B. With the all values we can't hit that limit.
        setcookie(
            'beans_auth',
            (string)json_encode($data),
            strtotime("+2 minutes")
        );
    }

    public static function customerLogin($user_login, $user)
    {
        self::customerRegister($user->ID);
    }

    public static function customerRegister($user_id)
    {

        $user_data = get_userdata($user_id);

        $first_name = get_user_meta($user_id, 'first_name', true);
        if (! $first_name && isset($_POST['first_name'])) {
            $first_name = $_POST['first_name'];
        }
        if (! $first_name) {
            $first_name = get_user_meta($user_id, 'billing_first_name', true);
        }
        if (! $first_name) {
            $first_name = get_user_meta($user_id, 'shipping_first_name', true);
        }

        $last_name = get_user_meta($user_id, 'last_name', true);
        if (! $last_name && isset($_POST['last_name'])) {
            $last_name = $_POST['last_name'];
        }
        if (! $last_name) {
            $first_name = get_user_meta($user_id, 'billing_last_name', true);
        }
        if (! $last_name) {
            $first_name = get_user_meta($user_id, 'shipping_last_name', true);
        }

        $email = $user_data->user_email;

        if ($email) {
            $account                   = self::createBeansAccount($email, $first_name, $last_name);
            $_SESSION['liana_account'] = $account;
            if ($account) {
                try {
                    $_SESSION['liana_token'] = Helper::API()->post(
                        '/liana/auth/consumer_token',
                        array('account' => $account['id'])
                    );
                } catch (BeansError $e) {
                    Helper::log('Getting Auth Token Failed: ' . $e->getMessage());
                }
            }
        }
    }

    public static function createBeansAccount($email, $firstname, $lastname)
    {
        try {
            return Helper::API()->post('/liana/account', array(
                'email'      => $email,
                'first_name' => $firstname,
                'last_name'  => $lastname,
            ));
        } catch (BeansError $e) {
            Helper::log('Unable to create account: ' . $e->getMessage());
        }

        return null;
    }

    public static function clearSession()
    {
        unset($_SESSION['liana_token']);
        unset($_SESSION['liana_account']);
        unset($_SESSION['liana_coupon']);
        unset($_SESSION['liana_debit']);
    }
}
