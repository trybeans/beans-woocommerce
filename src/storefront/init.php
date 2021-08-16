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
require_once "liana/Observer/LianaCartObserver.php";
require_once "liana/Observer/LianaProductObserver.php";
require_once "liana/Cart/LianaCart.php";
require_once "liana/Page/LianaPage.php";


use Beans\BeansError;
use BeansWoo\Helper;


class Main
{
    public static function init()
    {
        Scripts::init();
        Registration::init();
        Auth::init();

        ArrowLogin::init();

        BambooPage::init();

        LianaPage::init();
        LianaCart::init();


        try {
            $display = Helper::requestTransientAPI('GET', 'liana/display/current');
        } catch (BeansError $e) {
            Helper::log('Unable to retrieve display: ' . $e->getMessage());
            return;
        }

        if (!$display['is_active']) {
            Helper::log('Display is deactivated');
            return;
        }

        LianaCartObserver::init($display);
        LianaProductObserver::init($display);
    }
}
