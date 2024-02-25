<?php

defined('ABSPATH') or die;

use BeansWoo\Admin\Router;
use BeansWoo\Helper;
use BeansWoo\Admin\Inspector;

Inspector::init();
Inspector::checkVersioning();

function getSupportedTag($vs_is_supported)
{
    if ($vs_is_supported) {
        echo "&nbsp;&nbsp;<span>&#x2705</span>";
    } else {
        echo "&nbsp;&nbsp;<span>&#x274C</span>";
    }
}

?>

<div class="beans-admin-container">
  <img class="beans-admin-logo" src="<?=Helper::getAssetURL('/assets/img/beans-logo.svg') ?>" alt="ultimate-logo">
  <p>
    Take customer loyalty to the next level with Beans.&nbsp;
    <a href="<?php echo Helper::getDomain('WWW');?>"  target="_blank">Discover Beans</a>
  </p>
</div>

<div class="beans-admin-container" style="text-align: left !important;">
  <div style="display: flex; flex-direction: column; align-items: start; justify-content: start; width: fit-content;
                text-align: left; max-width: 500px">
    <a href="javascript:void(0)" id="view-config">View configuration</a>
    <ul class="wc-wizard-features" style="display: None" id="config-status">
      <h3>Configuration Check</h3>
      <li>
        <p class="">
          Ensure your online store has the right setup for Beans to function optimally by using the 
          <a href="https://docs.woocommerce.com/document/woocommerce-rest-api/"
                             target="_blank">WooCommerce REST API</a>.
        </p>
      </li>
      <li>
        <p>
          <strong>WooCommerce Version</strong>: <?php echo Inspector::$versioning_installed['woocommerce'];
            getSupportedTag(Inspector::$woo_is_supported); ?>
        </p>
        <?php if (!Inspector::$woo_is_supported) : ?>
          <p class="beans-admin-check-warning">
          An update is required for optimal performance:
            <a href="https://docs.woocommerce.com/document/how-to-update-woocommerce/" target="_blank">
              See WooCommerce update guide
            </a>
          </p>
        <?php endif; ?>
      </li>
      <li>
        <p>
          <strong>Wordpress Version</strong>: <?php echo Inspector::$versioning_installed['wordpress'];
            getSupportedTag(Inspector::$wp_is_supported); ?>
        </p>
        <?php if (!Inspector::$wp_is_supported) : ?>
          <p class="beans-admin-check-warning">
            Please upgrade your Wordpress:
            <a href="https://codex.wordpress.org/Upgrading_WordPress" target="_blank">
              See Wordpress update guide
            </a>
          </p>
        <?php endif; ?>
      </li>
      <li>
        <p>
          <strong>PHP Version</strong>: <?php echo Inspector::$versioning_installed['php'];
            getSupportedTag(Inspector::$php_is_supported); ?>
        </p>
        <?php if (!Inspector::$php_is_supported) : ?>
          <p class="beans-admin-check-warning">
            Contact your web host to update your PHP:
            <a href="https://wordpress.org/support/update-php/" target="_blank">See PHP update guide</a>
          </p>
        <?php endif; ?>
      </li>
      <li>
        <p>
          <strong>CURL Supported</strong>: <?php getSupportedTag(Inspector::$curl_is_supported); ?>
        </p>
        <?php if (!Inspector::$curl_is_supported) : ?>
          <p class="beans-admin-check-warning">Contact your web host to enable CURL support.</p>
        <?php endif; ?>
      </li>
      <li>
        <p>
          <strong>JSON Supported</strong>: <?php getSupportedTag(Inspector::$json_is_supported); ?>
        </p>
        <?php if (!Inspector::$json_is_supported) : ?>
          <p class="beans-admin-check-warning">Contact your web host to enable JSON support.</p>
        <?php endif; ?>
      </li>
      <li>
        <p>
          <strong>WordPress Permalink Enabled</strong>: <?php getSupportedTag(Inspector::$permalink_is_supported); ?>
        </p>
        <?php if (!Inspector::$permalink_is_supported) : ?>
          <p class="beans-admin-check-warning">
            Please enable Permalinks:
            <a href="https://wordpress.org/support/article/settings-permalinks-screen/" target="_blank">
              How to enable Permalink
            </a>
          </p>
        <?php endif; ?>
      </li>
      <?php if (!is_null(Inspector::$woo_api_uri_is_up)) : ?>
        <li>
          <p>
            <strong>WooCommerce API V3 URI
              Test</strong>: <?php getSupportedTag(Inspector::$woo_api_uri_is_up); ?>
          </p>
            <?php if (!Inspector::$woo_api_uri_is_up) : ?>
            <p class="beans-admin-check-warning">
              Unable to connect to the API using endpoint: <br/>
                <?php echo Inspector::$wc_endpoint_url_api; ?> <br/>
              HTTP Status: <?php echo Inspector::$woo_api_uri_http_status ?> <br/>
              Content Type: <?php echo Inspector::$woo_api_uri_content_type ?> <br/>
              Please:
              <a href="mailto:hello@trybeans.com" target="_blank">Contact the Beans Team</a> for
              assistance.
              Attach a screenshot of this page to your email.
            </p>
            <?php endif; ?>
        </li>
      <?php endif; ?>
      <?php if (!is_null(Inspector::$woo_api_auth_is_up)) : ?>
        <li>
          <p>
            <strong>
              WooCommerce API V2 Authentication
              Test</strong>: <?php getSupportedTag(Inspector::$woo_api_auth_is_up); ?>
          </p>
            <?php if (!Inspector::$woo_api_auth_is_up) : ?>
            <p class="beans-admin-check-warning">
              Unable to connect to the API using authentication endpoint: <br/>
                <?php echo Inspector::$wc_endpoint_url_auth; ?> <br/>
              HTTP Status: <?php echo Inspector::$woo_api_auth_http_status; ?> <br/>
              Content Type: <?php echo Inspector::$woo_api_auth_content_type; ?> <br/>
              Please:
              <a href="mailto:hello@trybeans.com" target="_blank">Contact the Beans Team</a> for
              assistance.
              Attach a screenshot of this page to your email.
            </p>
            <?php endif; ?>
        </li>
      <?php endif; ?>
    </ul>
  </div>
    <form action="" method="get">
    <div id="beans-ultimate-connect">
        <p class="wc-setup-actions step" style="justify-content: center; display: flex"
            id="beans-ultimate-submit-button">
          <input type="hidden" name="page" value="<?=Router::MENU_SLUG ?>">
          <input type="hidden" name="tab" value="<?=Router::TAB_CONNECT ?>">
          <?php if (Inspector::$beans_is_supported) : ?>
              <button 
                  type="submit"
                  class="btn beans-bg-primary beans-bg-primary-ultimate shadow-md" 
                  value="Connect to Beans Ultimate"
                  >
                  Get Started
              </button>
          <?php else : ?>
                  <!-- class="button button-disabled shadow-md" -->
              <button 
                  type="submit"
                  class="button button-link button-link-delete shadow-md"
                  value="Connect to Beans Ultimate"
                  >
                  Ignore warnings
              </button>
          <?php endif; ?>
        </p>
    </div>
    </form>
