<?php

namespace BeansWoo\StoreFront;

class LianaProductAjaxObserver extends LianaProductObserver
{

    public static function init($display)
    {
        LianaObserver::init($display);
        if (empty(self::$redemption['reward_exclusive_product_cms_ids'])) {
            return;
        }
        self::extractProductIds();

        add_action('woocommerce_remove_cart_item', array(__CLASS__, 'removeProductFromCart'), 99, 2);

        add_filter('woocommerce_add_to_cart_validation', array(__CLASS__, 'addToCartValidation'), 99, 5);
    }
}
