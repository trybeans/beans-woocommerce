<?php

defined('ABSPATH') or die;

use BeansWoo\Helper;
?>


<img class="beans-admin-logo"
     src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-ultimate.svg"
     alt="<?php echo static::$app_name;  ?>-logo">
<div class="welcome-panel-ultimate beans-admin-content-ultimate" style="max-width: 600px; margin: auto">
    <div>
        <div style="background-color:white; padding-top: 10px; padding-bottom: 60px;
        box-shadow: 0 15px 30px 0 rgba(0,0,0,.11), 0 5px 15px 0 rgba(0,0,0,.08); width: 600px; height: auto; position: relative;
        min-height: 400px;">
            <div id="beans-core" >
                <div style="display: none; text-align: center;" id="app-image"  class="app-image">
                    <div>
                        <img width="100px" height="30px" id="beans-app-img"
                             src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-liana.svg"
                             alt="<?php echo static::$app_name;  ?>-logo">
                    </div>
                </div>
                <div style="text-align: center;">
                    <h1 id="beans-welcome-text">Welcome to Beans</h1>
                    <p id="beans-app-title">Create a unified marketing experience for your online shop.</p>
                </div>
                <div style="display: flex; width: 100%; justify-content: center">
                    <div style="text-align: center">
                        <img id="beans-app-hero" src="<?php echo plugins_url('/assets/img/admin/onboarding/ultimate-hero.svg',
                            BEANS_PLUGIN_FILENAME) ?>" alt="" width="auto" height="280px">
                    </div>
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
                <div id="beans-ultimate-connect" style="display: none">
                    <p class="wc-setup-actions step" style="justify-content: center; display: flex" id="beans-ultimate-submit-button">
                        <?php if ( $beans_is_supported || $force ): ?>
                        <button type="submit" class="btn beans-bg-primary beans-bg-primary-<?php echo static::$app_name;  ?>
                            shadow-md" value="Connect to <?php echo ucfirst(static::$app_name);  ?>">
                            <?php else: ?>
                            <button type="submit"
                                    class="button button-disabled beans-bg-primary beans-bg-primary-<?php echo static::$app_name;  ?>
                                shadow-md " value="Connect to <?php echo ucfirst(static::$app_name);  ?>"
                                    disabled>
                                <?php endif; ?>
                                Connect
                            </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(function ($) {
            const info = [
                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/liana-hero.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-liana.svg",
                    title: "<?php echo Helper::getApps()['liana']['title']; ?>",
                    role: "<?php echo Helper::getApps()['liana']['role']; ?>",
                },
                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/bamboo-hero.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-bamboo.svg",
                    title: "<?php echo Helper::getApps()['bamboo']['title']; ?>",
                    role: "<?php echo Helper::getApps()['bamboo']['role']; ?>",
                },
                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/foxx-hero.png',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-foxx.svg",
                    title: "<?php echo Helper::getApps()['foxx']['title']; ?>",
                    role: "<?php echo Helper::getApps()['foxx']['role']; ?>",
                },

                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/poppy-hero.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-poppy.svg",
                    title: "<?php echo Helper::getApps()['poppy']['title']; ?>",
                    role: "<?php echo Helper::getApps()['poppy']['role']; ?>",
                },
                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/snow-hero.png',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-snow.svg",
                    title: "<?php echo Helper::getApps()['snow']['title']; ?>",
                    role: "<?php echo Helper::getApps()['snow']['role']; ?>",
                },

                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/lotus-hero.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-lotus.svg",
                    title: "<?php echo Helper::getApps()['lotus']['title']; ?>",
                    role: "<?php echo Helper::getApps()['lotus']['role']; ?>",
                },
                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/arrow-hero.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-arrow.svg",
                    title: "<?php echo Helper::getApps()['arrow']['title']; ?>",
                    role: "<?php echo Helper::getApps()['arrow']['role']; ?>",
                },
                {
                    hero: "<?php echo plugins_url('/assets/img/admin/onboarding/ultimate-hero.svg',
                        BEANS_PLUGIN_FILENAME) ?>",
                    banner: "https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-ultimate.svg",
                    title: "<?php echo Helper::getApps()['ultimate']['title']; ?>",
                },

            ];
            $("#beans-step").on('click', function(){
                let step = $(this).attr("data-step");
                if($(this).text() === "connect") {
                    $("#beans-connect-form").submit();
                }
                const data = info[step];
                let next_step = parseInt(step) + 1;
                if ( next_step === info.length ){
                    $(this).text("connect");
                    $(this).attr("id", "ultimate-submit-button");
                    $(this).attr("type", "submit");
                    $("#beans-welcome-text").text(data.title);
                    $("#beans-app-title").hide();
                    $("#beans-app-img").hide();
                    $("#beans-ultimate-connect").show();
                    $(this).hide();
                    $("#beans-app-hero").attr("width", "75%");
                }

                if ( ! data ) return "";
                $("#beans-welcome-text").text(data.role);
                $("#beans-welcome-text").attr("style", "margin: 0 0;");

                $("#beans-app-img").attr("src", data.banner);
                $("#beans-app-title").text(data.title);
                $("#beans-app-hero").attr( "src", data.hero);
                $(this).attr("data-step", next_step);
                $("#app-image").attr("style", "text-align:center;");
            });
            $("#beans-ultimate-submit-button").on('click', function(){
                $("#beans-connect-form").submit();
            })
        });

    </script>