</div>

<script>
  jQuery(function () {

    const woo_is_supported = "<?php echo Inspector::$woo_is_supported; ?>";
    const wp_is_supported = "<?php echo Inspector::$wp_is_supported; ?>";
    const php_is_supported = "<?php echo Inspector::$php_is_supported; ?>";
    const curl_is_supported = "<?php echo Inspector::$curl_is_supported; ?>";
    const json_is_supported = "<?php echo Inspector::$json_is_supported; ?>";
    const permalink_is_supported = "<?php echo Inspector::$permalink_is_supported; ?>";
    const woo_api_uri_is_up = "<?php echo Inspector::$woo_api_uri_is_up; ?>";
    const woo_api_auth_is_up = "<?php echo Inspector::$woo_api_auth_is_up; ?>";

    if (woo_is_supported !== '1' || wp_is_supported !== '1' || php_is_supported !== '1'
      || php_is_supported !== '1' || curl_is_supported !== '1' || json_is_supported !== '1'
      || permalink_is_supported !== '1' || woo_api_auth_is_up !== '1' || woo_api_uri_is_up !== '1') {
      jQuery('#view-config').text('View less');
      jQuery('#config-status').slideDown('slow');
    }
    jQuery('#view-config').click(function () {
      if (jQuery("#config-status").is(':hidden')) {
        jQuery('#config-status').slideDown('slow');
        jQuery('#view-config').text('View less');
      } else {
        jQuery('#config-status').slideUp('slow');
        jQuery('#view-config').text('View configuration');
      }
    });
  })
</script>
