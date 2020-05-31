<?php
$active =0;
define('__ROOT__', dirname(dirname(__FILE__)));

require_once('inc/conf/db.php');
require_once('inc/dep/login_register.php');
// echo $log;
if($log){
	header("Location: /dash.php");
}else{
	include('templates/steemauto-design-02/index.html');
}
?>
