<style>
	.action-icon{
		font-size: 28px;
		cursor: pointer;
	}
	.action-config-icon{
		color: blue;
	}
	.action-close-icon{
		color: red;
	}
</style>
<?php

if(isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] >0){
	$page = $_GET['p'];
}else{
	$page =0;
}
$mysqlpage = 20*$page;

if(isset($_GET['trail']) && $_GET['trail'] != ''){
	$searchtrail = 1;
}else{
	$searchtrail = 0;
}
?>
<!-- Settings -->
<div class="modal fade" id="myModalselectedtrails" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Settings for selected trails</h4>
			</div>
			<div class="modal-body">
				<!-- body -->
				<div style="text-align:left; display:; padding:20px;" id="setall" class="col-md-12">
					<form onsubmit="settingsforslectedtrails(); return false;">
						<b style="color:orange;">Read <a target="_blank" href="/faq.php">FAQ</a> before editing.</b><br><br>
						<div class="form-group" style="border:1px solid #ddd; padding:5px;">
							<strong>Settings for selected Trails</strong>
							<br><br>
							<div class="form-check" style="margin-bottom:5px;">
								<input class="form-check-input" type="checkbox" value="" id="enableall" checked>
								<label style="color:#2b0808;" class="form-check-label" id="enabling" for="defaultCheck1">
									Enable (uncheck for disabling)
								</label>
							</div>
							<div class="form-group" style="border:1px solid #ddd; padding:5px;">
								<label>Voting weight (%): (Default is 50%)</label>
								<input id="weightall" placeholder="Voting weight" name="weight" type="number" class="form-control" value="50" step="0.01" min="0" max="100">

								<div class="form-check">
									<label style="color:#2b0808;" class="form-check-label">
										<input class="form-check-input" type="radio" name="votingwayall" id="votingway" value="1" checked>
										Scale voting weight (default)
									</label>
								</div>
								<div class="form-check">
									<label style="color:#2b0808;" class="form-check-label">
										<input class="form-check-input" type="radio" name="votingwayall" id="votingway" value="2">
										Fixed voting weight
									</label>
								</div>
							</div>
							<label>Time to wait before voting (minutes): (Default is 0)</label>
							<input id="afterminall" value="0" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
							<input style="margin-top:10px;"value="Apply to selected trails" type="submit" class="btn btn-primary">
						</div>
					</form>
				</div>
			</div>
			<div style="border-top:0;" class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
//Select/unselect all function
	function togglecheckbox(source) {
		var checkboxes = document.getElementsByName('trail_id');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			checkboxes[i].checked = source.checked;
		}
	}
	function settingsforslectedtrails(){ //settings for trail
		$('.btn').attr('disabled','true');
		var minute = document.getElementById('afterminall').value;
		var votingway;
		var radios = document.getElementsByName('votingwayall');
		for (var i = 0, length = radios.length; i < length; i++){
			if (radios[i].checked){
				votingway = radios[i].value;
				break;
			}
		}
		var weight = document.getElementById('weightall').value;
		if(minute == '' || minute == null){
			minute = 0;
		}
		if(weight == '' || weight == null){
			weight = 50;
		}

		var enable;
		if(document.getElementById('enableall').checked){
			enable = 1;
		}else{
			enable = 0;
		}
		var checkboxes = document.getElementsByName('trail_id');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			if(checkboxes[i].checked){
				var user = checkboxes[i].id;
				const body = 'trail=' + encodeURIComponent(user) +
					'&weight=' + encodeURIComponent(weight) +
					'&minute=' + encodeURIComponent(minute) +
					'&votingway=' + encodeURIComponent(votingway) +
					'&enable=' + encodeURIComponent(enable)
				
				callApi('http://localhost/api/v1/dashboard/curation_trail/settings', body)
			}
		}
		return 1;
	}
	$(function(){
		$('#myModalselectedtrails').appendTo('body');
	});
	function modalforselectedtrails() {
		var checked = 0;
		var checkboxes = document.getElementsByName('trail_id');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			if(checkboxes[i].checked){
				checked = 1;
			}
		}
		if(checked){
			$('[id=\'myModalselectedtrails\']').modal('show');
		}else{
			$.notify({
				icon: 'pe-7s-attention',
				message: "Please select some trail from the list!"
			},{
				type: 'warning',
				timer: 1000
			});
		}
	}
</script>

