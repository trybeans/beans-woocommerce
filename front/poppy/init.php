<?php


namespace BeansWoo\Front\Poppy;

include_once('observer.php');

class Main {
	public static function init(){
        Observer::init();
	}
}