<?php

namespace BeansWoo\Server;

use Beans\BeansError;
use BeansWoo\Helper;

/**
 * Override System Status Hooks and Rest API
 *
 * @class SystemHookController
 * @since 3.3.0
 */
class SystemHookController
{
    /**
     * Initialize controller.
     *
     * @return void
     *
     * @since 3.3.0
     */
    public static function init()
    {
        add_filter('woocommerce_rest_prepare_system_status', array(__CLASS__, 'appendPages'), 90, 2);

        if (Helper::isSetup()) {
            add_filter('woocommerce_webhook_deliver_async', array(__CLASS__, 'setWebhookDeliverMode'), 10, 3);
        }
    }

    public static function setWebhookDeliverMode($true, $instance, $arg)
    {
        $topics = [
            'order.created',
            'customer.created',
        ];
        if (in_array($instance->get_topic(), $topics)) {
            return false;
        }
        return true;
    }

    public static function appendPages($response, $system_status)
    {
        $pages = [
            get_option('woocommerce_myaccount_page_id') => array(
                'path' => wc_get_page_permalink('myaccount'),
                'type' => 'login'
            ),
            get_option('woocommerce_cart_page_id') => array(
                'path' => wc_get_cart_url(),
                'type' => 'cart',
            ),
            get_option('woocommerce_shop_page_id') => array(
                'path' => wc_get_page_permalink('shop'),
                'type' => 'product',
            ),
            get_option('woocommerce_checkout_page_id') => array(
                'path' => wc_get_checkout_url(),
                'type' => 'checkout'
            ),
        ];

        foreach ($pages as &$link) {
            $link['path'] = str_replace(home_url(), '', $link['path']);
        }

        if (!isset($response->data['pages'])) {
            $response->data['pages'] = [];
        }

        foreach ($response->data['pages'] as &$page) {
            if (isset($pages[$page['page_id']])) {
                $page = array_merge($page, $pages[$page['page_id']]);
            }
        }

        $response->data['pages'] = array_merge($response->data['pages'], self::getBeansPages(), array(
                array(
                    'page_name' => 'Thank you',
                    'path' => str_replace(
                        home_url(),
                        '',
                        wc_get_endpoint_url(
                            get_option('woocommerce_checkout_order_received_endpoint'),
                            '',
                            wc_get_checkout_url()
                        )
                    ),
                    'type' => 'thank_you'
                )));

        return $response;
    }

    private static function getBeansPages()
    {
        $pages_output = [];

        foreach (Helper::getBeansPages() as $app_name => $values) {
            $page_id = $values['page_id'];
            $page_visible = false;

            $page_set = true;
            if ('publish' === get_post_status($page_id)) {
                $page_visible = true;
            }

            $pages_output[] = array(
                'type' => $values['type'],
                'page_id' => $page_id,
                'page_set' => $page_set,
                'page_name' => $values['page_name'],
                'page_exists' => $values['page_exists'],
                'page_visible' => $page_visible,
                'shortcode_present' => true,
                'shortcode_required' => true,
                'shortcode' => $values['shortcode'],
                'path' => str_replace(home_url(), '', get_permalink(Helper::getConfig($app_name . '_page'))),
            );
        }

        return $pages_output;
    }
}
