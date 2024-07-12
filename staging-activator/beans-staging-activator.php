<?php

/*
Plugin Name: Beans Staging Activator
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: This plugin allow you to turn the beta mode for Beans on your Woocommerce store
Version: 1.2.1
Author: Beans
Author URI: https:/www.trybeans.com
*/

namespace BeansWooEnv;

if (!defined('ABSPATH')) {
    exit;
}

class BeansWooEnv
{
    public function init()
    {
        add_action('init', array(__CLASS__, 'setEnv'));
        add_action('admin_notices', array(__CLASS__, 'displayAdminNotice'));
        add_action('wp_footer', array( __CLASS__, 'displayYellowNotice' ));
        add_action('wp_print_scripts', array(__CLASS__, 'removeWoocommercePasswordStrength'), 10);
    }

    public static function removeWoocommercePasswordStrength()
    {
        wp_dequeue_script('wc-password-strength-meter');
    }

    public static function displayAdminNotice()
    {
        ?>
        <div class="notice notice-warning">
            <div style="margin: 10px auto;">
            Beans: You are currently using the staging version of Beans Ultimate
            </div>
        </div>
        <?php
    }

    public static function displayYellowNotice()
    {
        ?>
        <style type="text/css">
            .swnza_banner{
                position:fixed;
                height:auto;
                width:100%;
                background:rgb(251,255,58);
                padding:10px;
                z-index:999;
                display:block;
            }

            .swnza_banner{ bottom:0; }
            .swnza_close_button { top:-10px;}

            .swnza_banner p {
                color: rgba(0,0,0,1);
                text-align:center;
                z-index:1000;
                font-size:18px;
                display:block;
                margin: 0;
            }

            #swnza_banner_text{
                margin-top:0;
            }

        </style>
        <div class="swnza_banner" id="swnza_banner_id" style="">
            <p id="swnza_banner_text">You are currently using the staging version of Beans Ultimate</p>
            <a id="swnza_close_button_link" class="swnza_close_button"></a>
        </div>
        <?php
    }

    public static function setEnv()
    {
        // putenv('BEANS_DOMAIN_WWW=https://www.bns.re');
        // putenv('BEANS_DOMAIN_STEM=https://api.trybeans.com');
        // putenv('BEANS_DOMAIN_BOILER=https://app.trybeans.com');
        // putenv('BEANS_DOMAIN_CONNECT=https://connect.trybeans.com');
        // putenv('BEANS_DOMAIN_TRELLIS=https://trellis.trybeans.com');
        putenv('BEANS_DOMAIN_CDN=https://bnsre.s3.amazonaws.com');
    }
}

(new BeansWooEnv())->init();
