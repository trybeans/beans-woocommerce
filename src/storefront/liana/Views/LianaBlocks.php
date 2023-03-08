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
    }

    /**
     * Append rewards program page to the account navigation menu.
     *
     * @since 3.4.0
     *
     * @param array $items: ['account-nav-slug' => 'Page Name', ...]
     * @return array $items: ['account-nav-slug' => 'Page Name', ...]
     */
    public static function updateAccountMenuItems($items)
    {
        $items['liana'] = 'Rewards';
        return $items;
    }

    /**
     * Resolve nagivation to the rewards program page from the account navigation menu.
     *
     * @since 3.4.0
     *
     * @param string $url: Link to the page before update
     * @param string  $endpoint: account-nav-slug
     *
     * @return string $url: Link to the page after update
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
     * @since 3.4.0
     *
     * @return HTML
     */
    public static function renderProductInfo()
    {
        global $product;

        $product_points = $product->price * self::$display['beans_ccy_spent'];

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
     * @since 3.4.0
     *
     * @return HTML
     */
    public static function renderCartNotice()
    {
        $notice_earn_points = null;
        $notice_redeem_points = null;
        $notice_cancel_redemption = null;

        $cart_subtotal = Helper::getCart()->cart_contents_total;
        $cart_points = $cart_subtotal * self::$display['beans_ccy_spent'];

        $account = BeansAccount::get();
        $active_redemption = LianaObserver::getActiveRedemption();

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
}
