<?php

use BeansWoo\Helper;

try {
    $loginkey = Helper::API()->post( 'core/user/current/loginkey' );
} catch ( \Beans\Error\BaseError  $e ) {}

$apps = Helper::getApps();
$card = array();

foreach ( $apps as $app_name => &$app_info ) {
    $app_info['instance'] = Helper::getCard( $app_name );
    if ( ! empty( $app_info['instance'] ) ) {
        $card = $app_info['instance'];
    }
}

?>

<style>
  .beans-admin-container {
    text-align: center;
  }

  .beans-admin-container div {
    display: block;
    margin: auto;
  }

  .beans-admin-logo {
    height: 40px;
    width: auto;
    display: block;
    margin: 40px auto 20px;
  }

  .beans-admin-content {
    max-width: 600px;
    text-align: left;
    padding: 30px 60px;
    box-sizing: border-box;
  }

  p.beans-admin-check-warning {
    background-color: #d70000;
    color: #ffffff;
    font-weight: 500;
    padding: 5px;
  }

  p.beans-admin-check-warning a {
    color: #fffca6;
  }

  p.beans-admin-check-warning a:hover {
    color: #dce249;
  }

  .beans-admin-form {

  }
  .beans-apps-table{
    width: 100%;
  }

  .beans-apps-table img{
    height: 30px;
    width: auto;
  }
  .beans-apps-table td{
    padding: 20px 0;
  }
  .beans-apps-table p{
    margin: auto;
  }

</style>
<div class="beans-admin-container">

  <img class="beans-admin-logo" src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/beans.svg"
       alt="Beans">

  <?php if(empty($card)): ?>

  <div class="welcome-panel beans-admin-content" style="max-width: 600px; margin: auto">
    <p class="beans-admin-check-warning">
      Unable to connect to Beans. Unable to retrieve information about your account status.
      Please:
      <a href="mailto:hello@trbyeans.com" target="_blank">Contact the Beans Team</a> for assistance.
      Attach a screenshot of this page to your email.
    </p>

    <div style='margin: 20px auto'>
      <a  style="color: #d70000; float: right" href='<?php echo admin_url( 'admin.php?page=beans-woo&reset_beans=1' ); ?>'>Reset Settings Now</a>
    </div>
  </div>

  <?php else: ?>

  <div class="welcome-panel beans-admin-content" style="max-width: 600px; margin: auto">
    <h1>Connected</h1>
    <p>
      Your store <b><?php echo $card['company_name']; ?></b>
      is successfully connected to Beans!
    </p>
    <p>
      Your store unique identifier <b><?php echo $card['address']; ?></b>
    </p>

    <div>
      <div class="beans-admin-form">
        <p class="wc-setup-actions step">
          <a class="button button-hero"
             href="https://<?php echo Helper::getDomain( 'CONNECT' ) . "/auth/login/${loginkey['key']}"; ?>" target="_blank">
            Go To Beans
          </a>
        </p>
      </div>
    </div>

    <h3> Installed Apps </h3>
    <table class="beans-apps-table">
        <?php foreach ( $apps as $app_name => &$app_info ): ?>
          <tr>
            <td>
              <div><img src="https://trybeans.s3.amazonaws.com/static-v3/connect/img/app/logo-full-<?php echo $app_name; ?>.svg"></div>
              <p><?php echo $app_info['description'] ?></p>
            </td>
            <td>
                <?php if ( isset( $app_info['instance'] ) ): ?>
                  <span>Installed</span>
                <?php else: ?>
                  <span>Not Installed</span>
                <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
    </table>
    <div style='margin: 20px auto'>
      <p>
      If you like Beans, please
      <a  href='https://wordpress.org/support/plugin/beans-woocommerce-loyalty-rewards/reviews/' target="_blank">
        leave us a ★★★★★ rating
      </a>
      </p>
      <a  style="color: #d70000; float: right" href='<?php echo admin_url( 'admin.php?page=beans-woo&reset_beans=1' ); ?>'>Reset Settings Now</a>
    </div>
  </div>
  <?php endif; ?>
</div>

