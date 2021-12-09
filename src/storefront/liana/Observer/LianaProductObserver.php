<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaProductObserver extends LianaObserver
{

    public static $pay_with_point_product_ids;

    public static function init($display)
    {
        parent::init($display);

        if (empty(self::$redemption['reward_exclusive_product_cms_ids'])) {
            return;
        }

        self::extractProductIds();

        add_action('wp_loaded', array(__CLASS__, 'handleProductRedemption'), 35);
        add_action('woocommerce_remove_cart_item', array(__CLASS__, 'removeProductFromCart'), 99, 2);

        add_filter('woocommerce_is_purchasable', array(__CLASS__, 'isPurchasableProduct'), 99, 2);
        add_filter('woocommerce_is_sold_individually', array(__CLASS__, 'isSoldIndividuallyProduct'), 99, 2);
        add_filter('woocommerce_product_single_add_to_cart_text', array(__CLASS__, 'updateProductCTA'), 99, 2);
        add_filter('woocommerce_add_to_cart_validation', array(__CLASS__, 'addToCartValidation'), 99, 5);
        add_filter('woocommerce_get_price_html', array(__class__, 'updateProductPrice'), 20, 2);
    }

    public static function handleProductRedemption()
    {
        $cart            = Helper::getCart();
        $account_balance = self::getAccountData('beans');

        if (!isset($cart) || is_null($account_balance)) {
            return;
        }

        $amount      = 0;
        $product_ids = array();
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if (in_array($cart_item['product_id'], self::$pay_with_point_product_ids)) {
                $product_ids[$cart_item_key] = $cart_item['product_id'];
                $amount                      += $cart_item['data']->get_price() * $cart_item['quantity'];
            }
        }
        $beans_amount = $amount * self::$display['beans_rate'];

        if (count($product_ids) != 0 && $beans_amount > $account_balance) {
            if ($cart->has_discount(self::REDEEM_COUPON_UID)) {
                $cart->remove_coupon(self::REDEEM_COUPON_UID);
            }

            foreach ($product_ids as $cart_item_key => $pay_with_point_product_id) {
                $cart->remove_cart_item($cart_item_key);
            }

            return;
        }

        if (count($product_ids) != 0 && !$cart->has_discount(self::REDEEM_COUPON_UID)) {
            self::cancelRedemption();

            # force quantity to be always equal to 1 for `pay wit point product`
            foreach ($product_ids as $cart_item_key => $pay_with_point_product_id) {
                $cart->set_quantity($cart_item_key, 1);
            }

            if ($beans_amount > $account_balance) {
                wc_add_notice(Helper::replaceTags(
                    self::$i18n_strings['reward_product']['not_enough_points'],
                    array(
                        "beans_name" => self::$display['beans_name'],
                    )
                ), 'notice');

                return;
            }
            $_SESSION['liana_redemption'] = array(
                'code'  => self::REDEEM_COUPON_UID,
                'value' => $amount,
                'beans' => $amount * self::$display['beans_rate'],
            );

            $cart->apply_coupon(self::REDEEM_COUPON_UID);
        }
    }

    public static function updateProductCTA($button_text, $product)
    {
        if (in_array($product->get_id(), self::$pay_with_point_product_ids)) {
            $button_text = __(
                Helper::replaceTags(
                    self::$i18n_strings['button']['pay_with'],
                    array(
                        'beans_name' => self::$display['beans_name'],
                    )
                ),
                "woocommerce"
            );
        }

        return $button_text;
    }

    public static function updateProductPrice($price_html, $product)
    {
        $product_id = (int)$product->get_parent_id();
        if ((int)$product_id === 0) {
            $product_id = (int)$product->get_id();
        }

        if (
            in_array($product_id, self::$pay_with_point_product_ids)
            && !in_array(Helper::getCurrentPage(), array('cart', 'home', 'shop'))
        ) {
            $price_html = '<span class="amount">'
                . $product->get_price() * self::$display['beans_rate'] . ' '
                . self::$display['beans_name']
                . ' </span>';
        }

        return $price_html;
    }

    public static function isSoldIndividuallyProduct($is_sol_individually, $product)
    {
        $product_id = (int)$product->get_parent_id();
        if ((int)$product_id === 0) {
            $product_id = (int)$product->get_id();
        }

        if (in_array($product_id, self::$pay_with_point_product_ids)) {
            $is_sol_individually = true;
        }

        return $is_sol_individually;
    }

    public static function isPurchasableProduct($is_purchasable, $product)
    {
        $product_with_points = array();

        $current_product_id = (int)$product->get_parent_id();
        if ((int)$current_product_id === 0) {
            $current_product_id = (int)$product->get_id();
        }
        if (WC()->cart->get_cart_contents_count() == 0) {
            return $is_purchasable;
        }

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if (in_array($cart_item['product_id'], self::$pay_with_point_product_ids)) {
                $product_with_points[] = $cart_item['product_id'];
            }
        }

        if (in_array($current_product_id, self::$pay_with_point_product_ids) && count($product_with_points) != 0) {
            $is_purchasable = false;
        }

        return $is_purchasable;
    }

    public static function addToCartValidation($result, $product_id, $quantity, $variation_id = 0, $variations = null)
    {
        if (!is_user_logged_in() && in_array($product_id, self::$pay_with_point_product_ids)) {
            wc_add_notice(self::$i18n_strings['reward_product']['join_and_get'], 'error');
            $result = false;
        } elseif (
            is_user_logged_in()
            && in_array($product_id, self::$pay_with_point_product_ids)
        ) {
            if ((int)$variation_id === 0) {
                $product = wc_get_product($product_id);
            } else {
                $product = new \WC_Product_Variation($variation_id);
            }
            BeansAccount::update();
            $account_balance = self::getAccountData('beans');

            $min_beans = $product->get_price() * self::$display['beans_rate'];

            if ($min_beans > $account_balance) {
                wc_add_notice(Helper::replaceTags(
                    self::$i18n_strings['reward_product']['not_enough_points'],
                    ["beans_name" => self::$display['beans_name']]
                ), 'notice');
                $result = false;
            }
        }

        return $result;
    }

    public static function removeProductFromCart($cart_item_key, $cart)
    {
        $cart_item = $cart->get_cart()[$cart_item_key];

        if (
            in_array($cart_item['product_id'], self::$pay_with_point_product_ids)
            && $cart->has_discount(self::REDEEM_COUPON_UID)
        ) {
            self::cancelRedemption();
        }
    }

    public static function extractProductIds()
    {
        self::$pay_with_point_product_ids = array_map(
            function ($value) {
                return (int)$value;
            },
            self::$redemption['reward_exclusive_product_cms_ids']
        );
    }
}
