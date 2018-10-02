<?php

namespace BeansWoo;

class Observer {

    public static function init(){

        if (!Helper::isActive())
            return;

        add_filter('wp_logout',                                     array(__CLASS__, 'clearSession'),      10, 1);
        add_filter('wp_login',                                      array(__CLASS__, 'customerLogin'),     10, 2);
        add_filter('user_register',                                 array(__CLASS__, 'customerRegister'),  10, 1);
        add_filter('profile_update',                                array(__CLASS__, 'customerRegister'),  10, 1);
        add_filter('wp_loaded',                                     array(__CLASS__, 'form_post_handler'), 30, 1);
        add_filter('woocommerce_get_shop_coupon_data',              array(__CLASS__, 'get_coupon'),        10, 2);
        add_filter('woocommerce_checkout_order_processed',          array(__CLASS__, 'orderPlaced'),       10, 1);
        add_filter('woocommerce_order_status_changed',              array(__CLASS__, 'orderPaid'),         10, 3);
//        add_filter('woocommerce_update_cart_action_cart_updated',   array(__CLASS__, 'cancel_redemption'),10, 1);
    }

    public static function clearSession(){
        unset($_SESSION['beans_token']);
        unset($_SESSION['beans_account']);
        unset($_SESSION['beans_coupon']);
        unset($_SESSION['beans_debit']);
    }

    public static function createBeansAccount($email, $firstname, $lastname) {
        try {
            return Helper::API()->post('account', array(
                'email'      => $email,
                'first_name' => $firstname,
                'last_name'  => $lastname,
            ));
        } catch (\Beans\Error\BaseError $e) {
            Helper::log('Unable to create account: '.$e->getMessage());
        }
        return null;
    }

    public static function customerLogin($user_login, $user) {
        self::customerRegister($user->ID);
    }

    public static function customerRegister($user_id) {

        $user_data = get_userdata($user_id);

        $first_name = get_user_meta( $user_id, 'first_name', true );
        if(!$first_name && isset($_POST['first_name'])) $first_name = $_POST['first_name'];

        $last_name = get_user_meta( $user_id, 'last_name', true );
        if(!$last_name && isset($_POST['last_name'])) $last_name = $_POST['last_name'];

        $email = $user_data->user_email;

        if($email){
            $account  = self::createBeansAccount($email, $first_name, $last_name);
            $_SESSION['beans_account'] = $account;
            if($account){
                $_SESSION['beans_token'] = Helper::API()->post('consumer_token', array(
                    'account'     => $account['id']
                ));
            }
        }
    }

    public static function form_post_handler() {


        if(!isset($_POST['beans_action'])) return;

        $action = $_POST['beans_action'];

        if($action == 'apply'){
            self::apply_redemption();
        }else {
            Helper::flushRedemption();
        }
    }

    private static function apply_redemption(){

        if(!isset($_SESSION['beans_account'])) return;

        Helper::flushRedemption();
        Helper::updateSession();

        $account = $_SESSION['beans_account'];

        $cart = Helper::get_cart();
        $card = Helper::getCard();

        $max_amount = $cart->subtotal;
        if(isset($card['settings']) && isset($card['settings']['range_max_redeem'])){
            $percent_discount = $card['settings']['range_max_redeem'];
            if($percent_discount < 100){
                $max_amount = (1.0*$cart->subtotal*$percent_discount)/100;
                wc_add_notice("Maximum discount allowed for this order is $percent_discount%", 'notice');
            }
        }

        $amount = min($max_amount, $account['beans_value']);
        $amount = sprintf('%0.2f', $amount);

        $_SESSION['beans_debit'] = array(
            'beans' => $amount * $card['beans_rate'],
            'value' => $amount
        );
        $cart->add_discount(BEANS_COUPON_UID);
    }

