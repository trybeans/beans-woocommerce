<?php


namespace BeansWoo\Front\Bamboo;

include_once('observer.php');
include_once('block.php');

class Main {
	public static function init(){
		Block::init();
	}
}