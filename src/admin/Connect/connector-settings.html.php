<?php

defined('ABSPATH') or die;

use Beans\BeansError;
use BeansWoo\Helper;
use BeansWoo\Admin\Router;


try {
    $loginkey = Helper::requestTransientAPI('POST', 'core/user/current/loginkey');
} catch (BeansError $e) {
    Helper::log('Unable to retrieve login key: ' . $e->getMessage());
    $loginkey = array();
}

if (isset($_POST['beans-liana-display-redemption-checkout'])) {
    $is_redeem_checkout = htmlspecialchars($_POST['beans-liana-display-redemption-checkout']);
    update_option('beans-liana-display-redemption-checkout', $is_redeem_checkout);
}

$base_asset_url = Helper::getAssetURL('assets/img/connector');

?>

<?php if (!Helper::isSetup()) : ?>
  <div class="welcome-panel beans-admin-content" style="max-width: 600px; margin: auto">
    <p class="beans-admin-check-warning">
      Unable to connect to Beans. Unable to retrieve information about your account status.
      Please:
      <a href="mailto:hello@trybeans.com" target="_blank">Contact the Beans Team</a> for assistance.
      Attach a screenshot of this page to your email.
    </p>
  </div>
<?php else : ?>
  <div style="max-width: 700px; margin: auto; margin-top: 30px;">

    <div style="padding:20px;">
      <div class="beans-woo-header">
        <div class="beans-woo-banner">
          <img width="auto" height="30px;"
               src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-ultimate.svg"
               alt="ultimate-logo">
          <div style="margin: auto;">
            <span class="beans-woo-banner-text"> <span style="color: #10a866">✔</span>️ Connected</span>
          </div>
        </div>
        <div>
          <a class="button beans-woo-banner-link"
             href="https://<?php echo Helper::getDomain('CONNECT') .
                 "/auth/login/${loginkey['key']}"; ?>?next=https://app.<?php echo Helper::getDomain('NAME') ?>"
             target="_blank">
            Go To Beans Ultimate
          </a>
        </div>
      </div>
      <div class="beans-woo-reward">
        <div>
          <div class="beans-woo-reward-title">
            Reward page
          </div>
          <div class="beans-woo-reward-description">
            The reward page is available on your website and let your customers join and use your reward
            program.
          </div>
          <span class="">
                <a style="margin-top: 10px;" class="button beans-woo-reward-link" target="_blank"
                   href="<?php echo get_permalink(Helper::getConfig('liana_page')); ?>">
                    Go to the reward page
                </a>
              </span>
        </div>
        <div style="display: flex; align-items: center; margin-left: 20px;">
          <img width="150px" src="<?php echo $base_asset_url; ?>/reward-page.svg"/>
        </div>
      </div>
      <div class="beans-woo-reward">
        <div>
          <div class="beans-woo-reward-title">Referral page</div>
          <div class="beans-woo-reward-description">
            The referral page is available on your website and let your customers join
            and use your referral program.
          </div>
          <a style="margin-top: 10px;" class="button beans-woo-reward-link" target="_blank"
             href="<?php echo get_permalink(Helper::getConfig('bamboo_page')); ?>">
            Go to the referral page
          </a>
        </div>
        <div style="display: flex; align-items: center; margin-left: 20px;">
          <img width="150px" src="<?php echo $base_asset_url; ?>/reward-page.svg"/>
        </div>
      </div>
      <div class="beans-woo-settings">
        <div class="beans-woo-settings-title">Settings</div>
        <form method="post" action="options.php">
            <?php
            settings_fields("beans-section");

            do_settings_sections("beans-woo");

            submit_button('Save', 'submit', 'beans-checkout-button', false, ['class' => 'button']);
            ?>
        </form>
      </div>
      <div class="beans-woo-help">
        <div class="beans-woo-help-title">Need help?</div>
        <div style="display: flex;">
          <span class="beans-woo-help-action">
            <a target="_blank" href="https://web.facebook.com/groups/1220975858059106/">
              <span>
                <img src="<?php echo $base_asset_url; ?>/facebook.svg" width="18px" height="18px"/>
              </span>
              Join Facebook Group
            </a>
          </span>
          <span class="beans-woo-help-action">
            <a target="_blank" href="https://twitter.com/beanshq">
              <span>
                <img src="<?php echo $base_asset_url; ?>/twitter.svg" width="18px" height="18px"/>
              </span>
              Follow us on Twitter
            </a>
          </span>
          <div class="beans-woo-help-action">
            <a target="_blank" href="http://help.trybeans.com/">
              <img src="<?php echo $base_asset_url; ?>/help-center.svg" width="18px" height="18px"/>
              Go to Help Center
            </a>
          </div>
          <div class="beans-woo-help-action">
            <a href="mailto:hello@trybeans.com">
              <img src="<?php echo $base_asset_url; ?>/email-support.svg" width="18px" height="18px"/>
              Contact Support
            </a>
          </div>
        </div>
      </div>

      <div class="beans-woo-review">
        <div style="display: flex; align-items: center;">
          <img src="<?php echo $base_asset_url; ?>/beans-review-logo.png"/>
        </div>
        <div>
          <div class="beans-woo-review-title">
            Review Beans
          </div>
          <div class="beans-woo-x">
            If you find Beans helpful, please take 30 seconds of your time to review it in the app store.
          </div>
          <span>
            <a class="beans-woo-j"
               href='https://wordpress.org/support/plugin/beans-woocommerce-loyalty-rewards/reviews/'
               target="_blank">Review Beans</a>
          </span>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<div style='max-width: 700px; margin: auto;'>
  <a style="color: #d70000; float: right; margin-right: 20px;"
     href='<?php echo Router::getTabURL(Router::TAB_SETTINGS) . '&reset_beans=1'; ?>'>Reset Settings Now</a>
</div>
