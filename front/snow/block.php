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

        add_filter('wp_footer',                                        array(__CLASS__, 'render_head'),     10, 1);
	}

    public static function render_head(){
        /* Issue with wp_enqueue_script not always loading, prefered using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */

        ?>
            <script defer src='https://<?php echo Helper::getDomain("STATIC"); ?>/lib/snow/3.1/js/snow.beans.js?shop=<?php echo self::$card['id'];  ?>' type="text/javascript"></script>
        <?php
    }


}