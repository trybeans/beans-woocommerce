<?php

defined('ABSPATH') or die;

use BeansWoo\Helper;
use BeansWoo\Admin\Router;
use BeansWoo\Admin\Inspector;
use BeansWoo\Admin\Connector;

Inspector::init();
Connector::setupPages();

$admin = wp_get_current_user();

$country_code = get_option('woocommerce_default_country');
if ($country_code && strpos($country_code, ':') !== false) {
    try {
        $country_parts = explode(':', $country_code);
        $country_code = $country_parts[0];
    } catch (\Exception $e) {
    }
}

$connect_url = Helper::getDomain('TRELLIS') . '/pages/$xxx/woocommerce/install/';

?>


<div class="beans-admin-container">
<img class="beans-admin-logo" src="<?=Helper::getAssetURL('/assets/img/beans-logo.svg') ?>" alt="ultimate-logo">
<div class="welcome-panel-ultimate beans-admin-content-ultimate" style="max-width: 600px; margin: auto">
  <div>
    <div style="background-color:white; padding-top: 10px; padding-bottom: 30px;
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
          <h1 id="beans-welcome-text">Get started</h1>
          <p id="beans-app-title">Retain your customers and get more repeat purchases.</p>
        </div>
        <div style="display: flex; width: 100%; justify-content: center">
          <div style="text-align: center">
            <img id="beans-app-hero" alt="" width="auto" height="280px"
                 src="<?=Helper::getAssetURL('/assets/img/admin/onboarding/ultimate-hero.svg') ?>"
            />
          </div>
        </div>
        <div id="beans-ultimate-connect">
          <p class="wc-setup-actions step" style="justify-content: center; display: flex"
             id="beans-ultimate-submit-button">
            <button
              type="submit"
              class="btn beans-bg-primary beans-bg-primary-ultimate shadow-md"
              value="Connect to Beans Ultimate">
                Connect
            </button>
          </p>
        </div>
      </div>
    </div>
  </div>
    <form method="get" class="beans-admin-form" id="beans-connect-form" action="<?php echo $connect_url; ?>">
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
          $("#beans-ultimate-submit-button").on('click', function () {
              $("#beans-connect-form").submit();
          })
      });
  </script>
</div>
    <a href="<?=Router::getTabURL(Router::TAB_INSPECT)?>" id="view-config">View configuration</a>
</div>
