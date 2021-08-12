<?php

namespace BeansWoo\Admin\Observer;

use BeansWoo\Helper;

class Observer
{
    public static function init()
    {

        add_action('admin_enqueue_scripts', array(__CLASS__, 'loadAdminStyle'));
        add_action("admin_init", array(__CLASS__, "registerSettingOptions"));

        add_action('admin_notices', array('\BeansWoo\Admin\Connector\Connector', 'adminNotice'));
        add_action('admin_init', array('\BeansWoo\Admin\Connector\Connector', 'noticeDismissed'));

        add_action('admin_menu', array(__CLASS__, 'registerAdminMenu'));
        add_action('admin_init', array(__CLASS__, 'checkCURLStatus'), 0, 99);
    }

    public static function loadAdminStyle()
    {
        wp_enqueue_style(
            'admin-styles',
            BEANS_PLUGIN_URL . 'assets/css/beans-admin.css'
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
        <!-- Here we are comparing stored value with 1. Stored value is 1 if user checks the checkbox otherwise empty string. -->
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

    public static function registerAdminMenu()
    {
        if (current_user_can('manage_options')) {
            add_menu_page(
                'Beans',
                'Beans',
                'manage_options',
                BEANS_WOO_BASE_MENU_SLUG,
                array('\BeansWoo\Admin\Connector\Connector', 'renderSettingsPage'),
                BEANS_PLUGIN_URL . 'assets/img/beans-wordpress-icon.svg',
                56
            );
        }
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
}
