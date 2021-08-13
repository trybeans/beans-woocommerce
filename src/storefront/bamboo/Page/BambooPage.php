<?php

namespace BeansWoo\StoreFront;

class BambooPage
{
    public static function init()
    {
        add_shortcode('beans_referral_page', array(__CLASS__, 'renderPage'));
    }
    public static function renderPage()
    {
        ob_start();
        include(dirname(__FILE__) . '/bamboo-page.html.php');
        return ob_get_clean();
    }
}
