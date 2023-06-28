<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaSubscriptionObserver extends LianaObserver
{
    public static function init($display)
    {
        parent::init($display);

        add_filter('wcs_new_order_created', array(__CLASS__, 'processSubscriptionRedemption'), 10, 3);
    }

    /**
     * Process the order:
     * - Verify redemption intent for subscription if any
     * - Create redemption coupon and apply to order renewal
     * - Debit points from the customer's beans account
     * - Clear redemption intent for subscription if any
     *
     * @param \WC_Order        $order        The new order created from the subscription.
     * @param \WC_Subscription $subscription The subscription the order was created from.
     * @param string           $type         The type of order being created. ['renewal_order', 'resubscribe_order']
     *
     * @return \WC_Order the order
     *
     * @since 3.5.0
     */
    public static function processSubscriptionRedemption($order, $subscription, $type)
    {
        $is_beans_redeem = $subscription->get_meta('_beans_redeem');

        Helper::log(
            "processing subscription={$subscription->get_id()} order={$order->get_id()} " .
            "is_redeem={$is_beans_redeem}"
        );

        if (!$is_beans_redeem) {
            return $order;
        }

        $coupon_code = self::REDEEM_SUBSCRIPTION_CODE;
        $customer = $order->get_user();
        $account = BeansAccount::retrieve($customer->user_email, false);
        $account_id = isset($account['id']) ? $account['id'] : null;
        $discount_amount = self::getAllowedDiscount($account, $subscription->get_subtotal(), false);

        Helper::log(
            "processing redemption subscription={$subscription->get_id()} order={$order->get_id()} " .
            "account={$account_id} discount_amount={$discount_amount}"
        );

        if (empty($discount_amount) || empty($account)) {
            return;
        }

        self::$redemption_cache["liana_redemption_{$coupon_code}"] = array(
            'code'          => $coupon_code,
            'amount'        => $discount_amount,
            'discount_type' => 'fixed_cart',
            'beans'         => $discount_amount * self::$display['beans_rate'],
        );

        $order->apply_coupon($coupon_code);
        $order->save();

        // Delete the metadata on the subscription which indicated that the
        // customer would like to redeem their points when the subscription renew
        $subscription->delete_meta_data('_beans_redeem');
        $subscription->save();

        // Debit points from the customer's beans account
        self::commitRedemption($account, $order, $coupon_code);

        return $order;
    }

    /**
     * Apply subscription redemption:
     * Add a metadata that indicates that the customer wants to redeem
     * their points on subscription renewal.
     *
     * @return void
     *
     * @since 3.5.0
     */
    public static function applySubscriptionRedemption()
    {
        if (!BeansAccount::getSessionAttribute('id')) {
            Helper::log(
                "Unable to redeem: Account is unavailable \n account=>" .
                print_r(BeansAccount::getSession(), true)
            );
            return;
        }

        $subscription = wcs_get_subscription($_POST['subscription_id']);

        // Add a metatag on the subscription which indicated that the
        // customer would like to redeem their points when the subscription renew
        $subscription->update_meta_data('_beans_redeem', self::REDEEM_SUBSCRIPTION_CODE);
        $subscription->save();
    }

    /**
     * Cancel subscription redemption:
     * Clear the metadata that indicates that the customer wants to redeem
     * their points on subscription renewal.
     *
     * @return void
     *
     * @since 3.5.0
     */
    public static function cancelSubscriptionRedemption()
    {
        $subscription = wcs_get_subscription($_POST['subscription_id']);

        // Delete the metatag on the subscription which indicated that the
        // customer would like to redeem their points when the subscription renew
        $subscription->delete_meta_data('_beans_redeem');
        $subscription->save();
    }
}
