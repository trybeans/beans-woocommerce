<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

defined('ABSPATH') or die;

require_once "Observer/Observer.php";
require_once "Connector/Connector.php";
require_once "Inspector/Inspector.php";

class Main
{

    public static function init()
    {
        if (!is_admin()) {
            return;
        }

        add_action('admin_menu', array(__CLASS__, 'registerAdminMenu'));

        Connector::init();
        Observer::init();
    }

    public static function registerAdminMenu()
    {
        if (current_user_can('manage_options')) {
            add_menu_page(
                'Beans',
                'Beans',
                'manage_options',
                BEANS_WOO_BASE_MENU_SLUG,
                array(__CLASS__, 'renderSettingsPage'),
                plugins_url('/assets/img/beans-wordpress-icon.svg', BEANS_PLUGIN_FILENAME),
                56
            );
        }
    }

    public static function renderSettingsPage()
    {

        if (isset($_GET['reset_beans'])) {
            if (Helper::resetSetup()) {
                return wp_redirect(BEANS_WOO_MENU_LINK);
            }
        }

        if (isset($_GET['card']) && isset($_GET['token'])) {
            if (Connector::processSetup()) {
                return wp_redirect(BEANS_WOO_MENU_LINK);
            }
        }

        Connector::renderNotices();

        if (Helper::isSetup() && Helper::isSetupApp(Connector::$app_name)) {
            Connector::updateInstalledApps();
            return include(dirname(__FILE__) . '/Connector/connector-settings.html.php');
        }

        return include(dirname(__FILE__) . '/Inspector/inspector-debug.html.php');
    }
}
