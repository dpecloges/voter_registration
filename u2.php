<?php
header('Content-type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require("lib/lib.php");


session_start();
$referer = "http://dpekloges.gr/apps/vreg/p2.php?SID=" . $_SESSION['RegistrationSID'];

if($_SERVER['HTTP_REFERER']!=$referer){
	$data['Error'] = 100;
	$data['ErrorDescr'] = '<h2>System Error!</h2>';
	//die(json_encode($data));
}

$AddressStreetName = $_POST['StreetName'];
$AddressStreetNumber = $_POST['StreetNumber'];
$Municipality = $_POST['Municipality'];
$AddressZip = $_POST['Zip'];
$Division = $_POST['Division'];
$AddressCountry = $_POST['Country'];
$NoNumbersAddress = $_POST['NoNumbersAddress']==1;
$FixedPhone = $_POST['FixedPhone'];
$AddressIsValid =
		!empty($AddressStreetName) && 
		(!empty($AddressStreetNumber) || $NoNumbersAddress) && 
		!empty($AddressZip) && 
		$AddressCountry == 'Ελλάδα';

$errcode = 0;
if($_SESSION['Email_PIN_Validated']!==TRUE){
	$errcode = 101;
	$errdescr = "Παρακαλούμε επαληθεύεστε το email σας!";
}elseif($_SESSION['Mobile_PIN_Validated']!==TRUE){
	$errcode = 102;
	$errdescr = "Παρακαλούμε επαληθεύεστε το κινητό σας τηλέφωνο!";
}elseif(!$AddressIsValid){
	$errcode = 103;
	$errdescr = "Η διεύθυνση δεν είναι έγκυρη!";
}else{
	$_SESSION['RegistrationSID'] = uniqid();
	$_SESSION['StreetName'] = $AddressStreetName;
	$_SESSION['StreetNumber'] = $AddressStreetNumber;
	$_SESSION['ZipCode'] = $AddressZip;
	$_SESSION['Municipality'] = $Municipality;
	$_SESSION['Division'] = $Division;
	$_SESSION['FixedPhone'] = $FixedPhone;
	$_SESSION['NoNumbersAddress'] = $NoNumbersAddress;	
	$errcode = 0;
	$errdescr = '';
}

$data['SID'] = $_SESSION['RegistrationSID'];
$data['Error'] = $errcode;
$data['ErrorDescr'] = $errdescr;
echo json_encode($data);


?>