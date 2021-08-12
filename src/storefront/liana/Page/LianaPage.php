<?php

namespace BeansWoo\Front\Liana;

use BeansWoo\Helper;

class Block
{

    public static function init()
    {

        add_filter('the_content', array(__CLASS__, 'renderPage'), 10, 1);

    }

    public static function renderPage($content, $vars = null)
    {
        if (strpos($content, '[beans_page]') !== false && Helper::isSetupApp('liana')) {
            ob_start();
            include(dirname(__FILE__) . '/html-page.php');
            $page = ob_get_clean();
            $content = str_replace('[beans_page]', $page, $content);
        }
        return $content;
    }
}