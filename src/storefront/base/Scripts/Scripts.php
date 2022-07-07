<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class Scripts
{
    public static function init()
    {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueueScripts'), 10, 1);

        add_action('wp_footer', array(__CLASS__, 'renderFooter'), 10, 1);
    }

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
            'https://' . Helper::getDomain("CDN") .
                "/lib/ultimate/$version/woocommerce/ultimate.beans.js?radix=woocommerce&id=" . $card,
            array(),
            (string)time(),
            false
        );

        wp_enqueue_style('beans-style', Helper::getAssetURL('assets/css/beans-storefront.css'));
    }

    public static function renderFooter()
    {
        $account_page_id = get_option('woocommerce_myaccount_page_id');
        ?>
        <script>
            window.beans_pages = {
                _current: "<?= Helper::getCurrentPage() ?>",
                cart: "<?= wc_get_cart_url() ?>",
                checkout: "<?= wc_get_checkout_url() ?>",
                shop: "<?= wc_get_page_permalink('shop') ?>",
                login: "<?= wc_get_page_permalink('myaccount') ?>",
                register: "<?= wc_get_page_permalink('myaccount') ?>",
                liana: "<?= get_permalink(Helper::getConfig('liana_page')) ?>",
                bamboo: "<?= get_permalink(Helper::getConfig('bamboo_page')) ?>",
            }

            window.beans_pages_ids = {
                cart: "<?= get_option('woocommerce_cart_page_id'); ?>",
                checkout: "<?= get_option('woocommerce_checkout_page_id'); ?>",
                shop: "<?= get_option('woocommerce_shop_page_id'); ?>",
                liana: "<?= Helper::getConfig('liana_page'); ?>",
                bamboo: "<?= Helper::getConfig('bamboo_page'); ?>",
                home: "<?= get_option('page_on_front'); ?>",
                register: "<?= $account_page_id; ?>",
                login: "<?= $account_page_id; ?>"
            }
            window.beans_plugin_version = "<?= BEANS_PLUGIN_VERSION ?>";
            window.beans_riper_version = "<?= Helper::getConfig('riper_version'); ?>";
            
            try {
                window.Beans3.Radix.init();
            } catch (error) {
                if (error instanceof TypeError) {
                    console.warn(
                    `You are loading the Beans script from your local cache.
                    It is recommended to not cache the Beans script.
                    Learn more here: https://help.trybeans.com/integrations/woocommerce/caching-issues`
                    );
                } else {
                    throw error;
                }
            }
        </script>
        <?php
    }
}
