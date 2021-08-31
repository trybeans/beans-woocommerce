<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaCart
{

    public static function init()
    {
        add_action('woocommerce_after_cart_totals', array(__CLASS__, 'renderCart'), 10, 1);

        if (get_option('beans-liana-display-redemption-checkout')) {
            add_action('woocommerce_review_order_after_payment', array(__CLASS__, 'renderCart'), 99, 1);
        }
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
