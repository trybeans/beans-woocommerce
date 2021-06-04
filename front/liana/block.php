<?php

namespace BeansWoo\Front\Liana;

defined('ABSPATH') or die;

use BeansWoo\Helper;

class Block {

    public static function init(){

        add_shortcode('beans_page',                                 array(__CLASS__, 'render_page'));
        add_action('woocommerce_after_cart_totals',                 array(__CLASS__, 'render_cart'),     10, 1);

        add_filter('woocommerce_add_to_cart_fragments',             array(__CLASS__, 'render_cart_fragment'), 15, 1 );

	    if (get_option('beans-liana-display-redemption-checkout')){
            add_action('woocommerce_review_order_after_payment',      array(__CLASS__, 'render_cart'),     15, 1);
        }
    }

    public static function render_cart_fragment( $fragments ) {
        $cart  = Helper::getCart();
        ob_start();
        if(count($cart->get_cart()) == 0){
            Observer::cancel_redemption();
            ?>
            <script>
                delete window.liana_init_data.debit
            </script>
            <?php
        }
        self::render_cart();
        if ($fragments){
        ?>
        <script>
            window.Beans3.Liana.Radix.init();
        </script>
        <?php
        }
         $fragments['div.beans-cart'] = ob_get_clean();
         return $fragments;
    }

    public static function render_cart(){
        $cart_subtotal  = Helper::getCart()->cart_contents_total;

        ?>
        <div id="beans-cart-redeem-button" class="beans-cart" beans-btn-class="checkout-button button" beans-cart_total="<?php echo $cart_subtotal; ?>"></div>
        <?php
    }

    public static function render_page(){
        ob_start();
        include(dirname(__FILE__) . '/html-page.php');
        return ob_get_clean();
    }

}