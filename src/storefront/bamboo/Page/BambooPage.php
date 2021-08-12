<?php

namespace BeansWoo\StoreFront\Bamboo\Page;

defined('ABSPATH') or die;

class Block
{

    public static function init()
    {
        add_filter('the_content', array(__CLASS__, 'renderPage'), 10, 1);
    }

    public static function renderPage($content, $vars = null)
    {
        if (strpos($content, '[beans_referral_page]') !== false) {
            ob_start();
            include(dirname(__FILE__) . '/bamboo-page.html.php');
            $page = ob_get_clean();
            $content = str_replace('[beans_referral_page]', $page, $content);
        }
        return $content;
    }
}
