<?php
use BeansWoo\Helper;
?>

<div class="container main content main-wrapper wrapper wrapper--margins">
	<div id="bamboo-referral-page">
		<!-- DO NOT TOUCH -->
	</div>
</div>

<div class="container main content main-wrapper wrapper wrapper--margins">
	<div class="rewards-page">
		<div class="rewards-page-grid">
			<div class="rewards-page-grid-item">
				<div id="beans-rewards">
					<!-- DO NOT TOUCH -->
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript" src="https://npmcdn.com/react@15.3.0/dist/react.js"></script>
<script type="text/javascript" src="https://npmcdn.com/react-dom@15.3.0/dist/react-dom.js"></script>
<?php if ( strpos(BEANS_DOMAIN_API, 'bns') !== flase ):  ?>
    <script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/bnsre/static-v3/bamboo/lib/3.0/js/beans-referral-page.js"></script>
    <link type="text/css" rel="stylesheet" href="https://bnsre.s3.amazonaws.com/lib/bamboo/3.1/css/bamboo.beans.min.css">
<?php else: ?>
    <script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/trybeans/static-v3/bamboo/lib/3.0/js/beans-referral-page.js"></script>
    <link type="text/css" rel="stylesheet" href="https://trybeans.s3.amazonaws.com/lib/bamboo/3.1/css/bamboo.beans.min.css">
<?php endif; ?>
<div style="margin: 50px auto 20px; max-width: 800px; text-align: center">
	<p style="text-align: center">
		This <a href="https://www.trybeans.com/bamboo">Referrals program</a> is powered by Beans
	</p>
</div>
