<?php


namespace BeansWoo\API;

use BeansWoo\Helper;

class BeansRestWoocommerce
{
    public static function init(){
        add_filter('woocommerce_rest_prepare_customer',           array(__CLASS__, 'add_beans_app_activated'), 90, 1);
        # add_filter('woocommerce_rest_prepare_coupon_object',    array(__CLASS__, 'add_beans_app_activated'), 90, 1);
        add_filter('woocommerce_rest_prepare_shop_order_object',  array(__CLASS__, 'add_beans_app_activated'), 90, 1);
        add_filter('woocommerce_rest_prepare_product_object',     array(__CLASS__, 'add_beans_app_activated'), 90, 1);

        add_filter('woocommerce_rest_prepare_system_status',      array(__CLASS__, 'add_site_pages_infos'),    20, 1);
    }

    public static function add_beans_app_activated($response){
        $apps = Helper::getApps();
        $installed_apps = [];

        foreach($apps as $app_name => $app_info){
            if (Helper::isSetupApp($app_name)){
                $installed_apps[] = $app_name;
            }
        }
        $response->data["beans_apps"] = $installed_apps;

        return $response;
    }

    public static function add_site_pages_infos($response){
        $pages = [
            "login" =>  get_permalink(get_option('woocommerce_myaccount_page_id')),
            "cart" =>   get_permalink(get_option('woocommerce_cart_page_id')),
            "product" => get_permalink(get_option('woocommerce_shop_page_id')),
            "checkout" => get_permalink(get_option('woocommerce_checkout_page_id')),
            "reward" => get_permalink(Helper::getConfig('liana_page')),
        ];

        foreach ($pages as &$link){
            $link = str_replace(home_url(), '', $link);
        }

        if (!isset($response->data['pages'])){
            $response->data['pages'] = [];
        }
        $response->data['pages']['beans'] = $pages;
        return $response;
    }
}
