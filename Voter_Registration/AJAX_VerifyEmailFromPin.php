<?php
session_start();
require_once("Classes/DBConnection.php"); 
header('Content-type: application/json');

$fUniqueKey = $_POST['UniqueKey'];
$fPin = intval($_POST['Pin']);

$verificationPinInDB = 0;
$sql = "SELECT `EMailVerificationPin` FROM `voter_registration_temp` WHERE `UniqueKey` = ?";
$command = new MySqlCommand($connection,$sql);
$command->Parameters->setString(1,$fUniqueKey);
$reader = $command->ExecuteReader();
while($reader->Read())
{
	$verificationPinInDB = intval($reader->getValue(0));
}
$reader->Close();

if($verificationPinInDB == $fPin && $fPin!=null && $fPin>0)
{
	$sql = "UPDATE `voter_registration_temp` SET `EMailIsVerified`=1 WHERE `UniqueKey` = ?";
	$command = new MySqlCommand($connection,$sql);	
	$command->Parameters->setString(1,$fUniqueKey);
	$command->ExecuteQuery();
	
	$data['Error'] = 0;
	$data['ErrorDescr'] = $errdescr;
	echo json_encode($data);
}
else
{
	$connection->Close();
	$data['Error'] = 101;
	$data['ErrorDescr'] = 'Ο κωδικός επαλήθευσης δεν είναι σωστός!';
	die(json_encode($data));
}

$connection->Close();
exit;
?>