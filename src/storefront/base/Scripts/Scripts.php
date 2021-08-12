<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class Scripts
{
    public static function init()
    {
        add_action('wp_head', array(__CLASS__, 'renderHead'), 10, 1);

        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueueScripts'), 10, 1);

        add_action('wp_footer', array(__CLASS__, 'renderFooter'), 10, 1);

        if (current_user_can('administrator') and is_null(Helper::getConfig('isAdmin_account'))) {
            do_action('woocommerce_new_customer', get_current_user_id());  // force customer webhook for admin
            Helper::setConfig('is_admin_account', true);
        }
    }

    public static function renderHead()
    {
        ?>
        <script>
            window.beans_cart_page = "<?php echo wc_get_cart_url(); ?>";
            window.beans_checkout_page = "<?php echo wc_get_checkout_url(); ?>";
            window.beans_current_page = "<?php echo Helper::getCurrentPage(); ?>";
            window.beans_shop_page = "<?php echo wc_get_page_permalink('shop'); ?>";
            window.beans_login_page = "<?php echo wc_get_page_permalink('myaccount'); ?>";
            window.beans_register_page = "<?php echo wc_get_page_permalink('myaccount'); ?>";
            window.beans_reward_page = "<?php echo get_permalink(Helper::getConfig('liana_page')); ?>";
            window.beans_referral_page = "<?php echo get_permalink(Helper::getConfig('bamboo_page')); ?>";

            window.beans_cjs_id = "<?php echo is_user_logged_in() ? wp_get_current_user()->ID : ''; ?>";
            window.beans_cjs_email = "<?php echo is_user_logged_in() ? wp_get_current_user()->user_email : ''; ?>";
            window.beans_plugin_version = "<?php echo BEANS_VERSION; ?>";
        </script>
        <?php
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

        wp_enqueue_style('beans-style', BEANS_PLUGIN_URL . 'assets/css/beans-storefront.css');
    }

    public static function renderFooter()
    {
        $token = array();
        $debit = array();

        if (is_user_logged_in() and !isset($_SESSION["liana_account"])) {
            LianaObserver::customerRegister(get_current_user_id());
        }

        if (isset($_SESSION['liana_token'])) {
            $token = $_SESSION['liana_token'];
        }
        if (isset($_SESSION['liana_debit'])) {
            $debit = $_SESSION['liana_debit'];
        }

        ?>

        <script>
            window.bamboo_init_data = {
                currentPage: window.beans_current_page,
                loginPage: window.beans_login_page,
                registerPage: window.beans_register_page,
                rewardPage: window.beans_reward_page,
                aboutPage: window.beans_referral_page,
            };

            window.arrow_init_data = {
                currentPage: window.beans_current_page,
                loginPage: window.beans_login_page,
                registerPage: window.beans_register_page,
            };

            window.liana_init_data = {
                currentPage: window.beans_current_page,
                loginPage: window.beans_login_page,
                registerPage: window.beans_register_page,
                accountToken: "<?php  echo isset($token['key']) ? $token['key'] : ''; ?>",
                aboutPage: window.beans_reward_page,
                cartPage: window.beans_cart_page,
                debit: {
                    <?php
                    Helper::getAccountData($debit, 'beans', 0);
                    Helper::getAccountData($debit, 'message', '');
                    echo "uid: '" . BEANS_LIANA_COUPON_UID . "'";?>
                },
            };

            if (window.liana_init_data.debit.beans === "") {
                delete window.liana_init_data.debit;
            }

            <?php if (Helper::getCart()->cart_contents_count != 0) : ?>
                window.Beans3.Liana.storage.cart = {
                    item_count: "<?php echo Helper::getCart()->cart_contents_count; ?>",
                    // to avoid the decimal numbers for the points.
                    total_price: "<?php echo Helper::getCart()->subtotal * 100; ?>", // DON'T TOUCH
                };
            <?php endif; ?>

            window.Beans3.Liana.Radix.init();
            window.Beans3.Bamboo.Radix.init();
            window.Beans3.Poppy.Radix.init();
            window.Beans3.Snow.Radix.init();
            window.Beans3.Arrow.Radix.init();
        </script>
        <?php
    }
}
