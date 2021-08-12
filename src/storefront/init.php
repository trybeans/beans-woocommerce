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

define('BEANS_LIANA_COUPON_UID', 'redeem_points');

use BeansWoo\Helper;


class Main
{
    public static function init()
    {
        Base\Scripts\Block::init();
        Base\Registration\Block::init();

        Arrow\Login\ArrowLogin::init();

        Bamboo\Page\Block::init();

        Liana\Page\Block::init();
        Liana\Cart\Block::init();

        $display = Helper::getBeansObject('liana', 'display');
        if (empty($display) || ! $display['is_active']) {
            return;
        }

        Liana\Observer\LianaObserver::init($display);
        Liana\Observer\LianaProductObserver::init($display);
    }
}
