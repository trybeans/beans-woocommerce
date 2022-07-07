<?php

use BeansWoo\Helper;

defined('ABSPATH') or die;

\BeansWoo\StoreFront\Auth::forceCustomerAuthentication();
?>
<div id="liana-rewards-page">
  <!-- DO NOT TOUCH -->
  <div style="display: flex;
              flex-direction: column;
              width: 100%;
              justify-content: center;
              align-items: center;
              text-align: center;"
  >
    <img src="https://cdn.trybeans.com/lib/ultimate/lts/assets/liana-loading.gif" 
        width="100px" height="100px" alt="liana-loading" 
    />
  </div>
</div>

<?php if (\BeansWoo\StoreFront\LianaPage::$is_powerby == true) : ?>
<div style="margin: 50px auto 20px; max-width: 800px; text-align: center">
  <p style="text-align: center">
    This <a href="https://<?php echo Helper::getDomain('WWW'); ?>/liana"
            target="_blank">rewards program</a> is powered by Beans
  </p>
</div>
<?php endif; ?>