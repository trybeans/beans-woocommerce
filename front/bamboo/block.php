<?php

namespace BeansWoo\Front\Bamboo;

use BeansWoo\Helper;

class Block {

    public static $app_name = 'bamboo';
    static $card;

    public static function init(){
	    self::$card = Helper::getCard( self::$app_name );
	    if ( empty( self::$card ) || !Helper::isSetupApp(self::$app_name)) {
		    return;
	    }

	    add_filter('the_content',                                   array(__CLASS__, 'render_page'),     10, 1);

        add_filter('wp_footer',                                     array(__CLASS__, 'render_init'),     10, 1);

    }

    public static function render_init($force=false){
        if (!$force && get_the_ID() === Helper::getConfig( 'bamboo_page')) return;

        ?>

        <script>
            window.beans_cjs_id = "<?php echo is_user_logged_in() ? wp_get_current_user()->ID: ''; ?>";
            window.beans_cjs_email = "<?php echo is_user_logged_in() ? wp_get_current_user()->user_email : ''; ?>";
            window.bamboo_init_data = {
                currentPage: "<?php echo Helper::getCurrentPage(); ?>",
                loginPage: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>",
                registerPage: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>",
                rewardPage: "<?php echo get_permalink( Helper::getConfig('liana_page') ); ?>",
                aboutPage: "<?php echo get_permalink( Helper::getConfig(static::$app_name . '_page') ); ?>",
            };
            window.Beans3.Bamboo.Radix.init();
        </script>
        <?php
    }

    public static function render_page($content, $vars=null){
        if (strpos($content,'[beans_referral_page]') !== false and !is_null(Helper::getConfig(static::$app_name. '_page')) ) {
            ob_start();
            include(dirname(__FILE__) . '/html-page.php');
            self::render_init(true);
            $page = ob_get_clean();
            $content = str_replace('[beans_referral_page]', $page, $content);
        }
        return $content;
    }
}