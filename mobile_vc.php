<?php

header('Content-type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require("lib/lib.php");


session_start();

if($_SERVER['HTTP_REFERER']!=$referer){
	$data['Error'] = 100;
	$data['ErrorDescr'] = '<h2>System Error!</h2>';
	//die(json_encode($data));
}


if($_SESSION['Mobile_PIN']!=$_POST['PIN']){
	$_SESSION['Mobile_PIN_Validated'] = FALSE;
	$data['Error'] = 101;
	$data['ErrorDescr'] = 'Ο κωδικός επαλήθευσης δεν είναι σωστός!';
	die(json_encode($data));
}


$_SESSION['Mobile_PIN_Validated'] = TRUE;
$errcode = 0;

mysqli_close($con);
$data['Error'] = $errcode;
$data['ErrorDescr'] = $errdescr;
echo json_encode($data);




?>