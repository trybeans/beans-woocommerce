<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaCart
{

    public static function init()
    {

        add_action('woocommerce_after_cart_totals', array(__CLASS__, 'renderCart'), 10, 1);

        add_filter('woocommerce_add_to_cart_fragments', array(__CLASS__, 'renderCartFragment'), 15, 1);

        if (get_option('beans-liana-display-redemption-checkout')) {
            add_action('woocommerce_review_order_after_payment', array(__CLASS__, 'renderCart'), 15, 1);
        }
    }

    public static function renderCartFragment($fragments)
    {
        $cart  = Helper::getCart();
        ob_start();
        if (count($cart->get_cart()) == 0) {
            LianaObserver::cancelRedemption();
            ?>
            <script>
                delete window.liana_init_data.debit
            </script>
            <?php
        }
        self::renderCart();
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

    public static function renderCart()
    {
        $cart_subtotal  = Helper::getCart()->cart_contents_total;

        ?>
        <div
                id="beans-cart-redeem-button"
                class="beans-cart"
                beans-btn-class="checkout-button button"
                beans-cart_total="<?php echo $cart_subtotal; ?>">
        </div>
        <?php
    }
}
