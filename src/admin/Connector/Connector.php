<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

class Connector
{

    public static $app_name = 'ultimate';
    public static $card ;

    public static $errors = array();
    public static $messages = array();

    public static function init()
    {
        self::$card = Helper::getBeansObject(self::$app_name, 'card');
        self::updateInstalledApps();
        add_action('admin_init', array(__CLASS__, 'installDefaultAssets'));
    }

    protected static function _installAssets($app_name = null)
    {
        if (!in_array($app_name, ['liana', 'bamboo'])) {
            return false;
        }
        $name = $app_name;
        // Install Page
        $page_infos = Helper::getPages()[$name];

        if (! get_post(Helper::getConfig($name . '_page'))) {
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

    public static function renderSettingsPage()
    {

        if (isset($_GET['card']) && isset($_GET['token'])) {
            if (self::_processSetup()) {
                return wp_redirect(BEANS_WOO_MENU_LINK);
            }
        }

        if (isset($_GET['reset_beans'])) {
            if (Helper::resetSetup()) {
                return wp_redirect(BEANS_WOO_MENU_LINK);
            }
        }

        self::_renderNotices();

        if (Helper::isSetup() && Helper::isSetupApp(static::$app_name)) {
            self::updateInstalledApps();
            return include(dirname(__FILE__) . '/connector-settings.html.php');
        }

        return include(dirname(__FILE__, 2) . '/Inspector/inspector-debug.html.php');
    }

    protected static function _processSetup()
    {
        Helper::log(print_r($_GET, true));

        $card  = $_GET['card'];
        $token = $_GET['token'];

        Helper::$key = $card;

        try {
            $integration_key = Helper::API()->get('/core/auth/integration_key/' . $token);
        } catch (\Beans\Error\BaseError  $e) {
            self::$errors[] = 'Connecting to Beans failed with message: ' . $e->getMessage();
            Helper::log('Connecting failed: ' . $e->getMessage());

            return null;
        }

        Helper::setConfig('key', $integration_key['id']);
        Helper::setConfig('card', $integration_key['card']['id']);
        Helper::setConfig('secret', $integration_key['secret']);
        Helper::setInstalledApp(self::$app_name);
        return true;
    }

    public static function _renderNotices()
    {
        if (self::$errors || self::$messages) {
            ?>
            <div class="<?php echo empty(self::$errors) ? "updated" : "error"; ?> ">
                <?php if (self::$errors) : ?>
                    <ul>
                        <?php foreach (self::$errors as $error) {
                            echo "<li>$error</li>";
                        } ?>
                    </ul>
                <?php else : ?>
                    <ul>
                        <?php foreach (self::$messages as $message) {
                            echo "<li>$message</li>";
                        } ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
        }
    }

    public static function adminNotice()
    {
        $user_id = get_current_user_id();
        if (get_user_meta($user_id, 'beans_' . static::$app_name . '_notice_dismissed')) {
            return;
        }

        if (! Helper::isSetup() || ! Helper::isSetupApp(static::$app_name)) {
            ?>
                <div class="notice notice-error" style="margin-left: auto">
                    <div style="margin: 10px auto;">
                        Beans: <?php echo __("Beans Ultimate is not properly setup", 'beans-woo'); ?>
                        <a href="<?php echo BEANS_WOO_MENU_LINK; ?>"><?php echo __('Set up', 'beans-woo') ?></a>
                        <a href="?beans_<?php echo static::$app_name?>._notice_dismissed"
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
        if (isset($_GET['beans_' . static::$app_name . '_notice_dismissed'])) {
            add_user_meta(
                $user_id,
                'beans_' . static::$app_name . '_notice_dismissed',
                'true',
                true
            );
            $location = $_SERVER['HTTP_REFERER'];
            wp_safe_redirect($location);
        }
    }

    public static function updateInstalledApps()
    {
        if (! is_array(self::$card)) {
            return;
        }

        foreach (self::$card['apps'] as $app => $status) {
            $app = strtolower($app);
            if ($status['is_installed']) {
                self::_installAssets($app);
                Helper::setInstalledApp($app);
            }
        }
    }

    public static function installDefaultAssets()
    {
        self::_installAssets('liana');
        self::_installAssets('bamboo');
    }
}
