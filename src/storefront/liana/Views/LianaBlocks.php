<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaBlocks
{
    protected static $display;

    public static function init($display)
    {
        self::$display = $display;

        add_action('woocommerce_after_cart_totals', array(__CLASS__, 'renderCart'), 10, 0);

        if (get_option(Helper::OPTIONS['checkout-redeem']['handle'])) {
            add_action('woocommerce_review_order_after_payment', array(__CLASS__, 'renderCart'), 99, 0);
        }

        if (get_option(Helper::OPTIONS['product-points']['handle'])) {
            add_action('woocommerce_simple_add_to_cart', array(__CLASS__, 'renderProductInfo'), 10, 0);
        }

        if (get_option(Helper::OPTIONS['cart-notices']['handle'])) {
            add_action('woocommerce_before_cart', array(__CLASS__, 'renderCartNotice'), 10, 0);
            if (get_option(Helper::OPTIONS['checkout-redeem']['handle'])) {
                add_action('woocommerce_before_checkout_form', array(__CLASS__, 'renderCartNotice'), 10, 0);
            }
        }

        if (get_option(Helper::OPTIONS['account-nav']['handle'])) {
            add_filter('woocommerce_account_menu_items', array(__CLASS__, 'updateAccountMenuItems'), 5, 1);
            add_filter('woocommerce_get_endpoint_url', array(__CLASS__, 'getAccountMenuItemLink'), 10, 2);
        }

        if (get_option(Helper::OPTIONS['subscription-redemption']['handle'])) {
            add_action(
                'woocommerce_subscription_details_table',
                array(__CLASS__, 'renderSubscriptionRedemption'),
                30,
                1
            );
        }
    }

    /**
     * Append rewards program page to the account navigation menu.
     *
     * @param array $items: ['account-nav-slug' => 'Page Name', ...]
     * @return array $items: ['account-nav-slug' => 'Page Name', ...]
     *
     * @since 3.4.0
     */
    public static function updateAccountMenuItems($items)
    {
        $items['liana'] = 'Rewards';
        return $items;
    }

    /**
     * Resolve nagivation to the rewards program page from the account navigation menu.
     *
     * @param string $url: Link to the page before update
     * @param string  $endpoint: account-nav-slug
     *
     * @return string $url: Link to the page after update
     *
     * @since 3.4.0
     */
    public static function getAccountMenuItemLink($url, $endpoint)
    {
        if ($endpoint === 'liana') {
            return get_permalink(Helper::getConfig('liana_page'));
        }
        return $url;
    }

    /**
     * Display an info on the product page, just before the add to card button.
     *
     * `Buy this product and earn X points.`
     *
     * @return void HTML
     *
     * @since 3.4.0
     */
    public static function renderProductInfo()
    {
        global $product;

        $product_points = $product->get_price() * self::$display['beans_ccy_spent'];

        $notice_earn_points = Helper::replaceTags(
            self::$display['i18n_strings']['rules']['earn_points_product'],
            ["beans_name" => self::$display['beans_name'], "quantity" => $product_points]
        );
        ?>
        <div class="woocommerce">
          <div class="beans-product-info"><p><?=$notice_earn_points?></p></div>
        </div>
        <?php
    }

    /**
     * Display messages on the cart page, just before the cart table
     *
     * `Complete your purchase and earn X points.`
     * `You have X points. | Redeem`
     *
     * @return void HTML
     *
     * @since 3.4.0
     */
    public static function renderCartNotice()
    {
        $notice_earn_points = null;
        $notice_redeem_points = null;
        $notice_cancel_redemption = null;

        $cart_subtotal = Helper::getCart()->cart_contents_total;
        $cart_points = intval($cart_subtotal * self::$display['beans_ccy_spent']);

        $account = BeansAccount::getSession();
        $active_redemption = LianaObserver::getActiveRedemption(LianaObserver::REDEEM_COUPON_CODE);

        $notice_earn_points = Helper::replaceTags(
            self::$display['i18n_strings']['rules']['earn_points_cart'],
            ["beans_name" => self::$display['beans_name'], "quantity" => $cart_points]
        );

        if ($active_redemption) {
            $notice_cancel_redemption = Helper::replaceTags(
                self::$display['i18n_strings']['redemption']['you_redeemed_x_points'],
                ["beans_name" => self::$display['beans_name'], "quantity" => $active_redemption['beans']]
            );
        } elseif ($account) {
            $notice_redeem_points = Helper::replaceTags(
                self::$display['i18n_strings']['status']['you_have_points'],
                ["beans_name" => self::$display['beans_name'], "quantity" => $account['liana']['beans']]
            );
        }

        ?>
        <div class="woocommerce">
          <div class="woocommerce-info"><?=$notice_earn_points?></div>
          <?php if ($notice_cancel_redemption) : ?>
            <div class="woocommerce-info">
                <?=$notice_cancel_redemption?>
              <a class="woocommerce-Button button" onclick="return Beans3.Liana.Redemption.cancel();"
                >Cancel redemption</a> 
            </div>
          <?php endif?>
          <?php if ($notice_redeem_points) : ?>
            <div class="woocommerce-info">
                <?=$notice_redeem_points?>
              <a class="woocommerce-Button button" onclick="return Beans3.Liana.Redemption.apply();">Redeem</a> 
            </div>
          <?php endif?>
        </div>
        <?php
    }

    /**
     * Display redemption button allowing customers to redeem their points
     * on the cart page
     *
     * `Redeem X points`
     *
     * @return void HTML
     *
     * @since 3.3.0
     */
    public static function renderCart()
    {
        $cart_subtotal = Helper::getCart()->cart_contents_total;

        ?>
        <div
          id="beans-cart-redeem-button"
          class="beans-cart"
          beans-btn-class="checkout-button button"
          beans-cart_total="<?=$cart_subtotal?>"
        >
        </div>
        <?php
    }

    /**
     * Display a block on the subscription page allowing
     * customers to redeem their points on the upcoming subscription renewal.
     *
     * `Buy this product and earn X points.`
     *
     * @param \WC_Subscription $subscription The subscription object being displayed
     *
     * @return void HTML
     *
     * @since 3.5.0
     */
    public static function renderSubscriptionRedemption($subscription)
    {
        $account = BeansAccount::getSession();

        $status_allowed = ["active", "on-hold", "pending", "completed", "failed"];
        if (!$account || !in_array($subscription->get_status(), $status_allowed)) {
            return;
        }

        $redemption_code = $subscription->get_meta('_beans_redeem');

        $sentence_1 = strtr(
            __('You have {quantity} {beans_name}.', 'beans-woocommerce'),
            array(
                '{quantity}' => $account['liana']['beans'],
                '{beans_name}' => self::$display['beans_name'],
            )
        );
        if ($redemption_code) {
            $sentence_2 = strtr(
                __(
                    'You have requested to redeem your {beans_name} on your upcoming subscription renewal. ',
                    'beans-woocommerce'
                ),
                array('{beans_name}' => self::$display['beans_name'])
            );
        } else {
            $sentence_2 = strtr(
                __('Use your {beans_name} to get a discount on your subscription renewal.', 'beans-woocommerce'),
                array('{beans_name}' => self::$display['beans_name'])
            );
        }
        ?>
        <h2><?=__('Loyalty Program', 'beans-woocommerce')?></h2>
        <table class="shop_table order_details beans-subscription">
        <tbody>
          <tr class="beans-subscription-redemption">
            <td class="beans-content">
              <p>
                <?=$sentence_1?> 
                <?=$sentence_2?>
              </p>
            </td>
            <td class="beans-actions">
              <form method="post" action="">
                <input type="hidden" name="subscription_id" value="<?=$subscription->get_id()?>">
                <?php if ($redemption_code) : ?>
                  <button type="submit" class="button redeem" name="beans_action" value="cancel-redeem-subscription">
                    <?=__('Cancel redemption', 'beans-woocommerce')?>
                  </button>
                <?php else : ?>
                  <button type="submit" class="button redeem" name="beans_action" value="redeem-subscription">
                    <?=__('Redeem', 'beans-woocommerce')?>
                  </button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
        </tbody>
        </table>
        <?php
    }
}
