<?php

defined('ABSPATH') or die;

use Beans\Error\BaseError;
use BeansWoo\Helper;

$loginkey = get_transient('beans_loginkey');

if( ! $loginkey ){
    try {
        $loginkey= Helper::API()->post('core/user/current/loginkey');
        set_transient('beans_loginkey', $loginkey, 3*60);
    } catch (BaseError  $e) {}
}

$app_info = Helper::getApps()[static::$app_name];
$card = array();

$app_info['instance'] = Helper::getCard(static::$app_name);
if (!empty($app_info['instance'])) {
    $card = $app_info['instance'];
}

if ( isset($_POST) && isset($_POST['beans-liana-display-redemption-checkout']) ){
    $is_redeem_checkout = htmlspecialchars($_POST['beans-liana-display-redemption-checkout']);
    update_option( 'beans-liana-display-redemption-checkout', $is_redeem_checkout);
}

$app_name = static::$app_name;

if (static::$app_name == 'ultimate') {
    $app_name = "app";
}

?>

<?php if (empty($card)): ?>
<div class="welcome-panel beans-admin-content" style="max-width: 600px; margin: auto">
    <p class="beans-admin-check-warning">
        Unable to connect to Beans. Unable to retrieve information about your account status.
        Please:
        <a href="mailto:hello@trybeans.com" target="_blank">Contact the Beans Team</a> for assistance.
        Attach a screenshot of this page to your email.
    </p>
</div>
<?php else: ?>

<div style="max-width: 700px; margin: auto; margin-top: 30px;">

    <div style="padding:20px;">
        <div class="beans-woo-header">
            <div class="beans-woo-banner">
                <img width="auto"  height="30px;"
                     src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-<?php echo static::$app_name; ?>.svg"
                     alt="<?php echo static::$app_name; ?>-logo">
                <div style="margin: auto;">
                    <span class="beans-woo-banner-text"> <span style="color: #10a866">✔</span>️ Connected</span>
                </div>
            </div>
            <div>
                <a class="button beans-woo-banner-link"
                   href="https://<?php echo Helper::getDomain('CONNECT') . "/auth/login/${loginkey['key']}"; ?>?next=https://<?php echo $app_name . "." . Helper::getDomain('NAME') ?>"
                   target="_blank">
                    Go To <?php echo static::$app_name != 'ultimate'? ucfirst(static::$app_name): "Beans Ultimate" ;  ?>
                </a>
            </div>
        </div>
        <?php if (Helper::isSetupApp('liana')): ?>
            <div class="beans-woo-reward">
                <div>
                    <div class="beans-woo-reward-title">
                        Reward page
                    </div>
                    <div class="beans-woo-reward-description">
                            The reward page is available on your website and let your customers join and use your rewards
                            program.
                    </div>
                    <span class="" >
                        <a style="margin-top: 10px;" class="button beans-woo-reward-link" target="_blank"
                                      href="<?php echo get_permalink(Helper::getConfig('liana_page')); ?>">
                            Go to the reward page
                        </a>
                    </span>
                </div>
                <div style="display: flex; align-items: center; margin-left: 20px;">
                    <img width="150px" src="<?php echo plugins_url('assets/img/reward-page.svg', BEANS_PLUGIN_FILENAME); ?>"  />
                </div>
            </div>
        <?php endif; ?>
        <?php if (Helper::isSetupApp('bamboo') ): ?>
            <div class="beans-woo-reward">
                <div>
                    <div class="beans-woo-reward-title">
                            Referral page
                    </div>
                    <div class="beans-woo-reward-description">
                            The referral page is available on your website and let your customers join and use your referrals
                            program.
                    </div>
                    <span class="" >
                        <a style="margin-top: 10px;" class="button beans-woo-reward-link" target="_blank"
                           href="<?php echo get_permalink(Helper::getConfig('bamboo_page')); ?>">
                            Go to the referral page
                        </a>
                    </span>
                </div>
                <div style="display: flex; align-items: center; margin-left: 20px;">
                    <img width="150px" src="<?php echo plugins_url('assets/img/reward-page.svg', BEANS_PLUGIN_FILENAME); ?>"  />
                </div>
            </div>
        <?php endif; ?>

        <?php if((Helper::isSetupApp('liana'))) : ?>
        <div class="beans-woo-settings">
            <div class="beans-woo-settings-title">Settings</div>
            <form method="post" action="options.php">
                <?php
                settings_fields("beans-section");

                do_settings_sections("beans-woo");

                submit_button('Save', 'submit', 'beans-checkout-button', '', ['class' => 'button' ]);
                ?>
            </form>
        </div>
        <?php endif; ?>

        <div class="beans-woo-help">
            <div class="beans-woo-help-title">Need help?</div>
            <div style="display: flex;">
                <span class="beans-woo-help-action">
                    <a target="_blank" href="https://web.facebook.com/groups/1220975858059106/">
                        <span>
                            <img
                                    src="<?php echo plugins_url('assets/img/facebook.svg', BEANS_PLUGIN_FILENAME); ?>" width="18px" height="18px"/>
                        </span>Join Facebook Group
                    </a>
                </span>
                <span class="beans-woo-help-action">
                    <a target="_blank" href="https://twitter.com/beanshq">
                        <span>
                            <img
                                    src="<?php echo plugins_url('assets/img/twitter.svg', BEANS_PLUGIN_FILENAME); ?>" width="18px" height="18px"/>
                        </span>Follow us on Twitter
                    </a>
                </span>
                <span class="beans-woo-help-action">
                    <a target="_blank" href="http://help.trybeans.com/">
                        <span><img src="<?php echo plugins_url('assets/img/help-center.svg', BEANS_PLUGIN_FILENAME); ?>" width="18px" height="18px"/>
                        </span>Go to Help Center
                    </a>
                </span>
                <span class="beans-woo-help-action">
                    <a href="mailto:hello@trybeans.com">
                        <span>
                            <img src="<?php echo plugins_url('assets/img/email-support.svg', BEANS_PLUGIN_FILENAME); ?>" width="18px" height="18px"/>
                        </span>Contact Support
                    </a>
                </span>
            </div>
        </div>

        <div class="beans-woo-review">
            <div style="display: flex; align-items: center;">
                <img src="<?php echo plugins_url('assets/img/beans-review-logo.png', BEANS_PLUGIN_FILENAME); ?>"/>
            </div>
            <div>
                <div class="beans-woo-review-title">
                    Review Beans
                </div>
                <div class="beans-woo-x">
                    If you find Beans helpful, please take 30 seconds of your time to review it in the app store.
                </div>
                <span><a class="beans-woo-j" href='https://wordpress.org/support/plugin/beans-woocommerce-loyalty-rewards/reviews/'
                         target="_blank">Review Beans</a></span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<div style='max-width: 700px; margin: auto;'>
    <a style="color: #d70000; float: right; margin-right: 20px;"
       href='<?php echo admin_url(static::$app_info['link'] . '&reset_beans=1'); ?>'>Reset Settings Now</a>
</div>
