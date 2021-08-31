<?php

defined('ABSPATH') or die;

use BeansWoo\Helper;
use BeansWoo\Admin\Router;
use BeansWoo\Admin\Inspector;
use BeansWoo\Admin\Connector;

Inspector::init();
Connector::setupPages();
$base_banner_url = 'https://' . Helper::getDomain('CDN') . '/static-v3/connect/img/app/';
$base_asset_url  = Helper::getAssetURL('/assets/img/admin');

$admin = wp_get_current_user();

$country_code = get_option('woocommerce_default_country');
if ($country_code && strpos($country_code, ':') !== false) {
    try {
        $country_parts = explode(':', $country_code);
        $country_code = $country_parts[0];
    } catch (\Exception $e) {
    }
}

$connect = "https://" . Helper::getDomain('CONNECT') . "/radix/woocommerce/connect";

$beans_app_list = array(
    'liana' => array(
        'title' => 'Make your customers addicted to your shop',
        'role'  => 'Loyalty Program',
    ),

    'bamboo' => array(
        'title' => 'Turn your customers into advocates of your brand',
        'role'  => 'Referral Program',
    ),

    'foxx' => array(
        'title' => 'Super-targeted automated emails that drive sales',
        'role'  => 'Email Automation',
    ),

    'snow' => array(
        'title' => 'Communicate with customers without disrupting their journey',
        'role'  => 'Notification Widget',
    ),

    'arrow' => array(
        'title' => 'Know your customer.',
        'role'  => 'Social Connect',
    ),

    'ultimate' => array(
        'title' => 'Connect your shop to get started',
        'role'  => '',
    ),
);

?>


<div class="beans-admin-container">
<img class="beans-admin-logo" src="<?php echo $base_banner_url; ?>logo-full-ultimate.svg" alt="ultimate-logo">
<div class="welcome-panel-ultimate beans-admin-content-ultimate" style="max-width: 600px; margin: auto">
  <div>
    <div style="background-color:white; padding-top: 10px; padding-bottom: 60px;
                    box-shadow: 0 15px 30px 0 rgba(0,0,0,.11), 0 5px 15px 0 rgba(0,0,0,.08); width: 600px;
                    height: auto; position: relative; min-height: 400px;">
      <div id="beans-core">
        <div style="display: none; text-align: center;" id="app-image" class="app-image">
          <div>
            <img width="100px" height="30px" id="beans-app-img"
                 src="<?php echo $base_banner_url; ?>logo-full-liana.svg"
                 alt="beans-app-logo">
          </div>
        </div>
        <div style="text-align: center;">
          <h1 id="beans-welcome-text">Welcome to Beans</h1>
          <p id="beans-app-title">Create a unified marketing experience for your online shop.</p>
        </div>
        <div style="display: flex; width: 100%; justify-content: center">
          <div style="text-align: center">
            <img id="beans-app-hero" alt="" width="auto" height="280px"
                 src="<?php echo $base_asset_url . '/onboarding/ultimate-hero.svg' ?>"
            />
          </div>
        </div>
        <div style="height: auto">
          <button class="beans-bg-primary-ultimate" id="beans-step" data-step="0"
                  style="margin-right: 30px; float:right; border-width: 0; font-size: 13px; cursor: pointer;
                                color: #fff; border-color: transparent; text-transform: uppercase; display: flex;
                                justify-content: center; padding-top: .5rem; padding-bottom: .5rem; margin-top: .75rem;
                                border-radius: .25rem;
                                box-shadow: 0 15px 30px 0 rgba(0,0,0,.11), 0 5px 15px 0 rgba(0,0,0,.08);
                                font-weight: 900;">next
            <div style=" margin-left: .5rem; margin-top: 2px;">
              <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M1.69525 0.113281L7.0838 5.50183L1.69525 10.8904L0.2927259.48785L4.27875 5.50183L0.292725
                      1.51581L1.69525 0.113281Z" fill="white">
                </path>
              </svg>
            </div>
          </button>
        </div>
        <div id="beans-ultimate-connect" style="display: none">
          <p class="wc-setup-actions step" style="justify-content: center; display: flex"
             id="beans-ultimate-submit-button">
            <button type="submit" class="btn beans-bg-primary beans-bg-primary-ultimate
                            shadow-md" value="Connect to Beans Ultimate">
                Connect
              </button>
          </p>
        </div>
      </div>
    </div>
  </div>
    <form method="get" class="beans-admin-form" id="beans-connect-form" action="<?php echo $connect; ?>">
        <input type="hidden" name="email" value="<?php echo $admin->user_email; ?>">
        <input type="hidden" name="first_name" value="<?php echo $admin->user_firstname; ?>">
        <input type="hidden" name="last_name" value="<?php echo $admin->user_lastname; ?>">
        <input type="hidden" name="country" value="<?php echo $country_code; ?>">
        <input type="hidden" name="company_name" value="<?php echo get_bloginfo('name'); ?>">
        <input type="hidden" name="currency" value="<?php echo strtoupper(get_woocommerce_currency()); ?>">
        <input type="hidden" name="website" value="<?php echo get_site_url(); ?>">
        <input type="hidden" name="api_uri" value="<?php echo Inspector::$wc_endpoint_url_api; ?>">
        <input type="hidden" name="api_auth_uri" value="<?php echo Inspector::$wc_endpoint_url_auth ?>">
        <input type="hidden" name="redirect" value="<?php echo Router::getTabURL(Router::TAB_CONNECT); ?>">
    </form>
  <script>

      jQuery(function ($) {
          let info = [];
            <?php
            $apps = ['liana', 'bamboo', 'foxx', 'snow', 'arrow', 'ultimate'];
            foreach ($apps as $app) {
                if (in_array($app, ['snow', 'foxx'])) {
                    $hero_image = $app . '-hero.png';
                } else {
                    $hero_image = $app . '-hero.svg';
                }
                ?>
                  info.push({
                      hero: "<?php echo $base_asset_url . '/onboarding/' . $hero_image ?>",
                      banner: "<?php echo $base_banner_url . 'logo-full-' . $app . '.svg'; ?>",
                      title: "<?php echo $beans_app_list[$app]['title']; ?>",
                      role: "<?php echo $beans_app_list[$app]['role']; ?>",
                  });
                <?php
            }
            ?>

          $("#beans-step").on('click', function () {
              let step = $(this).attr("data-step");
              if ($(this).text() === "connect") {
                  $("#beans-connect-form").submit();
              }
              const data = info[step];
              let next_step = parseInt(step) + 1;
              if (next_step === info.length) {
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

              if (!data) return "";
              $("#beans-welcome-text").text(data.role);
              $("#beans-welcome-text").attr("style", "margin: 0 0;");

              $("#beans-app-img").attr("src", data.banner);
              $("#beans-app-title").text(data.title);
              $("#beans-app-hero").attr("src", data.hero);
              $(this).attr("data-step", next_step);
              $("#app-image").attr("style", "text-align:center;");
          });
          $("#beans-ultimate-submit-button").on('click', function () {
              $("#beans-connect-form").submit();
          })
      });

  </script>
</div>
    <a href="<?=Router::getTabURL(Router::TAB_INSPECT)?>" id="view-config">View configuration</a>
</div>