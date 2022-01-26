<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaCartObserver extends LianaObserver
{

    public static function init($display)
    {
        parent::init($display);

        add_action('wp_loaded', array(__CLASS__, 'handleCartRedemption'), 30, 1);
        add_filter('woocommerce_get_shop_coupon_data', array(__CLASS__, 'getCartCoupon'), 10, 2);
        add_action('woocommerce_checkout_order_processed', array(__CLASS__, 'commitRedemption'), 10, 1);
    }

    public static function handleCartRedemption()
    {
        if (!isset($_POST['beans_action'])) {
            return;
        }

        $action = $_POST['beans_action'];

        if ($action == 'apply') {
            if (!self::getTierId()) {
                self::applyCartRedemption();
            }
        } else {
            self::cancelRedemption();
        }
    }

    public static function applyCartRedemption()
    {
        if (!self::getAccountData('id')) {
            Helper::log("Unable to redeem: Account is available \n account=>" . print_r(BeansAccount::get(), true));
            return;
        }

        self::cancelRedemption();

        BeansAccount::update();
        $account_beans       = self::getAccountData('beans');
        $account_beans_value = self::getAccountData('beans_value');

        $cart = Helper::getCart();

        $max_amount = $cart->subtotal;
        if (
            isset(self::$redemption) && isset(self::$redemption['min_beans'])
            && isset(self::$redemption['max_percentage'])
        ) {
            $min_beans = self::$redemption['min_beans'];
            if ($account_beans < $min_beans) {
                wc_add_notice(Helper::replaceTags(
                    self::$i18n_strings['redemption']['condition_minimum_points'],
                    array(
                        'quantity'   => $min_beans,
                        "beans_name" => self::$display['beans_name'],
                    )
                ), 'notice');

                return;
            }

            $percent_discount = self::$redemption['max_percentage'];
            if ($percent_discount < 100) {
                $max_amount = (1.0 * $cart->subtotal * $percent_discount) / 100;
                if ($max_amount < $account_beans_value) {
                    wc_add_notice(Helper::replaceTags(
                        self::$i18n_strings['redemption']['condition_maximum_discount'],
                        array(
                            'max_discount' => $percent_discount,
                        )
                    ), 'notice');
                }
            }
        }

        $amount = min($max_amount, $account_beans_value);
        $amount = sprintf('%0.2f', $amount);

        $_SESSION['liana_redemption'] = array(
            'code'  => self::REDEEM_COUPON_UID,
            'value' => $amount,
            'beans' => $amount * self::$display['beans_rate'],
        );
        $cart->apply_coupon(self::REDEEM_COUPON_UID);
    }

    public static function getCartCoupon($coupon, $coupon_code)
    {
        if (
            $coupon_code != self::REDEEM_COUPON_UID
            || !self::getAccountData('beans')
            || !self::getActiveRedemption()
        ) {
            return $coupon;
        }

        // If data exists in caching then we can use it;
        if (isset($_SESSION['liana_coupon']) && $_SESSION['liana_coupon']) {
            return $_SESSION['liana_coupon'];
        }

        $cart       = Helper::getCart();
        $redemption = self::getActiveRedemption();

        if (empty($cart)) {
            return $coupon;
        }

        $coupon_data = array(
            'id'                          => 0,
            'amount'                      => $redemption['value'],
            'date_created'                => strtotime('-1 hour', time()),
            'date_modified'               => time(),
            'date_expires'                => strtotime('+1 day', time()),
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
}