<div class="content"> <!-- 1 -->
<? if($searchtrail == 1){	?>
		<div class="row" style="margin:0 !important"> <!-- 2 -->
			<div class="col-md-3"></div>
				<div class="col-md-6"> <!-- 3 -->
					<div class="card"> <!-- 4 -->
						<div class="content"> <!-- 5 -->
							<h3>Searching for "<span><? echo htmlspecialchars($_GET['trail']); ?></span>": </h3><br>
							<?
							$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `trailers` WHERE `user`=?)");
							$searchedtrail = $_GET['trail'];
							$stmt->bind_param('s', $searchedtrail);
							$stmt->execute();
							$result = $stmt->get_result();
							$row = $result->fetch_assoc();
							$stmt->close();
							foreach($row as $exists){}
							if($exists == 1){
								$stmt = $conn->prepare("SELECT * FROM `trailers` WHERE `user`=?");
								$stmt->bind_param('s', $searchedtrail);
								$stmt->execute();
								$result = $stmt->get_result();
								$row = $result->fetch_assoc();
								$stmt->close();
								$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `followers` WHERE `follower` =? AND `trailer`=?)");
								$stmt->bind_param('ss',$name,$searchedtrail);
								$stmt->execute();
								$stmt->bind_result($y);
								$stmt->fetch();
								$stmt->close();
								if($y == 1){
									$alreadyfollowed = 1;
								}else{
									$alreadyfollowed = 0;
								}

								?>
								<strong>Trail Name:</strong><span> <? echo htmlspecialchars($searchedtrail); ?></span><br>
								<strong>Description:</strong><span> <? echo htmlspecialchars($row['description']); ?></span><br>
								<strong>Followers:</strong><span> <? echo $row['followers']; ?> (<a href="/dash.php?i=15&id=1&user=<? echo htmlspecialchars($searchedtrail); ?>">View active followers</a>)</span><br><br>
								<? if($alreadyfollowed){ ?>
									<button onclick="if(confirm('Are you sure?')){unfollow('<? echo $row['user']; ?>');};" class="btn btn-danger" <? if($row['user'] == $name){echo 'disabled="disabled"';} ?>>UNFOLLOW</button>
									<button onclick="showset('1');" class="btn btn-primary" <? if($row['user'] == $name){echo 'disabled="disabled"';} ?>>Settings</button>
									<?

									$stmt = $conn->prepare("SELECT * FROM `followers` WHERE `follower` =? AND `trailer`=?");
									$stmt->bind_param('ss',$name,$searchedtrail);
									$stmt->execute();
									$result = $stmt->get_result();
									$n = $result->fetch_assoc();


									?>
									<!-- Settings -->
									<div class="row" style="margin:0 !important;">
										<div style="text-align:left; display:none; padding:20px;" id="set1" class="col-md-12">
											<form onsubmit="settings('<? echo $row['user']; ?>'); return false;">
												<b style="color:orange;">Read <a target="_blank" href="/faq.php">FAQ</a> before editing.</b><br><br>
												<div class="form-group" style="border:1px solid #ddd; padding:5px;">
													<strong>Settings for Trailer: <a href="https://steemit.com/@<? echo $row['user']; ?>" target="_blank">@<? echo $row['user']; ?></a></strong>
													<br><br>
													<div class="form-check" style="margin-bottom:5px;">
														<input class="form-check-input" type="checkbox" value="" id="enable<? echo $row['user']; ?>" <? if($n['enable']){echo 'checked';} ?>>
														<label style="color:#2b0808;" class="form-check-label" id="enabling" for="defaultCheck1">
															Enable (uncheck for disabling)
														</label>
													</div>
													<div class="form-group" style="border:1px solid #ddd; padding:5px;">
														<label>Voting weight (%): (Default is 50%)</label>
														<input id="weight<? echo $row['user']; ?>" placeholder="Voting weight" name="weight" type="number" class="form-control" value="<? echo $n['weight']/100; ?>" step="0.01" min="0" max="100">

														<div class="form-check">
															<label style="color:#2b0808;" class="form-check-label">
																<input class="form-check-input" type="radio" name="votingway<? echo $row['user']; ?>" id="votingway" value="1" <? if($n['votingway'] == 1){echo 'checked';} ?>>
																Scale voting weight (default)
															</label>
														</div>
														<div class="form-check">
															<label style="color:#2b0808;" class="form-check-label">
																<input class="form-check-input" type="radio" name="votingway<? echo $row['user']; ?>" id="votingway" value="2" <? if($n['votingway'] == 2){echo 'checked';} ?>>
																Fixed voting weight
															</label>
														</div>
													</div>


													<label>Time to wait before voting (minutes): (Default is 0)</label>
													<input id="aftermin<? echo $row['user']; ?>" value="<? echo $n['aftermin']; ?>" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30">



													<input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
												</div>
											</form>
										</div>
									</div>

									<!-- /Settings -->

								<? }else{ ?>
										<button onclick="if(confirm('Are you sure?')){follow('<? echo $row['user']; ?>');};" class="btn btn-primary" <? if($row['user'] == $name){echo 'disabled="disabled"';} ?>>FOLLOW</button>
								<? } ?>



								<?
							}else{ ?>
								<p style="color:red;">Sorry, that curation trail does not exist. If that account belongs to you, sign up on SteemAuto and create a trail.</p>
								<hr style="">
								<p>Or, you can follow anyway! We will create this trail automatically:</p>
								<button onclick="if(confirm('Are you sure?')){follow('<? echo htmlspecialchars($searchedtrail); ?>');};" class="btn btn-primary" <? if(htmlspecialchars($searchedtrail) == $name){echo 'disabled="disabled"';} ?>>Follow anyway</button>
							<?
							}
							?>
						</div> <!-- /5 -->
					</div> <!-- /4 -->
				</div> <!-- /3 -->
			<div class="col-md-3"></div>
		</div> <!-- /2 -->

		<?
	}else{ ?>
<div class="row" style="margin:0 !important"> <!-- 2 -->
<div class="col-md-3"></div>
<div class="col-md-6"> <!-- 3 -->
	<div class="card"> <!-- 4 -->
		<div class="content"> <!-- 5 -->
			<h3>Welcome <? echo $name; ?>,</h3><br>
			Here you can view a list of existing curation trails and follow them.<br>
			Following a curation trail means that you will automatically upvote each post that the trail upvotes.<br>
			If you don't want to follow a trail you can also: <a style="margin:5px;" class="btn btn-success" onclick="showbecome();">create/edit your curation trail</a>
			<form style="display:none;" id="become" onsubmit="become(); return false;">
				<label>Short Description:(max 100 character)</label>
				<textarea id="description"  name="description" type="text" class="form-control" required>
				</textarea>
				<input style="margin-top:10px;"value="Submit" type="submit" class="btn btn-primary">
			</form>
			<?
				$result = $conn->query("SELECT EXISTS(SELECT `user` FROM `trailers` WHERE `user`='$name')");
				foreach($result as $x){
					foreach($x as $x){}
				}
				if ($x == 1) {
					$result = $conn->query("SELECT `enable` FROM `trailers` WHERE `user`='$name'");
					foreach($result as $x){
						$ts = $x['enable'];
					}
			?>
					<div>
						<hr style="margin:0">
						<h4>Trail options:</h4>
						<p>Since you are a trail, you can pause your trail to not receive upvotes after your upvotes. Pause/Resume may take up to 10 minutes.</p>
						<br>
						Status: <? if($ts) {echo '<span style="color:green">Enable</span>';}else{echo '<span style="color:red">Paused</span>';} ?>
						<br>
						<button onclick="updateTrail();" class="btn <? if($ts){echo 'btn-danger'; $txt='Pause';}else{echo 'btn-success'; $txt='Resume';} ?>"><? echo $txt; ?></button>
					</div>
		<? } ?>
		</div> <!-- /5 -->
	</div> <!-- /4 -->
</div> <!-- /3 -->
<div class="col-md-3"></div>
</div> <!-- /2 -->

<div class="row" style="margin:0 !important"> <!-- 6 -->

	<div class="col-md-12"> <!-- 7 -->
		<div class="card"> <!-- card -->
			<div class="content"> <!-- content -->


				<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">
					You are following:
					<button style="float:right;" type="button" class="btn btn-primary" onclick="modalforselectedtrails();">Settings for selections</button>
				</h3>
				<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1"> <!-- 8 -->

					<?
					$result = $conn->query("SELECT EXISTS(SELECT * FROM `trailers`)");
					foreach($result as $x){
						foreach($x as $x){}
					}
					if($x == 0){
						echo 'None';
					}else{
						$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `follower`= '$name')");
						foreach($result as $y){
							foreach($y as $y){}
						}
						if($y == 1){
							?>


					<!-- Table starts here -->
						<table id="table" class="table table-hover table-mc-light-blue">
						  <thead>
							<tr>
								<th><input type="checkbox" name="" onclick="togglecheckbox(this);" value="" id="selectall"></th>
							  <th>#</th>
							  <th>Username</th>
							  <th>Followers</th>
							  <th>Weight</th>
							  <th>Method</th>
							  <th>Wait Time</th>
							  <th>Status</th>
							  <th>Action</th>
							</tr>
						  </thead>
						  <tbody>

								<?
								$result = $conn->query("SELECT * FROM `followers` WHERE `follower` = '$name'");
								$k = 1;
								$enb;
								foreach($result as $n){
									$nn = $n['trailer'];
									$result = $conn->query("SELECT * FROM `trailers` WHERE `user` = '$nn'");
									foreach($result as $b){
										$w = ($n['weight']/100).'%';
										if($n['votingway'] == 1){
											$method = 'Scale <abbr data-toggle="tooltip" title="Read FAQ">?</abbr>';
										}else{
											$method = 'Fixed <abbr data-toggle="tooltip" title="Read FAQ">?</abbr>';
										}
										if($n['enable'] == 0){
											$status = '<i style="color:red; font-size:24px;" title="Disabled" class="pe-7s-less"></i>';
											$enb =0;
										}else{
											$status = '<i style="color:green; font-size:24px;" title="Enabled" class="pe-7s-check"></i>';
											$enb =1;
										}
							?>

								<tr class="tr1">
									<td data-title="ID"><input type="checkbox" name="trail_id" value="" id="<? echo $b['user']; ?>"></td>
									<td data-title="ID"><? echo $k; ?></td>
									<td data-title="Name"><a href="/dash.php?i=1&trail=<? echo $b['user']; ?>" target="_blank">@<? echo $b['user']; ?></a></td>
									<td data-title="Status"><? echo $b['followers']; ?></td>
									<td data-title="Status"><? echo $w; ?></td>
									<td data-title="Status"><? echo $method; ?></td>
									<td data-title="Status"><? echo $n['aftermin']; ?> min</td>
									<td data-title="Status"><? echo $status; ?></td>

									<td data-title="Status">
									<a title="Settings" data-toggle="modal" onclick="$('[id=\'myModal<? echo $b['user']; ?>\']').modal('show');" class="pe-7s-config action-icon action-config-icon"></a>
									<a title="Delete" onclick="if(confirm('Are you sure?')){unfollow('<? echo $b['user']; ?>');};" class="pe-7s-close-circle action-icon action-close-icon"></a>
									</td>
								</tr>
								<!-- Settings -->
								<div class="modal fade" id="myModal<? echo $b['user']; ?>" role="dialog">
									<div class="modal-dialog">
									<!-- Modal content-->
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title">Settings: @<? echo $b['user']; ?></h4>
											</div>
											<div class="modal-body">
												<!-- body -->
												<div style="text-align:left; display:; padding:20px;" id="set<? echo $k; ?>" class="col-md-12">
													<form onsubmit="settings('<? echo $b['user']; ?>'); return false;">
														<b style="color:orange;">Read <a target="_blank" href="/faq.php">FAQ</a> before editing.</b><br><br>
														<div class="form-group" style="border:1px solid #ddd; padding:5px;">
															<strong>Settings for Trailer: <a href="https://steemit.com/@<? echo $b['user']; ?>" target="_blank">@<? echo $b['user']; ?></a></strong>
															<br><br>
															<div class="form-check" style="margin-bottom:5px;">
																<input class="form-check-input" type="checkbox" value="" id="enable<? echo $b['user']; ?>" <? if($n['enable']){echo 'checked';} ?>>
																<label style="color:#2b0808;" class="form-check-label" id="enabling" for="defaultCheck1">
																	Enable (uncheck for disabling)
																</label>
															</div>
															<div class="form-group" style="border:1px solid #ddd; padding:5px;">
																<label>Voting weight (%): (Default is 50%)</label>
																<input id="weight<? echo $b['user']; ?>" placeholder="Voting weight" name="weight" type="number" class="form-control" value="<? echo $n['weight']/100; ?>" step="0.01" min="0" max="100">
																<div class="form-check">
																	<label style="color:#2b0808;" class="form-check-label">
																		<input class="form-check-input" type="radio" name="votingway<? echo $b['user']; ?>" id="votingway" value="1" <? if($n['votingway'] == 1){echo 'checked';} ?>>
																		Scale voting weight (default)
																	</label>
																</div>
																<div class="form-check">
																	<label style="color:#2b0808;" class="form-check-label">
																		<input class="form-check-input" type="radio" name="votingway<? echo $b['user']; ?>" id="votingway" value="2" <? if($n['votingway'] == 2){echo 'checked';} ?>>
																		Fixed voting weight
																	</label>
																</div>
															</div>
															<label>Time to wait before voting (minutes): (Default is 0)</label>
															<input id="aftermin<? echo $b['user']; ?>" value="<? echo $n['aftermin']; ?>" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
															<input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
														</div>
													</form>
												</div>
											</div>
											<div style="border-top:0;" class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>
								<script>
								$(document).ready(function(){
									$('[id="myModal<? echo $b['user']; ?>"]').appendTo("body");
								});
								</script>




								<?
								$k += 1;
								}
								}
								?>
							</tbody>
						</table>
						<?
						}else{
							echo 'None.';
						}
					}
					?>
				</div> <!-- /8 -->
			</div> <!-- /content -->
		</div> <!-- /card -->
	</div> <!-- /7 -->
