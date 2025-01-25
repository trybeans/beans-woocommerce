<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

/**
 * Beans scripts class.
 *
 * @class Scripts
 * @since 3.0.0
 */
class Scripts
{
    /**
     * Initialize scripts.
     *
     * @return void
     *
     * @since 3.0.0
     */
    public static function init()
    {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueueScripts'), 10, 1);
        add_action('wp_footer', array(__CLASS__, 'renderFooter'), 10, 1);
    }

    /**
     * Enqueue scripts.
     *
     * @return void
     *
     * @since 3.0.0
     */
    public static function enqueueScripts()
    {
        $card = Helper::getConfig('card');
        // This script helps to initialize our script even if it is cached.
        // We used to suffer from WP-Rocket caching
        // DON'T TOUCH
        ?>
        <script>
            window.beans_radix = "woocommerce";
            window.beans_card = "<?= $card; ?>";
        </script>
        <?php
        $version = Helper::getConfig('riper_version');
        // Fallback on `lts` when the version is not set. This will help to avoid breaking
        // the store after migrating from 3.3.x to 3.3.5.
        $version = $version ? $version : 'lts';
        wp_enqueue_script(
            'beans-ultimate-js',
            Helper::getDomain("CDN") .
                "/lib/ultimate/$version/woocommerce/ultimate.beans.js" .
                "?radix=woocommerce" .
                "&trellis_version=" . BEANS_TRELLIS_VERSION .
                "&beans_card_id=" . $card,
            array(),
            (string)time(),
            false
        );

        wp_enqueue_style('beans-style', Helper::getAssetURL('assets/css/beans-storefront.css'));
    }

    /**
     * Render footer.
     *
     * @return void
     *
     * @since 3.0.0
     */
    public static function renderFooter()
    {
        $account_page_id = get_option('woocommerce_myaccount_page_id');
        ?>
        <script>
            window.beans_init_data = {
                pages: {
                    _current: "<?= Helper::getCurrentPage() ?>",
                    cart: "<?= wc_get_cart_url() ?>",
                    checkout: "<?= wc_get_checkout_url() ?>",
                    shop: "<?= wc_get_page_permalink('shop') ?>",
                    login: "<?= wc_get_page_permalink('myaccount') ?>",
                    register: "<?= wc_get_page_permalink('myaccount') ?>",
                    liana: "<?= get_permalink(Helper::getConfig('liana_page')) ?>",
                    bamboo: "<?= get_permalink(Helper::getConfig('bamboo_page')) ?>",
                },
                page_ids: {
                    cart: "<?= get_option('woocommerce_cart_page_id'); ?>",
                    checkout: "<?= get_option('woocommerce_checkout_page_id'); ?>",
                    shop: "<?= get_option('woocommerce_shop_page_id'); ?>",
                    liana: "<?= Helper::getConfig('liana_page'); ?>",
                    bamboo: "<?= Helper::getConfig('bamboo_page'); ?>",
                    home: "<?= get_option('page_on_front'); ?>",
                    register: "<?= $account_page_id; ?>",
                    login: "<?= $account_page_id; ?>"
                },
                plugin_version: "<?= BEANS_PLUGIN_VERSION ?>",
                riper_version: "<?= Helper::getConfig('riper_version'); ?>",
            };
            window.Beans3.Radix.init();
        </script>
        <?php
    }
}
