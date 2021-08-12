<?php

namespace BeansWoo\StoreFront;

defined('ABSPATH') or die;

require_once "base/Scripts/Scripts.php";
require_once "base/Registration/Registration.php";

require_once "arrow/Login/ArrowLogin.php";

require_once "bamboo/Page/BambooPage.php";

require_once "liana/Observer/LianaObserver.php";
require_once "liana/Observer/LianaProductObserver.php";
require_once "liana/Cart/LianaCart.php";
require_once "liana/Page/LianaPage.php";


use BeansWoo\Helper;


class Main
{
    public static function init()
    {
        Scripts::init();
        Registration::init();

        ArrowLogin::init();

        BambooPage::init();

        LianaPage::init();
        LianaCart::init();

        $display = Helper::getBeansObject('liana', 'display');
        if (empty($display) || ! $display['is_active']) {
            return;
        }

        LianaObserver::init($display);
        LianaProductObserver::init($display);
    }
}
