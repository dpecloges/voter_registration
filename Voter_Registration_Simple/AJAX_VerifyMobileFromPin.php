<?php
session_start();
require_once("Classes/DBConnection.php"); 

header('Content-type: application/json');

$fUniqueKey = $_POST['UniqueKey'];
$fPin = intval($_POST['Pin']);

$verificationPinInDB = 0;
$fMobilePhone ="";
$sql = "SELECT `MobileVerificationPin`,`MobilePhone` FROM `voter_registration_temp` WHERE `UniqueKey` = ?";
$command = new MySqlCommand($connection,$sql);
$command->Parameters->setString(1,$fUniqueKey);
$reader = $command->ExecuteReader();
while($reader->Read())
{
	$verificationPinInDB = intval($reader->getValue(0));
	$fMobilePhone = intval($reader->getValue(1));
}
$reader->Close();

if($verificationPinInDB == $fPin && $fPin!=null && $fPin>0)
{
	$sql = "UPDATE `voter_registration_temp` SET `MobileIsVerified`=1 WHERE `UniqueKey` = ?";
	$command = new MySqlCommand($connection,$sql);	
	$command->Parameters->setString(1,$fUniqueKey);
	$command->ExecuteQuery();
	
	$sql = "DELETE FROM `voter_registration_mobilenumbers_timestamps` WHERE `MobileNo`=?";
	$command = new MySqlCommand($connection,$sql);	
	$command->Parameters->setString(1,$fMobilePhone);
	$command->ExecuteQuery();

	
	
	
	$data['Error'] = 0;
	$data['ErrorDescr'] = $errdescr;
	echo json_encode($data);
}
else
{
	$data['Error'] = 101;
	$data['ErrorDescr'] = 'Ο κωδικός επαλήθευσης δεν είναι σωστός!';
	die(json_encode($data));
}

$connection->Close();
exit;
?>