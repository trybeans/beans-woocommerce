<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class BambooPage
{
    const PAGE_SHORTCODE = 'beans_referral_page'; // public
    protected static $display;
    public static $is_powerby;

    public static function init($display)
    {
        self::$display = $display;
        self::$is_powerby = isset(self::$display['is_powerby']) ? self::$display['is_powerby'] : true;

        add_shortcode(self::PAGE_SHORTCODE, array(__CLASS__, 'renderPage'));
    }

    public static function renderPage()
    {
        ob_start();
        include(dirname(__FILE__) . '/bamboo-page.html.php');
        return ob_get_clean();
    }

    public static function getPageReferences()
    {
        $page_id = Helper::getConfig('bamboo_page');

        return array(
            'shortcode' => '[' . self::PAGE_SHORTCODE . ']',
            'page_id' => $page_id,
            'page_name' => 'Referral Program',
            'option' => 'beans_referral_page_id',
            'slug' => 'referral-program',
            'type' => 'referral',
            'page_exists' => get_post($page_id) ? true : false,
        );
    }
}
