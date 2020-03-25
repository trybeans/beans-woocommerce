<?php


namespace BeansWoo\Front\Snow;

use BeansWoo\Helper;

class Block {

    static public $app_name = 'snow';
    static $card;

    public static function init(){
        self::$card = Helper::getCard( self::$app_name );

        if ( empty( self::$card ) || !self::$card['is_active'] || ! Helper::isSetupApp(self::$app_name)) {
            return;
        }

        add_filter('wp_footer',         array(__CLASS__, 'render_init'), 10, 1);
	}

    public static function render_init(){
        ?>
        <script>
            window.Beans3.Snow.init();
        </script>
        <?php
    }

}