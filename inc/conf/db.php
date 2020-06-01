<?php
$server="localhost";
$user="root";
$pw="";
$db="steemauto";
$conn = new mysqli($server,$user,$pw,$db);
$conn->set_charset('utf8mb4');
// $BACKENDSERVER  = 'http://127.0.0.1/';
// $FRONTEND = "http://localhost/";
$BACKENDSERVER  = 'http://auto.steemdb.online/';
$FRONTEND = "http://auto.steemdb.online/";
?>
