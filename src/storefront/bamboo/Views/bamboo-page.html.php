<?php

use BeansWoo\Helper;

defined('ABSPATH') or die;
?>
<div id="bamboo-referral-page">
<!-- DO NOT TOUCH -->
  <div style="display: flex;
              flex-direction: column;
              width: 100%;
              justify-content: center;
              align-items: center;
              text-align: center;"
  >
    <img src="https://cdn.trybeans.com/lib/ultimate/lts/assets/bamboo-loading.gif" 
         width="100px" height="100px" alt="bamboo-loading" 
    />
  </div>
</div>

<?php if (\BeansWoo\StoreFront\BambooPage::$is_powerby == true) : ?>
<div style="margin: 50px auto 20px; max-width: 800px; text-align: center">
  <p style="text-align: center">
    This
    <a href="https://<?php echo Helper::getDomain('WWW'); ?>/bamboo" target="_blank">
      referral program
    </a>
    is powered by Beans
  </p>
</div>
<?php endif; ?>
