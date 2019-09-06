<?php


namespace BeansWoo\Bamboo;


use BeansWoo\Helper;

class Observer {

	public static function init(){

	}

	public static function  admin_notice(){
		Helper::admin_notice('bamboo');
	}
}