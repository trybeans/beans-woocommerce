<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaLifetimeDiscountObserver extends LianaObserver
{

    protected static $tiers;

    public static function init($display)
    {
        parent::init($display);
        self::$tiers = $display['tiers'];

        if (empty(self::$tiers)) {
            return;
        }

        add_action('wp_loaded', array(__CLASS__, 'handleTierRedemption'), 30, 1);
        add_filter('woocommerce_get_shop_coupon_data', array(__CLASS__, 'getCartCoupon'), 10, 2);
    }

    public static function handleTierRedemption()
    {
        if (!isset($_POST['beans_action'])) {
            return;
        }

        $action = $_POST['beans_action'];

        if ($action == 'apply' and self::getTierId()) {
            self::applyTierRedemption();
        }
    }

    public static function applyTierRedemption()
    {
        if (!self::getAccountData('id')) {
            Helper::log("Unable to redeem: Account is available \n account=>" . print_r(BeansAccount::get(), true));
            return;
        }

        self::cancelRedemption();

        $tier_id = self::getTierId();
        $tier = null;
        foreach (self::$tiers as $value) {
            if ($value['id'] == $tier_id) {
                $tier = $value;
                break;
            }
        }

        if (is_null($tier)) {
            return;
        }

        $discount_value = $tier['lifetime_discount'];

        $amount = sprintf('%0.2f', $discount_value);

        $_SESSION['liana_tier_redemption'] = array(
            'code'  => self::REDEEM_TIER_COUPON_UID,
            'value' => $amount,
            'beans' => null,
        );

        $cart = Helper::getCart();
        $cart->apply_coupon(self::REDEEM_TIER_COUPON_UID);
    }

    public static function getActiveTierRedemption()
    {
        return isset($_SESSION['liana_tier_redemption']) ? $_SESSION['liana_tier_redemption'] : null;
    }

    public static function getCartCoupon($coupon, $coupon_code)
    {
        if (
            $coupon_code != self::REDEEM_TIER_COUPON_UID
            || !self::getActiveTierRedemption()
        ) {
            return $coupon;
        }

        // If data exists in caching then we can use it;
        if (isset($_SESSION['liana_tier_coupon']) && $_SESSION['liana_tier_coupon']) {
            return $_SESSION['liana_tier_coupon'];
        }

        $cart       = Helper::getCart();
        $redemption = self::getActiveTierRedemption();

        if (empty($cart)) {
            return $coupon;
        }
        # todo; move this part into a function
        $coupon_data = array(
            'id'                          => 0,
            'amount'                      => $redemption['value'],
            'date_created'                => strtotime('-1 hour', time()),
            'date_modified'               => time(),
            'date_expires'                => strtotime('+1 day', time()),
            'discount_type'               => 'percent',
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

        $_SESSION['liana_tier_coupon'] = $coupon_data;

        return $coupon_data;
    }
}