</div> <!-- /6 -->

<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<div class="card">
			<div class="content">
				<h3>Search for a trail:</h3>
				<hr style="margin:10px;">
				<form action="/dash.php" class="form" method="GET">
					<label for="trail">Trail name:</label><input class="form-control" id="trail" placeholder="steemauto" name="trail" type="text" required/>
					<input name="i" type="number" value="1" style="display:none;" required>
					<input style="margin-top:7px;" class="btn btn-primary" value="Search" type="submit"/>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-3"></div>
</div>

<div class="row" style="margin:0 !important"> <!-- 9 -->
	<div class="col-md-12"> <!-- 10 -->
		<div class="card"> <!-- card -->
			<div class="content"> <!-- content -->
				<!-- -->
				<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">Popular Curation Trails: </h3>

				<?
				$result = $conn->query("SELECT EXISTS(SELECT * FROM `trailers` ORDER BY `trailers`.`followers` DESC LIMIT $mysqlpage,20)");
				foreach($result as $x){
					foreach($x as $x){}
				}
				if($x == 0){
					echo 'None';
				}else{
					$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `follower` = '$name')");
					foreach($result as $y){
						foreach($y as $y){}
					}
					$rrr = 0;
					if($y == 1){
						$result = $conn->query("SELECT `trailer` FROM `followers` WHERE `follower` = '$name'");
						$r = 0;
						foreach($result as $y){
							foreach($y as $y){
								$uze[$r]=$y;
								$r = $r+ 1;
								$rrr = 1;
							}
						}
					}
					?>

				<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1">
				  <!-- Table starts here -->

					<table id="table" class="table table-hover table-mc-light-blue">
						<thead>
							<tr>
								<th>#</th>
								<th>Username</th>
								<th>Description</th>
								<th>Followers</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $mysqlpage+1;
							$result = $conn->query("SELECT * FROM `trailers` ORDER BY `trailers`.`followers` DESC LIMIT $mysqlpage,20");
								foreach($result as $x){
									$s = 0;
									if($rrr = 1){
										foreach($uze as $u){
											if($u == $x['user']){
												$s = 1;
											}
										}
									}
							?>
							<tr class="tr2">
								<td data-title="ID"><? echo $i; ?></td>
								<td data-title="Name"><a href="/dash.php?i=1&trail=<? echo $x['user']; ?>" target="_blank">@<? echo $x['user']; ?></a></td>
								<td data-title="Link"><? echo substr(strip_tags($x['description']),0,100); ?></td>
								<td data-title="Status"><? echo $x['followers']; ?></td>
								<? if($x['user']!=$name && $s ==0){ ?>
								<td data-title="Status">
									<button onclick="if(confirm('Are you sure?')){follow('<? echo $x['user']; ?>');};" class="btn btn-primary">FOLLOW</button>
								</td>
								<? }elseif($s == 1){ ?>
								<td data-title="Status">
									<button onclick="if(confirm('Are you sure?')){unfollow('<? echo $x['user']; ?>');};" class="btn btn-danger">UNFOLLOW</button>
								</td>
								<? }else{ ?>
								<td data-title="Status">

								</td>
								<? } ?>
							</tr>
									<?
									$i += 1;
								}
								?>
							  </tbody>
						</table>
						<div class="col-md-12" style="text-align:center;">
							<? if($page>0){ ?> <a class="btn btn-primary" href="/dash.php?i=1">First page</a>
								<a class="btn btn-primary" href="/dash.php?i=1&p=<? echo $page-1; ?>">Previous page</a> <? } ?>
							<a class="btn btn-primary" href="/dash.php?i=1&p=<? echo $page+1; ?>">Next page</a>
						</div>
					</div>
				<? } ?>
			</div><!-- /content -->
		</div><!-- /card -->
	</div><!-- /10 -->

</div><!-- /9 -->

	<? } ?>
</div><!-- /1 -->
