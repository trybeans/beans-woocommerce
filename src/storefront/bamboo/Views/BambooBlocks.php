<?php

namespace BeansWoo\StoreFront;

use BeansWoo\Preferences;
use BeansWoo\Helper;

class BambooBlocks
{
    public static function init()
    {
        if (Preferences::get('display_account_nav')) {
            add_filter('woocommerce_account_menu_items', array(__CLASS__, 'updateAccountMenuItems'), 10, 1);
            add_filter('woocommerce_get_endpoint_url', array(__CLASS__, 'getAccountMenuItemLink'), 10, 2);
        }
    }

    /**
     * Append rewards program page to the account navigation menu.
     *
     * @param array $items: ['account-nav-slug' => 'Page Name', ...]
     * @return array $items: ['account-nav-slug' => 'Page Name', ...]
     *
     * @since 3.4.0
     */
    public static function updateAccountMenuItems($items)
    {
        $items['bamboo'] = 'Referrals';
        return $items;
    }

    /**
     * Resolve navigation to the rewards program page from the account navigation menu.
     *
     * @param string $url: Link to the page before update
     * @param string  $endpoint: account-nav-slug
     *
     * @return string $url: Link to the page after update
     *
     * @since 3.4.0
     */
    public static function getAccountMenuItemLink($url, $endpoint)
    {
        if ($endpoint === 'bamboo') {
            return get_permalink(Helper::getConfig('bamboo_page'));
        }
        return $url;
    }
}
