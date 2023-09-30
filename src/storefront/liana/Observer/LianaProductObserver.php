<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaProductObserver extends LianaObserver
{
    public static $pay_with_point_product_ids;

    public static function init($display)
    {
        parent::init($display);

        if (empty(self::$redemption_params['exclusive_product_cms_ids'])) {
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
        $coupon_code     = self::REDEEM_COUPON_CODE;
        $account_balance = BeansAccount::getSessionAttribute('beans');

        if (empty($cart) || empty($account_balance)) {
            return;
        }

        $amount      = 0;
        $product_items = array();
        foreach ($cart->get_cart() as $key => $item) {
            if (in_array($item['product_id'], self::$pay_with_point_product_ids)) {
                $product_items[$key] = $item['product_id'];
                $amount += $item['data']->get_price() * $item['quantity'];
            }
        }

        if (empty($product_items)) {
            return;
        }

        $beans_amount = $amount * self::$display['beans_rate'];

        // Customer does not have enough,
        // Remove the buyable product from the cart.
        if ($account_balance < $beans_amount) {
            if ($cart->has_discount($coupon_code)) {
                $cart->remove_coupon($coupon_code);
            }

            foreach ($product_items as $key => $pay_with_point_product_id) {
                $cart->remove_cart_item($key);
            }

            return;
        }

        if ($cart->has_discount($coupon_code)) {
            return;
        }

        self::cancelRedemption();

        # force quantity to be always equal to 1 for `pay wit point product`
        foreach (array_keys($product_items) as $key) {
            $cart->set_quantity($key, 1);
        }

        $_SESSION["liana_redemption_{$coupon_code}"] = array(
            'code'          => $coupon_code,
            'amount'        => $amount,
            'discount_type' => 'fixed_cart',
            'beans'         => $amount * self::$display['beans_rate'],
            'product_ids'   => array_values($product_items),
        );

        $cart->apply_coupon($coupon_code);
    }

    public static function updateProductCTA($button_text, $product)
    {
        if (in_array($product->get_id(), self::$pay_with_point_product_ids)) {
            $button_text = strtr(
                __("Pay with {beans_name}", "beans-woocommerce"),
                array(
                    '{beans_name}' => self::$display['beans_name'],
                )
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

        foreach (WC()->cart->get_cart() as $key => $item) {
            if (in_array($item['product_id'], self::$pay_with_point_product_ids)) {
                $product_with_points[] = $item['product_id'];
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
            $message = __("Join our rewards program to get this product.", "beans-woocommerce");
            wc_add_notice($message, 'error');
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
            BeansAccount::refreshSession();
            $account_balance = BeansAccount::getSessionAttribute('beans');

            $min_beans = $product->get_price() * self::$display['beans_rate'];

            if ($min_beans > $account_balance) {
                $message = strtr(
                    __("You don't have enough {beans_name} to get this product.", "beans-woocommerce"),
                    array(
                        "{beans_name}" => self::$display['beans_name']
                    )
                );
                wc_add_notice($message, 'notice');
                $result = false;
            }
        }

        return $result;
    }

    public static function removeProductFromCart($key, $cart)
    {
        $item = $cart->get_cart()[$key];

        if (
            in_array($item['product_id'], self::$pay_with_point_product_ids)
            && $cart->has_discount(self::REDEEM_COUPON_CODE)
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
            self::$redemption_params['exclusive_product_cms_ids']
        );
    }
}
