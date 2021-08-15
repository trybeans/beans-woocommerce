<?php

namespace BeansWoo\StoreFront;

use Beans\BeansError;
use BeansWoo\Helper;

class LianaObserver
{

    protected static $display;
    protected static $redemption;
    protected static $i18n_strings;
    protected const REDEEM_COUPON_UID = 'redeem_points';

    public static function init($display)
    {
        self::$display      = $display;
        self::$redemption   = $display['redemption'];
        self::$i18n_strings = self::$display['i18n_strings'];
    }

    protected static function getAccountData($key)
    {
        $account = BeansAccount::get();

        if (!$account) {
            return null;
        }

        if (isset($account['liana'][$key])) {
            return $account['liana'][$key];
        }

        if (isset($account[$key])) {
            return $account[$key];
        }

        return null;
    }

    public static function cancelRedemption()
    {
        Helper::getCart()->remove_coupon(self::REDEEM_COUPON_UID);

        unset($_SESSION['liana_coupon']);
        unset($_SESSION['liana_redemption']);
    }

    public static function getActiveRedemption()
    {
        return isset($_SESSION['liana_redemption']) ? $_SESSION['liana_redemption'] : null;
    }
}
