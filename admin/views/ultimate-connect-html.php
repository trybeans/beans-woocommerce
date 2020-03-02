<?php

defined('ABSPATH') or die;

use BeansWoo\Helper;
?>

<img class="beans-admin-logo" id="beans-app-img"
     src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-<?php echo static::$app_name;  ?>.svg"
     alt="<?php echo static::$app_name;  ?>-logo">
<div class="welcome-panel-ultimate beans-admin-content-ultimate" style="max-width: 600px; margin: auto">

    <div>
        <div style="background-color:white; padding-bottom: 60px; box-shadow: 0 15px 30px 0 rgba(0,0,0,.11), 0 5px 15px 0 rgba(0,0,0,.08); width: 600px; height: auto; position: relative">
            <div style="text-align: center; padding: 5px;">
                <h1 id="beans-welcome-text">Welcome to Beans</h1>
                <h2 id="beans-app-title"></h2>
                <p id="beans-p-description">Create a unified marketing experience for your online shop.</p>
            </div>
            <div style="display: flex; width: 100%">
                <img id="beans-app-hero" src="<?php echo plugins_url('/assets/ultimate-hero-image.svg',
                    BEANS_PLUGIN_FILENAME) ?>" alt="" width="96.5%">
            </div>
            <div style="height: auto">
                <button class="beans-bg-primary-ultimate" id="beans-step" data-step="0"  style="margin-right: 30px; float:right; border-width: 0; font-size: 13px; cursor: pointer; color: #fff; border-color: transparent; text-transform: uppercase; display: flex; justify-content: center; padding-top: .5rem; padding-bottom: .5rem; margin-top: .75rem; border-radius: .25rem; box-shadow: 0 15px 30px 0 rgba(0,0,0,.11), 0 5px 15px 0 rgba(0,0,0,.08); font-weight: 900;">next
                    <div style=" margin-left: .5rem; margin-top: 2px;">
                        <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M1.69525 0.113281L7.0838 5.50183L1.69525 10.8904L0.292725 9.48785L4.27875 5.50183L0.292725 1.51581L1.69525 0.113281Z"
                                  fill="white"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </div>
    <script>
        jQuery(function ($) {
            const info = [
                {
                    hero: "<?php echo plugins_url('/assets/liana-hero-image.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-liana.svg",
                    title: "<?php echo Helper::getApps()['liana']['title']; ?>",
                    bg: "beans-bg-primary-liana",
                },
                {
                    hero: "<?php echo plugins_url('/assets/bamboo-hero-image.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-bamboo.svg",
                    title: "<?php echo Helper::getApps()['bamboo']['title']; ?>",
                    bg: "beans-bg-primary-bamboo",
                },
                {
                    hero: "<?php echo plugins_url('/assets/foxx-hero-image.png',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-foxx.svg",
                    title: "<?php echo Helper::getApps()['foxx']['title']; ?>",
                    bg: "beans-bg-primary-foxx",
                },

                {
                    hero: "<?php echo plugins_url('/assets/poppy-hero-image.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-poppy.svg",
                    title: "<?php echo Helper::getApps()['poppy']['title']; ?>",
                    bg: "beans-bg-primary-poppy",
                },
                {
                    hero: "<?php echo plugins_url('/assets/snow-hero-image.png',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-snow.svg",
                    title: "<?php echo Helper::getApps()['snow']['title']; ?>",
                    bg: "beans-bg-primary-snow",
                },

                {
                    hero: "<?php echo plugins_url('/assets/lotus-hero-image.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-lotus.svg",
                    title: "<?php echo Helper::getApps()['lotus']['title']; ?>",
                    bg: "beans-bg-primary-lotus",
                },
                {
                    hero: "<?php echo plugins_url('/assets/arrow-hero-image.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-arrow.svg",
                    title: "<?php echo Helper::getApps()['arrow']['title']; ?>",
                    bg: "beans-bg-primary-arrow",
                },
                {
                    hero: "<?php echo plugins_url('/assets/ultimate-hero-image.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-ultimate.svg",
                    title: "<?php echo Helper::getApps()['ultimate']['title']; ?>",
                    bg: "beans-bg-primary-ultimate",
                },

            ];
            $("#beans-step").on('click', function(){
                let step = $(this).attr("data-step");
                if($(this).text() === "connect") {
                    $("#beans-connect-form").submit();
                }

                let next_step = parseInt(step) + 1;
                if ( next_step === info.length ){
                    $(this).text("connect");
                    $(this).attr("id", "ultimate-submit-button");
                    $(this).attr("type", "submit");
                }
                const data = info[step];
                if ( ! data ) return "";
                $("#beans-welcome-text").hide();
                $("#beans-p-description").hide();

                $("#beans-app-img").attr("src", data.banner);
                $("#beans-app-title").text(data.title);
                $("#beans-app-hero").attr( "src", data.hero);
                $(this).attr("class", data.bg);
                $(this).attr("data-step", next_step);
            })
        });
    </script>
