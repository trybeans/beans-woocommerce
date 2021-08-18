<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

defined('ABSPATH') or die;

class Router
{
    const MENU_SLUG = 'beans-woo'; // private
    const TAB_INSPECT = 'inspect'; // public
    const TAB_CONNECT = 'connect'; // public
    const TAB_SETTINGS = 'settings'; // public
    private static $_messages_info = array();
    private static $_messages_error = array();

    public static function init()
    {
        add_action('admin_menu', array(__CLASS__, 'registerRoutes'));
    }

    public static function registerRoutes()
    {
        if (current_user_can('manage_options')) {
            add_menu_page(
                'Beans',
                'Beans',
                'manage_options',
                self::MENU_SLUG,
                array(__CLASS__, 'renderAdminPage'),
                Helper::getAssetURL('/assets/img/beans-wordpress-icon.svg'),
                56
            );
        }
    }

    public static function alert($level, $message)
    {
        if ($level === 'info') {
            self::$_messages_info[] = $message;
        } else {
            self::$_messages_error[] = $message;
        }
    }

    private static function renderNotices()
    {

        if (self::$_messages_error) {
            ?>
            <div class="error">
                <ul>
                    <?php foreach (self::$_messages_error as $msg) {
                        echo "<li>$msg</li>";
                    } ?>
                </ul>
            </div>
            <?php
        }
        if (self::$_messages_info) {
            ?>
            <div class="error">
                <ul>
                    <?php foreach (self::$_messages_error as $msg) {
                        echo "<li>$msg</li>";
                    } ?>
                </ul>
            </div>
            <?php
        }
    }

    public static function getTabURL($tab = '')
    {
        $page = self::MENU_SLUG;
        return admin_url("?page=${page}&tab=${tab}");
    }

    public static function renderAdminPage()
    {
        $tab = null;
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        }

        if (isset($_GET['reset_beans'])) {
            if (Helper::resetSetup()) {
                return wp_redirect(self::getTabURL(self::TAB_INSPECT));
            }
        }

        if (isset($_GET['card']) && isset($_GET['token'])) {
            if (Connector::processSetup()) {
                return wp_redirect(self::getTabURL(self::TAB_SETTINGS));
            }
        }

        self::renderNotices();

        /* For authenticated retailers */

        if (Helper::isSetup()) {
            if ($tab === self::TAB_SETTINGS) {
                return include(dirname(__FILE__) . '/Connector/connector-settings.html.php');
            }
            return wp_redirect(self::getTabURL(self::TAB_SETTINGS));
        }

        /* For non authenticated retailers */

        if ($tab === self::TAB_CONNECT) {
            return include(dirname(__FILE__) . '/Connector/connector-connect.html.php');
        }

        if ($tab === self::TAB_INSPECT) {
            return include(dirname(__FILE__) . '/Inspector/inspector-debug.html.php');
        }

        return wp_redirect(self::getTabURL(self::TAB_INSPECT));
    }
}
