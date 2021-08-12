<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaPage
{

    public static function init()
    {
        add_shortcode('beans_page', array(__CLASS__, 'renderPage'));
    }
    public static function renderPage()
    {
        ob_start();
        include(dirname(__FILE__) . '/liana-page.html.php');
        return ob_get_clean();
    }
}
