<?php

namespace BeansWoo\StoreFront;

use Beans\BeansError;
use BeansWoo\Helper;

/**
 * Liana Observer
 *
 * Defines common utils useful for all Liana Observers
 *
 * @class LianaObserver
 * @since 3.0.0
 */
class LianaObserver
{
    protected static $display;
    protected static $redemption;
    protected static $i18n_strings;
    protected static $tiers;

    /**
     * In-memory caching
     * Consider using https://developer.wordpress.org/reference/classes/wp_object_cache/
     *
     * @since 3.5.0
     *
     * @var array
     */
    protected static $redemption_cache;

    // Enums that indicate the code of the redemption coupon.
    const REDEEM_COUPON_CODE        = 'redeem_points'; // protected
    const REDEEM_SUBSCRIPTION_CODE  = 'redeem_subscription'; // protected
    const REDEEM_LIFETIME_CODE      = 'redeem_lifetime'; // protected

    /**
     * Initialize observer.
     * Save display object from Beans API and add basic filters.
     *
     * @param array $display The display object retrieved from Beans API
     * @return void
     *
     * @since 3.0.0
     */
    public static function init($display)
    {
        self::$display              = $display;
        self::$redemption           = $display['redemption'];
        self::$i18n_strings         = $display['i18n_strings'];
        self::$tiers                = $display['tiers'];
        self::$redemption_cache     = array();

        add_filter('woocommerce_get_shop_coupon_data', array(__CLASS__, 'getWooCouponData'), 10, 2);
    }

    /**
     * Return a list of all coupon codes used for redemption.
     *
     * @return array
     *
     * @since 3.5.0
     */
    private static function getRedeemCodes()
    {
        return [self::REDEEM_COUPON_CODE, self::REDEEM_SUBSCRIPTION_CODE, self::REDEEM_LIFETIME_CODE];
    }

    /**
     * Clear all pending redemption from the active user's session.
     *
     * @return void
     *
     * @since 3.3.0
     */
    public static function cancelRedemption()
    {
        $cart = Helper::getCart();
        if (empty($cart)) {
            return;
        }
        foreach (self::getRedeemCodes() as $code) {
            $cart->remove_coupon($code);
            $key = "liana_redemption_{$code}";
            unset($_SESSION[$key]);
            unset(self::$redemption_cache[$key]);
        }
    }

    /**
     * Get the maximum discount allowed by verifying redemption
     * restrictions set by the merchant in the Beans interface.
     *
     * @param string $code The code of the coupon.
     *
     * @return array|null [ code => 'xxxx', value => 5, beans => 500 ]
     *
     * @since 3.3.0
     */
    public static function getActiveRedemption($code)
    {
        $key = "liana_redemption_{$code}";

        if (isset(self::$redemption_cache[$key])) {
            return self::$redemption_cache[$key];
        }

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;
    }

    /**
     * Get the maximum discount allowed by verifying redemption
     * restrictions set by the merchant in the Beans interface.
     *
     * @param array $account The beans account associated to the customer.
     * @param float $order_value The subtotal value of the order the coupon will be applied to.
     *
     * @return float|null
     *
     * @since 3.5.0
     */
    protected static function getAllowedDiscount($account, $order_value, $is_notice = true)
    {
        $account_beans       = $account['liana']['beans'];
        $account_beans_value = $account['liana']['beans_value'];

        $max_amount = $order_value;

        if (
            isset(self::$redemption) && isset(self::$redemption['min_beans'])
            && isset(self::$redemption['max_percentage'])
        ) {
            $min_beans = self::$redemption['min_beans'];
            if ($account_beans < $min_beans) {
                if ($is_notice) {
                    $message = strtr(
                        __("You need a minimum of {quantity} {beans_name} to get a discount.", "beans-woocommerce"),
                        array(
                            "{quantity}"   => $min_beans,
                            "{beans_name}" => self::$display['beans_name'],
                        )
                    );
                    $message = Helper::replaceTags(
                        self::$i18n_strings['redemption']['condition_minimum_points'],
                        array(
                            'quantity'   => $min_beans,
                            "beans_name" => self::$display['beans_name'],
                        )
                    );
                    wc_add_notice($message, 'notice');
                }

                return null;
            }

            $percent_discount = self::$redemption['max_percentage'];
            if ($percent_discount < 100) {
                $max_amount = (1.0 * $order_value * $percent_discount) / 100;
                if ($is_notice && $max_amount < $account_beans_value) {
                    $message = strtr(
                        __("Maximum discount for this order is {max_discount}%.", "beans-woocommerce"),
                        array(
                            '{max_discount}' => $percent_discount,
                        )
                    );
                    $message = Helper::replaceTags(
                        self::$i18n_strings['redemption']['condition_maximum_discount'],
                        array(
                            'max_discount' => $percent_discount,
                        )
                    );
                    wc_add_notice($message, 'notice');
                }
            }
        }

        $amount = min($max_amount, $account_beans_value);

        return floatval(sprintf('%0.2f', $amount));
    }

    /**
     * Returns a custom coupon object to be used on the WC_Order
     * This object reperesents a virtual coupon
     *
     * @param \WC_Coupon $coupon The coupon as initiated by WooCommerce or other third-paty app.
     * @param string $coupon_code The coupon used for redemption.
     *
     * @return \WC_Coupon|array data use to initially the virtual coupon
     *
     * @since 3.5.0
     */
    public static function getWooCouponData($coupon, $coupon_code)
    {
        // Check if active customer is member of the rewards program
        // Check if coupon_code is a redemption of points

        $r  = self::getActiveRedemption($coupon_code);

        if (!$r) {
            return $coupon;
        }

        $coupon_data = array(
            'id'                          => 0,
            'amount'                      => $r['amount'],
            'date_created'                => strtotime('-1 hour', time()),
            'date_modified'               => time(),
            'date_expires'                => strtotime('+1 day', time()),
            'discount_type'               => $r['discount_type'],
            'description'                 => '',
            'usage_count'                 => 0,
            'individual_use'              => false,
            'product_ids'                 => isset($r['product_ids']) ? $r['product_ids'] : array(),
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

        return $coupon_data;
    }

    /**
     * Debit points from the customer's beans account when
     * they complete an order using a redemption coupon.
     *
     * @param array $account The Beans account related to the customer placing the order.
     * @param \WC_Order $order The order the coupon has been applied to.
     * @param string $coupon_code The code of the coupon used for redemption.
     *
     * @return void
     *
     * @since 3.5.0
     */
    protected static function commitRedemption($account, $order, $coupon_code)
    {
        $coupon_codes = $order->get_coupon_codes();

        foreach ($coupon_codes as $code) {
            if ($code === $coupon_code) {
                if (!$account) {
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
                    'account'     => $account['id'],
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
    }
}
