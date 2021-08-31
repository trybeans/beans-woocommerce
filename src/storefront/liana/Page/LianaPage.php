<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class LianaPage
{
    const PAGE_SHORTCODE = 'beans_page'; // public

    public static function init()
    {
        add_shortcode(self::PAGE_SHORTCODE, array(__CLASS__, 'renderPage'));
    }

    public static function renderPage()
    {
        ob_start();
        include(dirname(__FILE__) . '/liana-page.html.php');
        return ob_get_clean();
    }

    public static function getPageReferences()
    {
        return array(
            'shortcode' => '[' . self::PAGE_SHORTCODE . ']',
            'page_id'   => Helper::getConfig('liana_page'),
            'page_name' => 'Rewards Program',
            'option'    => 'beans_page_id',
            'slug'      => 'rewards-program',
            'type'      => 'reward',
        );
    }
}
