<?php


namespace BeansWoo\Front\Arrow;

use BeansWoo\Helper;

class Block {

    static public $app_name = 'arrow';
    static $card;

    public static function init(){
        self::$card = Helper::getCard( self::$app_name );

        if ( empty( self::$card ) || !self::$card['is_active'] || ! Helper::isSetupApp(self::$app_name)) {
            return;
        }

        add_filter('wp_footer',                                         array(__CLASS__, 'render_head'),     10, 1);

        add_filter('wp_footer',                                         array(__CLASS__, 'render_init'),     10, 1);
	}

    public static function render_head(){
        /* Issue with wp_enqueue_script not always loading, prefered using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */

        ?>
            <script src='http://localhost:5000/bundle.js?shop=<?php echo self::$card['id'];?>&radix=woocommerce' type="text/javascript"></script>
        <?php
    }

    public static function render_init($force=false){
        ?>

        <script>
            window.beans_cjs_id = "<?php echo is_user_logged_in() ? wp_get_current_user()->ID: ''; ?>";
            window.beans_cjs_email = "<?php echo is_user_logged_in() ? wp_get_current_user()->user_email : ''; ?>";
            window.arrow_init_data = {
                currentPage: "<?php echo Helper::getCurrentPage(); ?>",
                loginPage: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>",
                registerPage: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>",
            };

            window.Beans3.Arrow.Radix.init();
        </script>
        <?php
    }

}