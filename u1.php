<?php

header('Content-type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require("lib/lib.php");


if($_SERVER['HTTP_REFERER']!='http://dpekloges.gr/apps/vreg/p1.php'){
	$data['Error'] = 100;
	$data['ErrorDescr'] = '<h2>System Error!</h2>';
	die(json_encode($data));
}

session_start();
$con = openDB();
$_SESSION['RegistrationUID'] = uniqid();
$_SESSION['RegistrationSID'] = uniqid();
$errcode = 0;
$FName = trim($_POST['FName']);
$LName = trim($_POST['LName']);
$PName = trim($_POST['PName']);

$BirthYear = $_POST['BirthYear'];
$Age = date("Y") - $BirthYear;
$EidEklArithm = $_POST['EidEklArithm'];

if(empty($EidEklArithm)){
	$errcode = 101;
	$errdescr = "Παρακαλούμε αναζητήστε πρώτα τον Ειδικό Εκλογικό Αριθμό σας!";
}elseif(EidEklArExist($EidEklArithm, $con)){
	$errcode = 117;
	$errdescr = "Είστε ήδη καταχωρημένος!";
}else{
	$errcode = 0;
	$errdescr = '';
}

/*
mysqli_query($con, $sql);
$ID = mysqli_insert_id($con);
*/

mysqli_close($con);


$data['SID'] = $_SESSION['RegistrationSID'];
$data['Error'] = $errcode;
$data['ErrorDescr'] = $errdescr;
echo json_encode($data);


function EidEklArExist($EidEklArithm, $con){
	$sql = "SELECT ID FROM vreg_volunteers WHERE EidEklAr = '$EidEklArithm'";
	$result = mysqli_query($con, $sql);
	$num_rows = mysqli_num_rows($result);
	$r = ($num_rows > 0);
	return $r;
}


?>