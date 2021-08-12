<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class Scripts
{

    public static function init()
    {

    }

    public static function loadUserData()
    {
        $current_user = is_user_logged_in() ? wp_get_current_user() : '';
        $data         = array(
            "beans_cjs_id"    => $current_user && $current_user->ID,
            "beans_cjs_email" => $current_user && $current_user->user_email,
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
        if ($cart->cart_contents_count != 0) {
            $data_liana['cart'] = array(
                'item_count'  => $cart->cart_contents_count,
                'total_price' => $cart->subtotal * 100,
            );
        }

        $data['liana'] = $data_liana;

        // Todo, compress data, check size and save to cookies
        // This will fix all caching issues faced with authenticated consumers.

    }

}
