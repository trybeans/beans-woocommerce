<?php


namespace BeansWoo\Front\Arrow;

use BeansWoo\Helper;

class Block {

    static public $app_name = 'arrow';
    static $card;

    public static function init(){
        self::$card = Helper::getCard( 'ultimate' );

        if (! isset(self::$card[self::$app_name])){
            return ;
        }

        self::$card = self::$card[self::$app_name];

        if ( empty( self::$card ) || ! self::$card['is_active'] || ! Helper::isSetupApp(self::$app_name)) {
            return;
        }

        add_action("woocommerce_login_form_start",                      array(__CLASS__, 'render_button'), 10);

        add_filter('wp_footer',                                         array(__CLASS__, 'render_init'),     10, 1);
	}

    public static function render_init(){
        if (is_user_logged_in()){ return ; }
        ?>

        <script>
            window.beans_cjs_id = "<?php echo is_user_logged_in() ? wp_get_current_user()->ID: ''; ?>";
            window.beans_cjs_email = "<?php echo is_user_logged_in() ? wp_get_current_user()->user_email : ''; ?>";
            window.arrow_init_data = {
                currentPage: "<?php echo Helper::getCurrentPage(); ?>",
                loginPage: "<?php echo str_replace(home_url(), '', get_permalink(get_option('woocommerce_myaccount_page_id')))  ; ?>",
                registerPage: "<?php echo str_replace(home_url(), '', get_permalink(get_option('woocommerce_myaccount_page_id')))  ; ?>",
            };

            window.Beans3.Arrow.Radix.init();
        </script>
        <?php
    }

    public static function render_button(){
        ?>
        <div class="form-row form-row-first beans-arrow"></div>
        <?php
    }

}