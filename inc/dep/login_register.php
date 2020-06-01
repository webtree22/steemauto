<?php

if(isset($_COOKIE['access_key']) && isset($_COOKIE['username'])){
	// echo 'asdf<hr/>';
	$access_key = $_COOKIE['access_key'];
	$username = $_COOKIE['username'];
	$loginUrl = $BACKENDSERVER . 'api/v1/login';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $loginUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("cookie: access_key=$access_key;username=$username"));
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds 
	$result = curl_exec($ch);
	// echo $result;
	if($result && json_decode($result)->id == 1){
		$name = $username;
		$log = 1;
	}else{
		$log = 0;
	}
}else{
	$log = 0;
}

?>