    public static function get_coupon($coupon, $coupon_code){

        if( $coupon_code != BEANS_COUPON_UID)                       return $coupon;
        if( !isset($_SESSION['beans_account']) ||
            !isset($_SESSION['beans_account']['beans']) )           return $coupon;
        if( !isset($_SESSION['beans_debit']) ||
            !isset($_SESSION['beans_debit']['beans']) )             return $coupon;

        if(isset($_SESSION['beans_coupon']) &&
           $_SESSION['beans_coupon'])                               return $_SESSION['beans_coupon'];

        $cart = Helper::get_cart();
        if(empty($cart))                                            return $coupon;

        $quantity = $_SESSION['beans_debit']['beans'];
        $amount = $_SESSION['beans_debit']['value'];

        $coupon_data = array();

        $coupon_data['id']                         = true;
        $coupon_data['individual_use']             = null;
        $coupon_data['product_ids']                = null;
        $coupon_data['exclude_product_ids']        = null;
        $coupon_data['usage_limit']                = null;
        $coupon_data['usage_limit_per_user']       = null;
        $coupon_data['limit_usage_to_x_items']     = null;
        $coupon_data['usage_count']                = null;
        $coupon_data['apply_before_tax']           = null;
        $coupon_data['discount_cart_tax']          = null;
        $coupon_data['free_shipping']              = null;
        $coupon_data['product_categories']         = null;
        $coupon_data['exclude_product_categories'] = null;
        $coupon_data['exclude_sale_items']         = null;
        $coupon_data['minimum_amount']             = null;
        $coupon_data['maximum_amount']             = null;
        $coupon_data['customer_email']             = null;

        $coupon_data['type']                       = 'fixed_cart';
        $coupon_data['discount_type']              = 'fixed_cart';
        $coupon_data['amount']                     = $amount;
        $coupon_data['coupon_amount']              = $amount;
        $coupon_data['expiry_date']                = strtotime('+1 day', time());

        $_SESSION['beans_coupon'] = $coupon_data;

        return $coupon_data;
    }

    public static function orderPlaced($order_id) {
        $order = new \WC_Order($order_id);

        $account = null;

        if(isset($_SESSION['beans_account'])) $account = $_SESSION['beans_account'];

        $coupon_codes = $order->get_used_coupons();

        foreach ($coupon_codes as $code) {

            if ($code === BEANS_COUPON_UID) {

                if(!$account) throw new \Exception('Trying to redeem beans without beans account.');

                $coupon = new \WC_Coupon($code);

                $amount = (double) (property_exists($coupon, 'coupon_amount') ? $coupon->coupon_amount : $coupon->amount);
                $amount = sprintf('%0.2f', $amount);
                $amount_str = sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $amount);

                $data = array(
                    'quantity'      => $amount,
                    'rule'          => strtoupper(get_woocommerce_currency()),
                    'account'       => $account['id'],
                    'description'   => "Debited for a $amount_str discount",
                    'uid'           => 'wc_'.$order->id . '_' . $order->order_key,
                    'commit'        => true
                );


                try {
                    $debit = Helper::API()->post('debit', $data);
                } catch (\Beans\Error\BaseError $e) {
                    if($e->getCode() != 409) {
                        Helper::log('Debiting failed: ' . $e->getMessage());
                        throw new \Exception('Beans debit failed: ' . $e->getMessage());
                    }
                }

            }
        }

        Helper::flushRedemption();
        Helper::updateSession();
    }

    public static function orderPaid($order_id, $old_status, $new_status) {
        $order = new \WC_Order($order_id);
        $account = null;

        try {
            $account = Helper::API()->get('account/' . $order->billing_email);
        } catch (\Beans\Error\ValidationError $e) {
            if ($e->getCode() == 404 && $order->customer_user) {
                $account = self::createBeansAccount($order->billing_email, $order->billing_first_name, $order->billing_last_name);
            }
            else {
                Helper::log('Looking for Beans account for crediting failed with message: ' . $e->getMessage());
            }
        } catch (\Beans\Error\BaseError $e) {
            Helper::log('Looking for Beans account for crediting failed with message: ' . $e->getMessage());
        }

        if (!$account) return;

        $total = $order->get_total() - $order->get_total_shipping();
        $total = sprintf('%0.2f', $total);

        if($new_status=='processing' || $new_status=='completed'){

            try {
                $credit = Helper::API()->post('credit', array(
                    'account'     => $account['id'],
                    'quantity'    => $total,
                    'rule'        => 'beans:currency_spent',
                    'uid'         => 'wc_'.$order->id . '_' . $order->order_key,
                    'description' => 'Customer loyalty rewarded for order #' . $order->id,
                    'commit'      => true
                ));
            } catch (\Beans\Error\BaseError $e) {
                if($e->getCode() != 409)
                    Helper::log('Crediting failed with message: ' . $e->getMessage());
            }
        }
        else if($new_status == 'cancelled'){
            $order_key = 'wc_'.$order->id . '_' . $order->order_key;
            try {
                Helper::API()->post("debit/$order_key/cancel");
            } catch (\Beans\Error\BaseError $e) {
                Helper::log('Cancelling debit failed with message: ' . $e->getMessage());
            }
            try {
                Helper::API()->post("credit/$order_key/cancel");
            } catch (\Beans\Error\BaseError $e) {
                Helper::log('Cancelling credit failed with message: ' . $e->getMessage());
            }
        }
        Helper::updateSession();
    }

}
