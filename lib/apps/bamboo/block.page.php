<?php
use BeansWoo\Helper;
?>
<script type="text/javascript">
    (function (e) {
        if (e.Beans3) return; var r = {};r._q = [];
        function a(e) {r[e] = function () {r._q.push([e].concat([].slice.call(arguments, 0)))}}
        var i = ["init", "get", "post", "put", "delete", "updateDisplay", "getAccountID", "setAccountID", "setAccountToken"];
        for (var o = 0; o < i.length; o++) {a(i[o])}e.Beans3 = r
    })(window);
</script>
<div class="container main content main-wrapper wrapper wrapper--margins">
  <div id="bamboo-referral-page">
    <!-- DO NOT TOUCH -->
  </div>
</div>

<link rel="stylesheet" href="https://bamboo.trbyeans.com/static/beans-referral-page/beans-referral-page.min.css">
<script type="text/javascript" src="https://bamboo.trbyeans.com/static/beans-referral-page/beans-referral-page.js"></script>

<div style="margin: 50px auto 20px; max-width: 800px; text-align: center">
  <p style="text-align: center">
    This <a href="https://<?php echo Helper::getDomain('WWW');?>/bamboo">referral program</a> is powered by Beans
  </p>
</div>
