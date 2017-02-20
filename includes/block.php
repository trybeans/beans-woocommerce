<?php

namespace BeansWoo;

class Block {


    public static function init(){
        add_filter('wp_enqueue_scripts',                             array(__CLASS__, 'enqueue_scripts'), 10, 1);
        add_filter('woocommerce_single_product_summary',             array(__CLASS__, 'render_product'),  15, 1);
        add_filter('woocommerce_after_cart_table',                   array(__CLASS__, 'render_cart'),     10, 1);
        add_filter('woocommerce_before_checkout_form',               array(__CLASS__, 'render_cart'),     15, 1);
        add_filter('woocommerce_register_form_start',                array(__CLASS__, 'render_register'), 15, 1);
        add_filter('wp_footer',                                      array(__CLASS__, 'render_init'),     10, 1);
        add_filter('the_content',                                    array(__CLASS__, 'render_page'),     10, 1);
    }

    public static function enqueue_scripts(){
        wp_enqueue_script('beans-script', '//'.Helper::getDomain('WWW').'/assets/static/js/lib/1.1/shop.beans.js');
        wp_enqueue_style( 'beans-style', plugins_url( 'assets/beans.css' , BEANS_PLUGIN_FILE ));
        wp_enqueue_style( 'beans-page-style', plugins_url( 'assets/white_bean.css' , BEANS_PLUGIN_FILE ));
    }

    public static function render_product(){
        global $post;

        if(function_exists('wc_get_product')){
            $product = wc_get_product( $post->ID );
        }else{
            $product = get_product( $post->ID );
        }

        $price          = $product->get_price();
        $sku            = $product->get_sku();
        $min_price      = $product->min_variation_price;
        $max_price      = $product->max_variation_price;

        ?>
            <div class="beans-product" beans-price="<?php echo $price;?>"  beans-price_min="<?php echo $min_price;?>" beans-price_max="<?php echo $max_price;?>" beans-sku="<?php echo $sku;?>"></div>
        <?php
    }

    public static function render_cart(){
        $cart_subtotal  = Helper::get_cart()->cart_contents_total;

        ?>
        <div class="beans-cart" beans-btn-class="button" beans-cart_total="<?php echo $cart_subtotal; ?>"></div>
        <?php
    }

    public static function render_init($force=false){
        if (!$force && get_the_ID() === Helper::getConfig('page')) return;

        $account = array();
        $debit = array();
        if(isset($_SESSION['beans_account'])) $account = $_SESSION['beans_account'];
        if(isset($_SESSION['beans_debit'])) $debit = $_SESSION['beans_debit'];

        ?>
        <div></div>
        <script>
            Beans.Shop.init({
                is_redeem: true,
                address: '<?php echo Helper::getConfig('card'); ?>',
                beans_domain: '<?php echo Helper::getDomain('WWW'); ?>',
                beans_domain_api: '<?php echo Helper::getDomain('API'); ?>',
                reward_page: '<?php echo get_permalink( Helper::getConfig('page') ); ?>',
                login_page: '<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>',
                account: {<?php Helper::getAccountData($account, 'id', '');  Helper::getAccountData($account, 'beans'); ?>},
            });
            Beans.Shop.Redemption = {
                <?php Helper::getAccountData($debit, 'beans', 0);  Helper::getAccountData($debit, 'message', ''); ?>
                apply: function(){Beans.Shop.utils.formPost('', {beans_action: 'apply'});},
                cancel: function(){Beans.Shop.utils.formPost('', {beans_action: 'cancel'});},
            };
        </script>
        <?php
    }

    public static function render_page($content, $vars=null){
        if (strpos($content,'[beans_page]') !== false) {
            ob_start();
            self::render_init(true);
            include(dirname(__FILE__).'/block.page.php');
            $page = ob_get_clean();
            $content = str_replace('[beans_page]', $page, $content);
        }
        return $content;
    }

    public static function render_register(){
       ?>
        <p class="form-row form-row-first">
            <label for="reg_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="first_name" id="reg_first_name" value="<?php if ( ! empty( $_POST['first_name'] ) ) esc_attr_e( $_POST['first_name'] ); ?>" />
        </p>

        <p class="form-row form-row-last">
            <label for="reg_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="last_name" id="reg_last_name" value="<?php if ( ! empty( $_POST['last_name'] ) ) esc_attr_e( $_POST['last_name'] ); ?>" />
        </p>
        <?php
    }

}