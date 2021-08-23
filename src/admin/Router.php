<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

defined('ABSPATH') or die;

class Router
{
    const MENU_SLUG = 'beans-woo'; // private
    const NOTICE_KEY = 'beans_ultimate_notice_dismissed'; // private
    const TAB_INSPECT = 'inspect'; // public
    const TAB_CONNECT = 'connect'; // public
    const TAB_SETTINGS = 'settings'; // public
    private static $_messages_info = array();
    private static $_messages_error = array();

    public static function init()
    {
        add_action('admin_menu', array(__CLASS__, 'registerRoutes'));
        add_action('admin_notices', array(__CLASS__, 'displayAdminNotice'));

        add_filter("plugin_action_links_" . BEANS_PLUGIN_FILENAME, array(__CLASS__, 'addPluginActionLinks' ), 10, 1);
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

    private static function renderPageNotices()
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

        if (isset($_GET[self::NOTICE_KEY])) {
            if (self::discardAdminNotice()) {
                return wp_safe_redirect($_SERVER['HTTP_REFERER']);
            }
        }

        self::loadPageStyle();

        self::renderPageNotices();

        /* For authenticated retailers */

        if (Helper::isSetup()) {
            if ($tab === self::TAB_SETTINGS) {
                return include(dirname(__FILE__) . '/Connect/connector-settings.html.php');
            }
            return wp_redirect(self::getTabURL(self::TAB_SETTINGS));
        }

        /* For non authenticated retailers */

        if ($tab === self::TAB_CONNECT) {
            return include(dirname(__FILE__) . '/Connect/connector-connect.html.php');
        }

        if ($tab === self::TAB_INSPECT) {
            return include(dirname(__FILE__) . '/Inspector/inspector-debug.html.php');
        }

        return wp_redirect(self::getTabURL(self::TAB_INSPECT));
    }

    public static function loadPageStyle()
    {
        wp_enqueue_style(
            'admin-styles',
            Helper::getAssetURL('assets/css/beans-admin.css')
        );
    }

    public static function addPluginActionLinks($links)
    {
        $row_meta = array(
            'settings' => '<a href=' . self::getTabURL(self::TAB_SETTINGS) . '>Settings</a>',
            'help' => '<a href="http://help.trybeans.com/" target="_blank" title="Help">Help Center</a>',
            'support' => '<a href="mailto:hello@trybeans.com" title="Support">Contact Support</a>',
        );

        return array_merge($links, $row_meta);
    }

    public static function displayAdminNotice()
    {
        $user_id = get_current_user_id();
        if (get_user_meta($user_id, self::NOTICE_KEY)) {
            return;
        }

        if (!Helper::isSetup()) {
            ?>
            <div class="notice notice-error" style="margin-left: auto">
                <div style="margin: 10px auto;">
                Beans: <?=__("Beans Ultimate is not properly setup", 'beans-woo');?>
                <a href="<?=self::getTabURL(self::TAB_INSPECT)?>"><?=__('Set up', 'beans-woo')?></a>
                <a href="<?=self::getTabURL()?>&<?=self::NOTICE_KEY?>=1" 
                    style="float:right; text-decoration: none">
                    x
                </a>
                </div>
            </div>
            <?php
        }
    }

    private static function discardAdminNotice()
    {
        $user_id = get_current_user_id();
        add_user_meta(
            $user_id,
            self::NOTICE_KEY,
            'true',
            true
        );
        return true;
    }
}
