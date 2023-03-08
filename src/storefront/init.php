<?php

namespace BeansWoo\StoreFront;

defined('ABSPATH') or die;

require_once "base/BeansAccount.php";
require_once "base/Auth/Auth.php";
require_once "base/Scripts/Scripts.php";
require_once "base/Registration/Registration.php";

require_once "liana/Observer/LianaObserver.php";
require_once "liana/Observer/LianaAjaxObserver.php";
require_once "liana/Observer/LianaCartObserver.php";
require_once "liana/Observer/LianaProductObserver.php";
require_once "liana/Observer/LianaProductAjaxObserver.php";
require_once "liana/Observer/LianaLifetimeDiscountObserver.php";

require_once "liana/Views/LianaBlocks.php";
require_once "liana/Views/LianaPage.php";
require_once "bamboo/Views/BambooBlocks.php";
require_once "bamboo/Views/BambooPage.php";

use BeansWoo\Helper;


class Main
{
    public static function init()
    {
        Registration::init();

        if (!Helper::isSetup()) {
            return;
        }

        if (! session_id()) {
            session_start();
        }

        if (isset($_GET['beans-mode'])) {
            $_SESSION['beans_mode'] = $_GET['beans-mode'];
        }

        Scripts::init();
        Auth::init();

        $display_liana = Helper::requestTransientAPI('GET', 'liana/display/current');
        $display_bamboo = Helper::requestTransientAPI('GET', 'bamboo/display/current');

        BambooPage::init($display_bamboo);
        BambooBlocks::init($display_bamboo);

        LianaPage::init($display_liana);

        if (empty($display_liana)) {
            Helper::log('Display is empty');
            return;
        }

        // Either the rewards program is active, or we are in live test mode
        if (!$display_liana['is_active'] && !isset($_SESSION['beans_mode'])) {
            Helper::log('Display is deactivated');
            return;
        }

        LianaBlocks::init($display_liana);

        LianaCartObserver::init($display_liana);
        LianaLifetimeDiscountObserver::init($display_liana);
        LianaProductObserver::init($display_liana);
    }

    public static function initAjax()
    {
        if (!Helper::isSetup()) {
            return;
        }

        if (! session_id()) {
            session_start();
        }

        LianaAjaxObserver::init(null);

        $display_liana = Helper::requestTransientAPI('GET', 'liana/display/current');

        if (empty($display_liana)) {
            Helper::log('Ajax: Display is empty');
            return;
        }

        if (!$display_liana['is_active']) {
            Helper::log('Ajax: Display is deactivated');
            return;
        }
        LianaProductAjaxObserver::init($display_liana);
    }
}
