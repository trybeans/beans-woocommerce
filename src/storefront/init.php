<?php

namespace BeansWoo\StoreFront;

defined('ABSPATH') or die;

require_once "base/BeansAccount.php";
require_once "base/Auth/Auth.php";
require_once "base/Scripts/Scripts.php";
require_once "base/Registration/Registration.php";

require_once "arrow/Login/ArrowLogin.php";

require_once "bamboo/Page/BambooPage.php";

require_once "liana/Observer/LianaObserver.php";
require_once "liana/Observer/LianaAjaxObserver.php";
require_once "liana/Observer/LianaCartObserver.php";
require_once "liana/Observer/LianaProductObserver.php";
require_once "liana/Observer/LianaProductAjaxObserver.php";
require_once "liana/Observer/LianaLifetimeDiscountObserver.php";

require_once "liana/Cart/LianaCart.php";
require_once "liana/Page/LianaPage.php";


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

        Scripts::init();
        Auth::init();

        ArrowLogin::init();

        $display_liana = Helper::requestTransientAPI('GET', 'liana/display/current');
        $display_bamboo = Helper::requestTransientAPI('GET', 'bamboo/display/current');

        BambooPage::init($display_bamboo);

        LianaPage::init($display_liana);
        LianaCart::init();

        if (empty($display_liana)) {
            Helper::log('Display is empty');
            return;
        }

        if (!$display_liana['is_active']) {
            Helper::log('Display is deactivated');
            return;
        }

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

        $display = Helper::requestTransientAPI('GET', 'liana/display/current');

        if (empty($display)) {
            Helper::log('Ajax: Display is empty');
            return;
        }

        if (!$display['is_active']) {
            Helper::log('Ajax: Display is deactivated');
            return;
        }
        LianaProductAjaxObserver::init($display);
    }
}
