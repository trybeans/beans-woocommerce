<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

class Observer
{
    private static $_notice_dismissed_key = 'beans_ultimate_notice_dismissed';

    public static function init()
    {

        add_action('admin_enqueue_scripts', array(__CLASS__, 'loadAdminStyle'));
        add_action("admin_init", array(__CLASS__, "registerSettingOptions"));

        add_action('admin_notices', array(__CLASS__, 'adminNotice'));
        add_action('admin_init', array(__CLASS__, 'noticeDismissed'));

        add_action('admin_init', array(__CLASS__, 'checkCURLStatus'), 0, 99);
        add_filter("plugin_action_links_" . BEANS_PLUGIN_FILENAME, array(__CLASS__, 'addPluginSettingsLinks' ), 10, 1);

        if (current_user_can('administrator') and is_null(Helper::getConfig('is_admin_account'))) {
            do_action('woocommerce_new_customer', get_current_user_id());  // force customer webhook for admin
            Helper::setConfig('is_admin_account', true);
        }
    }

    public static function loadAdminStyle()
    {
        wp_enqueue_style(
            'admin-styles',
            plugins_url('assets/css/beans-admin.css', BEANS_PLUGIN_FILENAME)
        );
    }

    public static function registerSettingOptions()
    {
        add_settings_section("beans-section", "", null, "beans-woo");
        add_settings_field(
            "beans-liana-display-redemption-checkout",
            "Redemption on checkout",
            array(__CLASS__, "displayRedeemCheckboxSettings"),
            "beans-woo",
            "beans-section"
        );
        register_setting("beans-section", "beans-liana-display-redemption-checkout");
    }

    public static function displayRedeemCheckboxSettings()
    {
        ?>
        <!-- Here we are comparing stored value with 1. Stored value is 1 if user
        checks the checkbox otherwise empty string. -->
        <div>
            <input type="checkbox"
                   id="beans-liana-display-redemption-checkout"
                   name="beans-liana-display-redemption-checkout"
                   value="1"
                <?php checked(1, get_option('beans-liana-display-redemption-checkout'), true); ?>
            />
            <label for="beans-liana-display-redemption-checkout">Display redemption on checkout page</label>
        </div>
        <?php
    }

    public static function checkCURLStatus()
    {
        $text = __(
            "cURL is not installed. Please install and activate, otherwise, the Beans program may not work.",
            'beans-woo'
        );
        if (! Helper::isCURL()) {
            ?>
            <div class="notice notice-warning" style="margin-left: auto">
                <div style="margin: 10px auto;"> Beans: <?php echo $text; ?></div>
            </div>
            <?php
        }
    }

    public static function adminNotice()
    {
        $user_id = get_current_user_id();
        if (get_user_meta($user_id, self::$_notice_dismissed_key)) {
            return;
        }

        if (! Helper::isSetup() || ! Helper::isSetupApp('ultimate')) {
            ?>
          <div class="notice notice-error" style="margin-left: auto">
            <div style="margin: 10px auto;">
              Beans: <?php echo __("Beans Ultimate is not properly setup", 'beans-woo'); ?>
              <a href="<?php echo BEANS_WOO_MENU_LINK; ?>"><?php echo __('Set up', 'beans-woo') ?></a>
              <a href="<?php echo self::$_notice_dismissed_key;?>"
                 style="float:right; text-decoration: none">
                x
              </a>
            </div>
          </div>
            <?php
        }
    }

    public static function noticeDismissed()
    {
        $user_id = get_current_user_id();
        if (isset($_GET[self::$_notice_dismissed_key])) {
            add_user_meta(
                $user_id,
                self::$_notice_dismissed_key,
                'true',
                true
            );
            $location = $_SERVER['HTTP_REFERER'];
            wp_safe_redirect($location);
        }
    }

    public static function addPluginSettingsLinks($links)
    {
        $row_meta = array(
            'help' => '<a href="http://help.trybeans.com/" target="_blank" title="Help">Help Center</a>',
            'support' => '<a href="mailto:hello@trybeans.com" title="Support">Contact Support</a>',
            'settings' => '<a href=' . BEANS_WOO_MENU_LINK . '>Settings</a>'
        );

        return array_merge($links, $row_meta);
    }
}
