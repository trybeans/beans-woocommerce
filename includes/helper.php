<?php

namespace BeansWoo;

use Beans\Beans;

class Helper {
    const CONFIG_NAME = 'beans-woo-config';

    public static $card = null;
    public static $key = null;

    public static function getDomain($sub){
        if($sub == 'API'){
            $domain = getenv('BEANS_DOMAIN_API');
            if(!$domain)
                $domain = 'api-2.trybeans.com';
            return $domain;
        }else{
            $domain = getenv('BEANS_DOMAIN_BUSINESS');
            if(!$domain)
                $domain = 'www.trybeans.com';
            return $domain;
        }
    }

    public static function getAccountData($account, $k, $default=null){
        if(isset($account[$k])) {
            echo "$k:'".$account[$k]."',";
        }
        else if($default !== null) {
            echo "$k: '',";
        }
    }

    public static function getConfig($key){
        $config = get_option(self::CONFIG_NAME);
        if(isset($config[$key])) return $config[$key];
        return null;
    }

    public static function isConfigured(){
        return Helper::getConfig('key') &&
               Helper::getConfig('card')&&
               Helper::getConfig('secret');
    }

    public static function setConfig($key, $value){
        $config = get_option(self::CONFIG_NAME);
        $config[$key] = $value;
        update_option(self::CONFIG_NAME, $config);
    }

    private static function setKey(){
        if (!self::$key)
            self::$key = self::getConfig('secret');
    }

    public static function resetSetup(){
        self::setConfig('key', null);
        self::setConfig('card', null);
        self::setConfig('secret', null);
        self::setConfig('oauth_token', null);
        self::setConfig('oauth_consumer_connect', null);
        self::$card = null;
        return true;
    }

    public static function API() {
        self::setKey();
        $beans           = new Beans(self::$key);
        $beans->endpoint = 'https://'.self::getDomain('API').'/v2/';
        return $beans;
    }

    public static function log($info){
        if( file_exists(BEANS_INFO_LOG) && filesize(BEANS_INFO_LOG)>100000)
            unlink(BEANS_INFO_LOG);

        if(!is_writable(BEANS_INFO_LOG)){
            return false;
        }

        $log = date('Y-m-d H:i:s.uP') ." => ".$info.PHP_EOL;

        try{
            file_put_contents(BEANS_INFO_LOG, $log, FILE_APPEND);
        }catch(\Exception $e){
            return false;
        }
        return true;
    }

    public static function getCard() {
        if (!self::$card && self::isInstalled()) {
            try {
                self::$card = self::API()->get('card/current');
            } catch (\Beans\Error\BaseError $e) {
                if($e->getCode() < 400){
                    self::resetSetup();
                }
                self::log('Unable to get card: '.$e->getMessage());
            }
        }

//        if(self::$card && self::getConfig('synced') != date('Y-m-d').BEANS_VERSION){
//            if(self::synchronise()){
//                self::setConfig('synced', date('Y-m-d').BEANS_VERSION);
//            }
//        }

        return self::$card;
    }

    public static function isInstalled() {
        self::setKey();
        return (bool) self::$key;
    }

    public static function isActive() {
        if (!self::getCard())
            return false;

        return self::$card['is_active'];
    }

    public static function updateSession() {
        $account = null;
        if(!empty($_SESSION['beans_account'])) $account = $_SESSION['beans_account'];
        if (!$account)
            return;
        try {
            $account = self::API()->get('account/'.$account['id']);
            $_SESSION['beans_account'] = $account;
        } catch (\Beans\Error\BaseError $e) {
            self::log('Unable to get account: '.$e->getMessage());
        }
    }

    public static function flushRedemption() {

        self::get_cart()->remove_coupon(BEANS_COUPON_UID);

        unset($_SESSION['beans_coupon']);
        unset($_SESSION['beans_debit']);
    }

    private static function getOAuthConsumer(){

        Helper::log('Retrieving API Access');

        global $wpdb;

        /* In the past key oauth_consumer, but it has been compromised after bad synchronisation */
        $consumer_id = Helper::getConfig('oauth_consumer_connect');
        if($consumer_id) {
            $consumer = $wpdb->get_row( $wpdb->prepare( "
                SELECT key_id, user_id, description, permissions,
                consumer_key, consumer_secret, truncated_key, last_access
                FROM {$wpdb->prefix}woocommerce_api_keys
                WHERE key_id = %d
            ", $consumer_id ), ARRAY_A );

            if($consumer)
                return array();
        }

        if (!current_user_can('manage_woocommerce'))
            return array();

        $consumer_key    = 'ck_' . wc_rand_hash();
        $consumer_secret = 'cs_' . wc_rand_hash();

        $consumer = array(
            'user_id'         => get_current_user_id(),
            'description'     => 'Beans Connect',
            'permissions'     => 'read_write',
            'consumer_key'    => wc_api_hash( $consumer_key ),
            'consumer_secret' => $consumer_secret,
            'truncated_key'   => substr( $consumer_key, -7 )
        );

        $wpdb->insert(
            $wpdb->prefix . 'woocommerce_api_keys',
            $consumer,
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );

        $key_id = $wpdb->insert_id;

        Helper::setConfig('oauth_consumer_connect', $key_id);
        $consumer['consumer_key'] = $consumer_key ;
        return $consumer;
    }
/*
    public static function synchronise(){

        Helper::log('Launching synchronization');

        $estimated_account = 0;
        $contact_data = array();

        $users_count = count_users();
        if(isset($users_count['avail_roles']['customer'])){
            $estimated_account = $users_count['avail_roles']['customer'];
        }

        if (current_user_can('manage_woocommerce')){
            $admin = wp_get_current_user();
            $contact_data = array(
                'email' => $admin->user_email,
                'last_name' => $admin->user_lastname,
                'first_name' => $admin->user_firstname,
            );
        }

        $consumer_data = self::getOAuthConsumer();
//        $consumer_data = array();

        $country_code = get_option('woocommerce_default_country');
        if($country_code && strpos($country_code, ':') !== false){
            try {
                $country_parts = explode( ':', $country_code );
                $country_code = $country_parts[0];
            }catch(\Exception $e){}
        }

        $data = array(
            'website' => get_site_url(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'company_name' => get_bloginfo('name'),
            'store_name' => get_bloginfo('name'),
            'country_code' => $country_code,
            'contact' => $contact_data,
            'estimated_account' => $estimated_account,
            'rest_consumer' => $consumer_data,
            'rest_url' => get_site_url().'/wp-json/wc/v1/',
            'php_version' => phpversion(),
            'shop_version' => self::plugin_version('woocommerce'),
            'plugin_version' => BEANS_VERSION,
        );

        try{
            $response = self::API()->post('hook/integrations/woocommerce/synchronise', $data);
            if(isset($response['result'])) return $response['result'];
        }catch (\Exception $e) {
            self::log('Unable to install: '.$e->getMessage());
        }

        return false;
    }
*/
    public static function plugin_version($plugin_name = 'woocommerce') {
        if (!function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugin_folder = get_plugins("/$plugin_name");
        return $plugin_folder["$plugin_name.php"]['Version'];
    }

    public static function get_cart(){
        global $woocommerce;

        if(!empty($woocommerce->cart) && empty($woocommerce->cart->cart_contents))
            $woocommerce->cart->calculate_totals();

        return $woocommerce->cart;
    }

}
