<?php

namespace BeansWoo\Front\Liana;

use BeansWoo\Helper;

class Block {

    public static $app_name = 'liana';
    static $card;

    public static function init(){
	    self::$card = Helper::getCard( self::$app_name );
	    if ( empty( self::$card ) || ! self::$card['is_active'] || !Helper::isSetupApp(self::$app_name)) {
		    return;
	    }

        add_filter('wp_enqueue_scripts',                            array(__CLASS__, 'enqueue_scripts'), 10, 1);
        add_filter('wp_head',                                       array(__CLASS__, 'render_head'),     10, 1);
        add_filter('the_content',                                   array(__CLASS__, 'render_page'),     10, 1);
        add_filter('woocommerce_after_cart_totals',                 array(__CLASS__, 'render_cart'),     10, 1);
        add_filter('woocommerce_register_form_start',               array(__CLASS__, 'render_register'), 15, 1);
        add_filter( 'add_to_cart_fragments',                        array(__CLASS__, 'render_cart_fragment'), 999, 1 );

        add_filter('wp_footer',                                     array(__CLASS__, 'render_init'),     10, 1);


        //add_filter('woocommerce_review_order_after_payment',         array(__CLASS__, 'render_cart'),     15, 1);
//	    if (is_user_logged_in() && isset($_SESSION[static::$app_name . "_account"]) ){
//            add_filter('woocommerce_review_order_after_submit',      array(__CLASS__, 'render_cart'),     15, 1);
//        }
    }

    public static function render_cart_fragment( $fragments ) {
        $cart_subtotal  = Helper::getCart()->cart_contents_total;
        if($cart_subtotal == 0){
            Observer::cancelRedemption();
            ob_start();
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

    public static function enqueue_scripts(){
        wp_enqueue_style( 'beans-style', plugins_url( 'assets/beans.css' , BEANS_PLUGIN_FILENAME ));
    }

    public static function render_head(){
        /* Issue with wp_enqueue_script not always loading, prefered using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */

        ?>
        <script src= 'https://<?php echo Helper::getDomain("STATIC"); ?>/lib/liana/3.1/js/liana.beans.js?radix=woocommerce&id=<?php echo self::$card['id'];  ?>' type="text/javascript"></script>
        <?php
    }

    public static function render_cart(){
        $cart_subtotal  = Helper::getCart()->cart_contents_total;

        ?>
        <div class="beans-cart"  beans-btn-class="checkout-button button" beans-cart_total="<?php echo $cart_subtotal; ?>"></div>
        <?php
    }

    public static function render_init($force=false){
        if (!$force && get_the_ID() === Helper::getConfig(static::$app_name. '_page')) return;

        $token = array();
        $debit = array();

        if (is_user_logged_in() and !isset($_SESSION[static::$app_name . "_account"])){
            Observer::customerRegister(get_current_user_id());
        }

        if(isset($_SESSION[static::$app_name . '_token'])) $token = $_SESSION[static::$app_name . '_token'];
        if(isset($_SESSION[static::$app_name . '_debit'])) $debit = $_SESSION[static::$app_name . '_debit'];

        ?>
        <div></div>
        <script>
            window.liana_init_data = {
                currentPage: '<?php echo self::getCurrentPage(); ?>',
                accountToken: "<?php  echo isset($token['key'])? $token['key'] : ''; ?>",
                loginPage: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>",
                aboutPage:  "<?php echo get_permalink( Helper::getConfig(static::$app_name . '_page') ); ?>",
                debit: {
                    <?php
                        Helper::getAccountData($debit, 'beans', 0);
                        Helper::getAccountData($debit, 'message', '');
                        echo "uid: '". BEANS_LIANA_COUPON_UID . "'";?>
                },
            };
            if (window.liana_init_data.debit.beans === ""){
                delete window.liana_init_data.debit;
            }
            window.Beans3.Liana.Radix.init();
            <?php if (Helper::getCart()->cart_contents_count != 0): ?>
            window.Beans3.Liana.storage.cart = {
                item_count: "<?php echo Helper::getCart()->cart_contents_count; ?>",
                total_price: "<?php echo Helper::getCart()->subtotal; ?>",
            };
            <?php endif; ?>
        </script>
        <?php
    }

    public static function getCurrentPage(){
        $pages = [
            get_permalink(get_option('woocommerce_myaccount_page_id')) => 'login',
            get_permalink(get_option('woocommerce_cart_page_id')) => 'cart',
            get_permalink(get_option('woocommerce_shop_page_id')) => 'product',
            get_permalink(get_option('woocommerce_checkout_page_id')) => 'checkout',
            get_permalink(Helper::getConfig(static::$app_name . '_page')) => 'reward',
        ];

        $current_page = esc_url(home_url($_SERVER['REQUEST_URI']));

        return isset($pages[$current_page]) ? $pages[$current_page] : '';
    }

    public static function render_page($content, $vars=null){
        if (strpos($content,'[beans_page]') !== false and !is_null(Helper::getConfig(static::$app_name. '_page')) ) {
            ob_start();
            include(dirname(__FILE__) . '/html-page.php');
            self::render_init(true);
            $page = ob_get_clean();
            $content = str_replace('[beans_page]', $page, $content);
        }
        return $content;
    }

    public static function render_register(){
       ?>
        <p class="form-row form-row-first">
            <label for="reg_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="first_name" id="reg_first_name"
                   value="<?php if ( ! empty( $_POST['first_name'] ) ) esc_attr_e( $_POST['first_name'] ); ?>" />
        </p>

        <p class="form-row form-row-last">
            <label for="reg_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="last_name" id="reg_last_name"
                   value="<?php if ( ! empty( $_POST['last_name'] ) ) esc_attr_e( $_POST['last_name'] ); ?>" />
        </p>
        <?php
    }

}