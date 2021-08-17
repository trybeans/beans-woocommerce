<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

defined('ABSPATH') or die;

require_once "Observer/Observer.php";
require_once "Connector/Connector.php";
require_once "Inspector/Inspector.php";

class Router
{
    private const MENU_SLUG = 'beans-woo';
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
                plugins_url('/assets/img/beans-wordpress-icon.svg', BEANS_PLUGIN_FILENAME),
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
        return admin_url('?page=' . self::MENU_SLUG);
    }

    public static function renderAdminPage()
    {

        if (isset($_GET['reset_beans'])) {
            if (Helper::resetSetup()) {
                return wp_redirect(self::getTabURL());
            }
        }

        if (isset($_GET['card']) && isset($_GET['token'])) {
            if (Connector::processSetup()) {
                return wp_redirect(self::getTabURL());
            }
        }

        self::renderNotices();

        if (Helper::isSetup()) {
            return include(dirname(__FILE__) . '/Connector/connector-settings.html.php');
        }

        return include(dirname(__FILE__) . '/Inspector/inspector-debug.html.php');
    }
}
