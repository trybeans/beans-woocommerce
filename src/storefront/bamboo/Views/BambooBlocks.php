<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Helper;

class BambooBlocks
{
    protected static $display;

    public static function init($display)
    {
        self::$display = $display;

        if (get_option(Helper::OPTIONS['account-nav']['handle'])) {
            add_filter('woocommerce_account_menu_items', array(__CLASS__, 'updateAccountMenuItems'), 10, 1);
            add_filter('woocommerce_get_endpoint_url', array(__CLASS__, 'getAccountMenuItemLink'), 10, 2);
        }
    }

    /**
     * Append rewards program page to the account navigation menu.
     *
     * @since 3.4.0
     *
     * @param array $items: ['account-nav-slug' => 'Page Name', ...]
     * @return array $items: ['account-nav-slug' => 'Page Name', ...]
     */
    public static function updateAccountMenuItems($items)
    {
        $items['bamboo'] = 'Referrals';
        return $items;
    }

    /**
     * Resolve nagivation to the rewards program page from the account navigation menu.
     *
     * @since 3.4.0
     *
     * @param string $url: Link to the page before update
     * @param string  $endpoint: account-nav-slug
     *
     * @return string $url: Link to the page after update
     */
    public static function getAccountMenuItemLink($url, $endpoint)
    {
        if ($endpoint === 'bamboo') {
            return get_permalink(Helper::getConfig('bamboo_page'));
        }
        return $url;
    }
}
