<?php

namespace BeansWoo\Admin;

use Beans\BeansError;
use BeansWoo\Helper;

class Connector
{
    public static function init()
    {
        if (Helper::isSetup()) {
            add_action('admin_init', array(__CLASS__, 'registerSettingOptions'));
        }
    }

    public static function processSetup()
    {
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

        Helper::setConfig('card', $card_id);
        Helper::setConfig('key', $integration_key['id']);
        Helper::setConfig('secret', $integration_key['access_token']);

        return true;
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

    public static function registerSettingOptions()
    {
        add_settings_section("beans-section", "", null, "beans-woo");

        foreach (Helper::OPTIONS as $key => $params) {
            add_settings_field(
                $params['handle'],
                $params['label'],
                array(__CLASS__, "displayOption"),
                "beans-woo",
                "beans-section",
                $params
            );
            register_setting("beans-section", $params['handle']);
        }
    }

    public static function displayOption($args)
    {
        ?>
      <!-- Here we are comparing stored value with 1. Stored value is 1 if user
      checks the checkbox otherwise empty string. -->
      <div>
        <input type="checkbox" id="<?=$args['handle']?>" name="<?=$args['handle']?>" value="1"
            <?php checked(1, get_option($args['handle']), true); ?>
        />
        <label for="<?=$args['handle']?>"><?=$args['help_text']?></label>
      </div>
        <?php
    }

    public static function setupPages()
    {
        self::installAssets('liana');
        self::installAssets('bamboo');
    }
}
