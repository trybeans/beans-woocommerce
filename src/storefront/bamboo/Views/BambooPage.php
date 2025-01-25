<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

/**
 * Bamboo page renderer.
 *
 * @class BambooPage
 * @since 3.0.0
 */
class BambooPage
{
    const PAGE_SHORTCODE = 'beans_referral_page'; // public
    protected static $display;
    public static $is_powerby;

    /**
     * Initialize page renderer.
     * Save display object from Beans API and add actions.
     *
     * @param array $display The display object retrieved from Beans API
     * @return void
     *
     * @since 3.0.0
     */
    public static function init($display)
    {
        self::$display = $display;
        self::$is_powerby = isset(self::$display['is_powerby']) ? self::$display['is_powerby'] : true;

        add_shortcode(self::PAGE_SHORTCODE, array(__CLASS__, 'renderPage'));
    }

    /**
     * Render page.
     *
     * @return string
     *
     * @since 3.0.0
     */
    public static function renderPage()
    {
        ob_start();
        include(dirname(__FILE__) . '/bamboo-page.html.php');
        return ob_get_clean();
    }

    /**
     * Get bamboo page metadata.
     * This is used by connector API to get the page metadata.
     *
     * @return array
     *
     * @since 3.0.0
     */
    public static function getPageMetadata()
    {
        $page_id = Helper::getConfig('bamboo_page');

        return array(
            'shortcode'     => '[' . self::PAGE_SHORTCODE . ']',
            'page_id'       => $page_id,
            'page_name'     => 'Referral Program',
            'option'        => 'beans_referral_page_id',
            'slug'          => 'referral-program',
            'type'          => 'referral',
            'page_exists'   => get_post($page_id) ? true : false,
        );
    }
}
