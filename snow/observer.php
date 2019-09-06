<?php


namespace BeansWoo\Snow;
use BeansWoo\Helper;

class Observer {

	public static function init(){

	}

	public static function  admin_notice(){
		Helper::admin_notice('snow');
	}
}