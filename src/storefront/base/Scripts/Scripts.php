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
        wp_enqueue_script(
            'beans-ultimate-js',
            'https://' . Helper::getDomain("CDN") .
            '/lib/ultimate/3.3/woocommerce/ultimate.beans.js?radix=woocommerce&id=' . Helper::getConfig('card'),
            array(),
            time(),
            false
        );

        wp_enqueue_style('beans-style', plugins_url('assets/css/beans-storefront.css', BEANS_PLUGIN_FILENAME));
    }

    public static function renderFooter()
    {
        ?>
        <script>
            window.beans_pages = {
                _current: "<?php echo Helper::getCurrentPage(); ?>",
                cart: "<?php echo wc_get_cart_url(); ?>",
                checkout: "<?php echo wc_get_checkout_url(); ?>",
                shop: "<?php echo wc_get_page_permalink('shop'); ?>",
                login: "<?php echo wc_get_page_permalink('myaccount'); ?>",
                register: "<?php echo wc_get_page_permalink('myaccount'); ?>",
                liana: "<?php echo get_permalink(Helper::getConfig('liana_page')); ?>",
                bamboo: "<?php echo get_permalink(Helper::getConfig('bamboo_page')); ?>",
            }
            window.beans_plugin_version = "<?php echo BEANS_VERSION; ?>";
            window.Beans3.Radix.init();
        </script>
        <?php
    }
}
