<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

/**
 * Liana Cart Observer
 *
 * Handle cart redemptions
 *
 * @class LianaCartObserver
 * @since 3.0.0
 */
class LianaCartObserver extends LianaObserver
{
    /**
     * Initialize observer.
     * Save display object from Beans API and add filters.
     *
     * @param array $display The display object retrieved from Beans API
     * @return void
     *
     * @since 3.0.0
     */
    public static function init($display)
    {
        parent::init($display);

        add_action('woocommerce_checkout_order_processed', array(__CLASS__, 'processCartRedemption'), 10, 3);
        add_filter('woocommerce_add_to_cart_fragments', array(__CLASS__, 'renderCartFragment'), 15, 1);
    }

    /**
     * Add a JS fragment that forces the re-rendering of the redeem button on the cart page.
     * This is necessary when the cart is re-load through AJAX
     * For example when updating the number of product of product in the cart.
     *
     * @return string HTML
     *
     * @since 3.0.0
     */
    public static function renderCartFragment($fragments)
    {
        $cart = Helper::getCart();
        ob_start();
        if (count($cart->get_cart()) == 0) {
            self::cancelRedemption();
        }
        LianaBlocks::renderCart();
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

    /**
     * Add redemption coupon to cart:
     * - Clear any existing points redemption from cart
     * - Ensure that the customer has enough points
     * - Create redemption coupon while ensuring redemption constraints
     *
     * @return void The redemption metadata is saved to $_SESSION
     *
     * @since 3.0.0
     */
    public static function applyCartRedemption()
    {
        $account = BeansAccount::refreshSession();

        if (!$account) {
            Helper::log(
                "Unable to redeem: Account is unavailable \n account=>" .
                print_r(BeansAccount::getSession(), true)
            );
            return;
        }

        self::cancelRedemption();

        $cart = Helper::getCart();
        $discount_amount = self::getAllowedDiscount($account, $cart->subtotal);

        if ($discount_amount) {
            $_SESSION['liana_redemption_' . self::REDEEM_COUPON_CODE] = array(
                'code'          => self::REDEEM_COUPON_CODE,
                'amount'        => $discount_amount,
                'discount_type' => 'fixed_cart',
                'beans'         => $discount_amount * self::$display['beans_rate'],
            );
            $cart->apply_coupon(self::REDEEM_COUPON_CODE);
        }
    }

    /**
     * Add lifetime discount to purchase:
     * - Clear any existing points redemption from cart
     * - Ensure that the customer is on the right tier
     * - Create redemption coupon
     *
     * @return void The redemption metadata is saved to $_SESSION
     *
     * @since 3.4.0
     */
    public static function applyLifetimeRedemption()
    {
        if (!BeansAccount::getSessionAttribute('id')) {
            Helper::log(
                "Unable to redeem: Account is unavailable \n account=>" .
                print_r(BeansAccount::getSession(), true)
            );
            return;
        }

        self::cancelRedemption();

        $tier_id = $_POST['tier_id'];
        $tier = null;
        foreach (self::$tiers as $x) {
            if ($x['id'] == $tier_id) {
                $tier = $x;
                break;
            }
        }

        if (is_null($tier)) {
            return;
        }

        $discount_amount = sprintf('%0.2f', $tier['lifetime_discount']);

        $coupon_code = self::REDEEM_LIFETIME_CODE;

        $_SESSION["liana_redemption_{$coupon_code}"] = array(
            'code'          => self::REDEEM_LIFETIME_CODE,
            'amount'        => $discount_amount,
            'discount_type' => 'percent',
            'beans'         => null,
        );

        $cart = Helper::getCart();
        $cart->apply_coupon(self::REDEEM_LIFETIME_CODE);
    }

    /**
     * Process the order:
     * - Debit points from the customer's beans account if there is redemption.
     * - Clear redemption from cart
     * - Refresh the customer's beans account balance
     *
     * @param int $order_id The id of the order being processed.
     * @param array $posted_data
     * @param \WC_Order $order The order being processed.
     *
     * @return void
     *
     * @since 3.5.0
     */
    public static function processCartRedemption($order_id, $posted_data, $order)
    {
        $account = BeansAccount::getSession();
        self::commitRedemption($account, $order, self::REDEEM_COUPON_CODE);
        self::cancelRedemption();
        BeansAccount::refreshSession();
    }
}
