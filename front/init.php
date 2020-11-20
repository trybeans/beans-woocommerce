<?php
namespace BeansWoo\Front;


if ( ! defined( 'ABSPATH' ) )
    exit;
include_once('block.php');
include_once('liana/init.php');
include_once('bamboo/init.php');
include_once ('arrow/init.php');

use BeansWoo\Front\Liana\Main as LianaMain;
use BeansWoo\Front\Bamboo\Main as BambooMain;
use BeansWoo\Front\Arrow\Main as ArrowMain;
use BeansWoo\Helper;

class Main {
    public static $card;
    public static function init(){
        self::$card = Helper::getBeansObject('ultimate', 'card');
        if (!self::$card['is_active'] || ! Helper::isSetupApp('ultimate')){
            return ;
        }

        Block::init();
        LianaMain::init();
        BambooMain::init();
        ArrowMain::init();
    }
}