<?php

namespace BeansWoo\Front\Arrow;

defined('ABSPATH') or die;

class Block
{

    public static function init()
    {
        add_action("woocommerce_login_form_start", array(__CLASS__, 'renderButtonContainer'), 10);
    }

    public static function renderButtonContainer()
    {
        ?>
        <div class="beans-arrow"></div>
        <?php
    }
}