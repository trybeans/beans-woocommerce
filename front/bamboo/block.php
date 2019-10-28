<?php

namespace BeansWoo\Front\Bamboo;

use BeansWoo\Helper;

class Block {

    public static $app_name = 'bamboo';
    static $card;

    public static function init(){
	    self::$card = Helper::getCard( self::$app_name );
	    if ( empty( self::$card ) || ! self::$card['is_active'] || !Helper::isSetupApp(self::$app_name)) {
		    return;
	    }

        add_filter('wp_head',                                       array(__CLASS__, 'render_head'),     10, 1);
        add_filter('the_content',                                   array(__CLASS__, 'render_page'),     10, 1);

        add_filter('wp_footer',                                     array(__CLASS__, 'render_init'),     10, 1);

    }

    public static function render_head(){
        /* Issue with wp_enqueue_script not always loading, preferred using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */

        ?>
        <script src= 'https://<?php echo Helper::getDomain("STATIC"); ?>/lib/bamboo/3.1/js/bamboo.beans.js?radix=woocommerce&id=<?php echo self::$card['id'];  ?>' type="text/javascript"></script>
        <?php
    }

    public static function render_init($force=false){
        if (!$force && get_the_ID() === Helper::getConfig(static::$app_name. '_page')) return;
        ?>
        <div></div>
        <script>

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