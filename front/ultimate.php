<?php


namespace BeansWoo\Front;


use BeansWoo\Front\Liana\Observer;
use BeansWoo\Helper;

class Ultimate
{
    static $card;

    public static function init()
    {
        self::$card = Helper::getCard('ultimate');

        add_filter('wp_head',    array(__CLASS__, 'render_head'), 10, 1);
        add_filter('wp_footer', array(__CLASS__, 'ultimate_init'), 10, 1);

    }

    /* HEADER */
    public static function render_head()
    {
        /* Issue with wp_enqueue_script not always loading, preferred using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */

        ?>
        <script src='https://<?php echo Helper::getDomain("STATIC"); ?>/lib/ultimate/3.2/js/woocommerce/ultimate.beans.js?radix=woocommerce&id=<?php echo self::$card['id']; ?>'
                type="text/javascript"></script>
        <?php
    }

    public static function ultimate_init($force = false){
        /* START BAMBOO */
        if ($force && get_the_ID() === Helper::getConfig('bamboo_page')){;

        ?>

        <script>
            window.bamboo_init_data = {
                currentPage: window.beans_currentPage,
                loginPage: window.beans_loginPage,
                registerPage: window.beans_registerPage,
                rewardPage: "<?php echo get_permalink(Helper::getConfig('liana_page')); ?>",
                aboutPage: "<?php echo get_permalink(Helper::getConfig('bamboo_page')); ?>",
            };
            window.Beans3.Bamboo.Radix.init();
        </script>
        <?php
        }
        /* END BAMBOO */

        /* START ARROW */
        if (! is_user_logged_in()) {
        ?>

        <script>
            window.arrow_init_data = {
                currentPage: window.beans_currentPage,
                loginPage: window.beans_loginPage,
                registerPage: window.beans_registerPage,
            };

            window.Beans3.Arrow.Radix.init();
        </script>
        <?php
        }/* END ARROW */

        /* START LIANA */
        if ($force && get_the_ID() === Helper::getConfig('liana_page')) {

            $token = array();
            $debit = array();

            if (is_user_logged_in() and !isset($_SESSION["liana_account"])) {
                Observer::customerRegister(get_current_user_id());
            }

            if (isset($_SESSION['liana_token'])) $token = $_SESSION['liana_token'];
            if (isset($_SESSION['liana_debit'])) $debit = $_SESSION['liana_debit'];

            ?>
            <div></div>
            <script>
                window.liana_init_data = {
                    currentPage: window.beans_currentPage,
                    loginPage: window.beans_loginPage,
                    registerPage: window.beans_registerPage,
                    accountToken: "<?php  echo isset($token['key']) ? $token['key'] : ''; ?>",
                    aboutPage: "<?php echo get_permalink(Helper::getConfig('liana_page')); ?>",
                    cartPage: "<?php echo get_permalink(get_option('woocommerce_checkout_page_id')); ?>",
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
                window.Beans3.Liana.Radix.init();

                <?php if (Helper::getCart()->cart_contents_count != 0): ?>
                window.Beans3.Liana.storage.cart = {
                    item_count: "<?php echo Helper::getCart()->cart_contents_count; ?>",
                    // to avoid the decimal numbers for the points.
                    total_price: "<?php echo Helper::getCart()->subtotal * 100; ?>", // DON'T TOUCH
                };
                <?php endif; ?>
            </script>
            <?php
        }
        /* END LIANA */
        ?>
        <script>
            window.Beans3.Poppy.Radix.init();
            window.Beans3.Snow.Radix.init();
        </script>
        <?php
    }
}