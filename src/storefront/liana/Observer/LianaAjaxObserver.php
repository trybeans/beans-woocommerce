<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaAjaxObserver extends LianaObserver
{

    public static function init($display)
    {
        add_filter('woocommerce_get_shop_coupon_data', array(__CLASS__, 'getCartCoupon'), 10, 2);
        add_filter('woocommerce_add_to_cart_fragments', array(__CLASS__, 'renderCartFragment'), 15, 1);
    }

    public static function getCartCoupon($coupon, $coupon_code)
    {
        if (
            $coupon_code === self::REDEEM_COUPON_UID
            && isset($_SESSION['liana_coupon'])
            && $_SESSION['liana_coupon']
        ) {
            return $_SESSION['liana_coupon'];
        }

        return $coupon;
    }

    public static function renderCartFragment($fragments)
    {
        $cart = Helper::getCart();
        ob_start();
        if (count($cart->get_cart()) == 0) {
            self::cancelRedemption();
        }
        LianaCart::renderCart();
        if ($fragments) {
            ?>
            <script>
                window.Beans3.Liana.Radix.init();
            </script>
            <?php
        }
        $fragments['div.beans-cart'] = ob_get_clean();
        return $fragments;
    }
}
