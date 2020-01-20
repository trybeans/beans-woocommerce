<?php


namespace BeansWoo\Front\Poppy;

use BeansWoo\Helper;

class Block {

    static public $app_name = 'poppy';
    static $card;

    public static function init(){
        self::$card = Helper::getCard( self::$app_name );

        if ( empty( self::$card ) || !self::$card['is_active'] || ! Helper::isSetupApp(self::$app_name)) {
            return;
        }
        if( !Helper::isSetupApp('ultimate') ){

            add_filter('wp_head',                                        array(__CLASS__, 'render_head'),     10, 1);
        }
        add_filter('wp_footer',         array(__CLASS__, 'render_init'), 10, 1);
	}

    public static function render_head(){
        /* Issue with wp_enqueue_script not always loading, prefered using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */

        ?>
            <script src='https://<?php echo Helper::getDomain("STATIC"); ?>/lib/poppy/3.1/js/poppy.beans.js?id=<?php echo self::$card['id'];  ?>' type="text/javascript"></script>
        <?php
    }

    public static function render_init(){
        ?>
        <script>
            window.Beans3.Poppy.init();
        </script>
        <?php
    }
}