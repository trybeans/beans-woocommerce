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
    }

    public static function add_beans_app_activated($response){
        $apps = Helper::getApps();
        $installed_apps = [];

        foreach($apps as $app_name => $app_info){
            if (Helper::isSetupApp($app_name)){
                $installed_apps[] = $app_name;
            }
        }
        $response->data["beans_app"] = $installed_apps;

        return $response;
    }
}
