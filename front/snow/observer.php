<?php


namespace BeansWoo\Front\Snow;

use BeansWoo\Helper;

class Observer {

    static public $app_name = 'snow';

	public static function init(){
        $card = Helper::getCard( self::$app_name );
        if ( empty( $card ) || ! $card['is_active'] || ! Helper::isSetupApp(self::$app_name)) {
            return;
        }

        add_filter('wp_head',                                        array(__CLASS__, 'render_head'),     10, 1);
	}

    public static function render_head(){
        /* Issue with wp_enqueue_script not always loading, prefered using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */
        ?>
        <script src='https://trybeans.s3.amazonaws.com/lib/snow/3.1/js/snow.beans.js?domainAPI=api-3.bns.re&domainConnect=connect.bns.re&env=beta&shop=corentin-test.myshopify.com' type="text/javascript"></script>
        <?php
    }


}