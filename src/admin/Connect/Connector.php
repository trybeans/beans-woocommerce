<?php

namespace BeansWoo\Admin;

use Beans\BeansError;
use BeansWoo\Helper;
use BeansWoo;

class Connector
{
    public static function init()
    {
        if (Helper::isSetup()) {
            add_action('admin_init', array(__CLASS__, 'registerSettingOptions'));
        }
    }

    public static function registerSettingOptions()
    {
        add_settings_section("beans-section", "", null, "beans-woo");

        foreach (BeansWoo\PREFERENCES_META as $key => $params) {
            if ($params['type'] == 'boolean') {
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

    /**
     * Create the Rewards and Referral pages if they do not exist.
     *
     * @return void
     *
     * @since 3.3.0
     */
    public static function setupPages()
    {
        self::installAssets('liana');
        self::installAssets('bamboo');
    }

    /**
     * Create the Rewards and Referral pages.
     *
     * @param string $app_name:
     * @return void
     *
     * @since 3.3.0
     */
    private static function installAssets($app_name = null)
    {
        $page_id = Helper::getConfig($app_name . '_page');
        if ($page_id && get_post($page_id)) {
            return true;
        }

        $page_infos = Helper::getBeansPages()[$app_name];

        $page_id = wc_create_page(
            $page_infos['slug'],
            $page_infos['option'],
            $page_infos['page_name'],
            $page_infos['shortcode'],
            0
        );
        Helper::setConfig($app_name . '_page', $page_id);
    }
}
