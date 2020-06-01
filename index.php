<?php
$active =0;
$ROOT  = dirname(__FILE__);
$BACKENDSERVER  = 'http://127.0.0.1/';

require_once('inc/conf/db.php');
require_once('inc/dep/login_register.php');
// echo $log;
if($log){
	header("Location: /dash.php");
}else{
	include('templates/steemauto-design-02/index.html');
}
?>
