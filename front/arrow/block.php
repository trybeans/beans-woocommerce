<?php


namespace BeansWoo\Front\Arrow;

use BeansWoo\Helper;

class Block {

    static public $app_name = 'arrow';
    static $card;

    public static function init(){
        if (! Helper::isSetupApp(self::$app_name)) {
            return;
        }

        add_action("woocommerce_login_form_start",                      array(__CLASS__, 'render_button'), 10);
    }


    public static function render_arrow_button_container(){
        ?>
        <div class="form-row form-row-first beans-arrow"></div>
        <?php
    }

}