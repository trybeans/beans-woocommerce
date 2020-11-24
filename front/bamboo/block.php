<?php

namespace BeansWoo\Front\Bamboo;

class Block {

    public static function init(){
	    add_shortcode('beans_referral_page',                 array(__CLASS__, 'render_page'));
    }
    public static function render_page(){
        ob_start();
        include(dirname(__FILE__) . '/html-page.php');
        return ob_get_clean();
    }
}
