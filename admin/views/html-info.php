<?php

defined('ABSPATH') or die;

use BeansWoo\Helper;

try {
	$loginkey = Helper::API()->post( 'core/user/current/loginkey' );
} catch ( \Beans\Error\BaseError  $e ) {}

$app_info = Helper::getApps()[static::$app_name];
$card = array();

$app_info['instance'] = Helper::getCard( static::$app_name );
if ( ! empty( $app_info['instance'] ) ) {
	$card = $app_info['instance'];
}
?>

<div class="beans-admin-container">
    <img class="beans-admin-logo"
         src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-<?php echo static::$app_name;  ?>.svg"
         alt="<?php echo static::$app_name;  ?>-logo">

	<?php if(empty($card)): ?>

        <div class="welcome-panel beans-admin-content" style="max-width: 600px; margin: auto">
            <p class="beans-admin-check-warning">
                Unable to connect to Beans. Unable to retrieve information about your account status.
                Please:
                <a href="mailto:hello@trybeans.com" target="_blank">Contact the Beans Team</a> for assistance.
                Attach a screenshot of this page to your email.
            </p>

            <div style='margin: 20px auto'>
                <a  style="color: #d70000; float: right" href='<?php echo admin_url(  static::$app_info['link'].'&reset_beans=1' ); ?>'>Reset Settings Now</a>
            </div>
        </div>

	<?php else: ?>

        <div class="welcome-panel beans-admin-content" style="max-width: 600px; margin: auto">
            <h1 style="text-align: center">Connected</h1>
            <p>
                Your store <b><?php echo $card['company_name']; ?></b>
                is successfully connected to <?php echo ucfirst(static::$app_name);  ?>!
            </p>
            <p>
                Your store unique identifier <b><?php echo $card['address']; ?></b>
            </p>
            <div style="margin: auto;">
                <img src="<?php echo plugins_url('/assets/' . static::$app_name . "-hero-image.svg",
                    BEANS_PLUGIN_FILENAME) ?>"  alt="" width="95%" onerror="this.style.display='none'">
                <img src="<?php echo plugins_url('/assets/' . static::$app_name . "-hero-image.png",
                    BEANS_PLUGIN_FILENAME) ?>"  alt="" width="95%" onerror="this.style.display='none'">
            </div>

            <div>
                <div class="beans-admin-form">
                    <p class="wc-setup-actions step" style="display: flex; justify-content: center;">
                        <a class="btn bg-primary bg-primary-<?php echo static::$app_name;  ?>"
                           href="https://<?php echo Helper::getDomain( 'CONNECT' ) . "/auth/login/${loginkey['key']}"; ?>?next=https://<?php echo static::$app_name. ".". Helper::getDomain( 'NAME' ) ?>" target="_blank">
                            Go To <?php echo ucfirst(static::$app_name);  ?>
                        </a>
                    </p>
                </div>
            </div>
            <div style='margin: 20px auto; display: flex; justify-content: space-between '>
                <p>
                    If you like Beans, please
                    <a  href='https://wordpress.org/support/plugin/beans-woocommerce-loyalty-rewards/reviews/' target="_blank">
                        leave us a ★★★★★ rating
                    </a>
                </p>
                <div>
                    <a  style="color: #d70000; float: right" href='<?php echo admin_url( static::$app_info['link'].'&reset_beans=1' ); ?>'>Reset Settings Now</a>
                </div>
            </div>
        </div>
	<?php endif; ?>
    <div style="margin-top: 20px !important;" >
        <img  src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/beans.svg"
              alt="Beans" width="5%">
    </div>
</div>

