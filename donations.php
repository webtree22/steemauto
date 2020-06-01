<?php
require_once('inc/conf/db.php');
require_once('inc/dep/login_register.php');
$active = 0;
require_once('inc/temp/head.php');

$url1= 'https://api.coinmarketcap.com/v1/ticker/steem/';
$url2= 'https://api.coinmarketcap.com/v1/ticker/steem-dollars/';
$file1 = file_get_contents($url1);
$file2 = file_get_contents($url2);
$file1 = json_decode($file1);
$file2 = json_decode($file2);

$result = $conn->query("SELECT `amount` FROM `donations` WHERE `month`=1 AND `type`='SBD'");
foreach ($result as $amount) {
	$sbd = $amount['amount'];
}
$result = $conn->query("SELECT `amount` FROM `donations` WHERE `month`=1 AND `type`='STEEM'");
foreach ($result as $amount) {
	$steem = $amount['amount'];
}
$donations = ($steem*$file1[0]->price_usd)+($sbd*$file2[0]->price_usd);
$donations = round($donations,2);

?>

<style>
label{
	margin-top:11px;
}
</style>

<div class="content" ng-app="viewer_app" ng-controller="viewer_controller"> <!-- Content -->
	<div class="row" style="margin:0 !important">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<div class="card">
				<div class="content">
					<h4 style="border-bottom:1px solid #000;">Support Steemauto</h4>
					<p>Hello, Steemauto users. As you know, Steemauto is a free service for all steem users and we don't have any earnings from Steemauto.
						 But, we need to pay for our servers. I know, that is not your business. Just if you can, help us to keep this service free forever.
					</p>
					<h4 style="border-bottom:1px solid #000;">Upvoting</h4>
					<p>We are publishing daily posts by 
						<a href="https://steemit.com/@steemauto.app" target="_blank">@steemauto.app</a> account. You can follow @steemauto.app in the (Fanbase or Curation trail) to upvote these daily posts.
						We may change from daily posts to the weekly or monthly posts if we get enough upvotes. 
						All rewards from posts of @steemauto.app goes to the steemauto's costs.
					</p>
					<h4 style="border-bottom:1px solid #000;">Witness vote</h4>
					<p>You can support this project by voting @mahdiyari as a steem witness.
					 A witness receives small rewards from steem blockchain depending on witness votes.
					 <br />
					 <a href="https://steemlogin.com/sign/account-witness-vote?witness=mahdiyari&approve=1">Click here to vote by steemconnect</a>
					</p>
					<h4 style="border-bottom:1px solid #000;">Donations</h4>
					<h5>Our bills:</h5>
					<p>1- 256 GB + 64 GB RAM servers: $200/month</p>
					<p>2- Mastercard/Visa: ~$20/month</p>
					<hr>
					<p>Total bill: $220/month</p>
					<br><br>
					<p>You can send your donations (SBD/STEEM) to @steemauto.app account. Or use below buttons.</p>
					<h4>Easy donation buttons:</h4>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=1%20SBD">1 SBD</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=5%20SBD">5 SBD</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=10%20SBD">10 SBD</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=20%20SBD">20 SBD</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=50%20SBD">50 SBD</a>
					<br><br>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=1%20STEEM">1 STEEM</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=5%20STEEM">5 STEEM</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=10%20STEEM">10 STEEM</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=20%20STEEM">20 STEEM</a>
					<a class="btn btn-primary" href="https://steemlogin.com/sign/transfer?to=steemauto&amount=50%20STEEM">50 STEEM</a>
				</div>
			</div>
		</div>
		<div class="col-md-2"></div>
	</div>
</div> <!-- /Content -->


<?php
require('inc/temp/footer.php');

?>
