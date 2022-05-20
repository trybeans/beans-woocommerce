<?php

namespace BeansWoo\StoreFront;

use Beans\BeansError;
use BeansWoo\Helper;

class LianaObserver
{

    protected static $display;
    protected static $redemption;
    protected static $i18n_strings;
    const REDEEM_COUPON_UID = 'redeem_points'; // protected
    const REDEEM_TIER_COUPON_UID = 'redeem_tiers'; // protected

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
        Helper::getCart()->remove_coupon(self::REDEEM_TIER_COUPON_UID);

        unset($_SESSION['liana_coupon']);
        unset($_SESSION['liana_redemption']);

        unset($_SESSION['liana_tier_coupon']);
        unset($_SESSION['liana_tier_redemption']);
    }

    public static function getActiveRedemption()
    {
        return isset($_SESSION['liana_redemption']) ? $_SESSION['liana_redemption'] : null;
    }

    public static function commitRedemption($order_id)
    {
        $order = new \WC_Order($order_id);

        $account_id = self::getAccountData('id');

        $coupon_codes = $order->get_coupon_codes();

        foreach ($coupon_codes as $code) {
            if ($code === self::REDEEM_COUPON_UID) {
                if (!$account_id) {
                    throw new \Exception('Trying to redeem beans without beans account.');
                }

                $coupon = new \WC_Coupon($code);

                $amount     = sprintf('%0.2f', $coupon->get_amount());
                $amount_str = sprintf(
                    get_woocommerce_price_format(),
                    html_entity_decode(get_woocommerce_currency_symbol()),
                    $amount
                );

                $data = array(
                    'quantity'    => $amount,
                    'rule'        => strtoupper(get_woocommerce_currency()),
                    'account'     => $account_id,
                    'description' => "Debited for a $amount_str discount on order #" . $order->get_id(),
                    'uid'         => 'wc_' . $order->get_id() . '_' . $order->get_order_key(),
                    'commit'      => true,
                );

                try {
                    Helper::API()->post('liana/debit', $data);
                } catch (BeansError $e) {
                    if ($e->getCode() != 409) {
                        Helper::log('Debiting failed: ' . $e->getMessage());
                        throw new \Exception('Beans debit failed: ' . $e->getMessage());
                    }
                }
            }
        }

        self::cancelRedemption();
        BeansAccount::update();
    }

    public static function getTierId()
    {
        $tier_id = isset($_POST['tier_id']) && $_POST['tier_id'] ? $_POST['tier_id'] : null;
        return $tier_id;
    }
}
