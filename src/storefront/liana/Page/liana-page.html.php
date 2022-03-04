<?php

use BeansWoo\Helper;

defined('ABSPATH') or die;

\BeansWoo\StoreFront\Auth::forceCustomerAuthentication();
$is_powerby = Helper::getConfig('liana_is_powerby');
?>
<div id="liana-rewards-page">
<!-- DO NOT TOUCH -->
</div>

<?php if (is_null($is_powerby) | $is_powerby == true) : ?>
<div style="margin: 50px auto 20px; max-width: 800px; text-align: center">
  <p style="text-align: center">
    This <a href="https://<?php echo Helper::getDomain('WWW'); ?>/liana"
            target="_blank">rewards program</a> is powered by Beans
  </p>
</div>
<?php endif; ?>