<?php

use BeansWoo\Helper;

defined('ABSPATH') or die;

\BeansWoo\StoreFront\Auth::forceCustomerAuthentication();

?>
<div id="liana-rewards-page">
<!-- DO NOT TOUCH -->
</div>
<div class ="beans-page-footer">
  <p>
    This <a href="https://<?php echo Helper::getDomain('WWW'); ?>/liana"
            target="_blank">rewards program</a> is powered by Beans
  </p>
</div>
