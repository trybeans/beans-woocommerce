<?php

namespace BeansWoo\Admin;

use Beans\BeansError;
use BeansWoo\Helper;

class Connector
{
    private static $_errors = array();
    private static $_messages = array();

    public static function init()
    {
        add_action('admin_init', array(__CLASS__, 'installDefaultAssets'));
    }

    private static function installAssets($app_name = null)
    {
        if (!in_array($app_name, ['liana', 'bamboo'])) {
            return false;
        }
        $name = $app_name;
        // Install Page
        $page_infos = Helper::getBeansPages()[$name];

        if (!get_post(Helper::getConfig($name . '_page'))) {
            require_once(WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php');
            $page_id = wc_create_page(
                $page_infos['slug'],
                $page_infos['option'],
                $page_infos['page_name'],
                $page_infos['shortcode'],
                0
            );
            Helper::setConfig($name . '_page', $page_id);
        }
        return true;
    }

    public static function processSetup()
    {
        Helper::log(print_r($_GET, true));

        $card_id = $_GET['card'];
        $token   = $_GET['token'];

        Helper::$key = $card_id;

        try {
            $integration_key = Helper::API()->get('core/auth/integration_key/' . $token);
        } catch (BeansError  $e) {
            Router::alert('error', 'Connecting to Beans failed with message: ' . $e->getMessage());
            Helper::log('Connecting failed: ' . $e->getMessage());

            return null;
        }

        Helper::setConfig('key', $integration_key['id']);
        Helper::setConfig('card', $integration_key['card']['id']);
        Helper::setConfig('secret', $integration_key['secret']);

        return true;
    }

    public static function installDefaultAssets()
    {
        self::installAssets('liana');
        self::installAssets('bamboo');
    }
}
