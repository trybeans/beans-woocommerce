<?php

namespace BeansWoo\StoreFront;

class LianaAjaxObserver extends LianaObserver
{

    public static function init($display)
    {
        add_filter('woocommerce_get_shop_coupon_data', array(__CLASS__, 'getCartCoupon'), 10, 2);
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
}
