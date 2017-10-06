<?php
session_start();
require_once("Classes/DBConnection.php"); 
header('Content-type: application/json');

require_once("Classes/SMSController.php"); 
$fSMSController = new SMSController();

$fMobile = strtolower(trim($_POST['Mobile']));
$fUniqueKey = $_POST['UniqueKey'];
$fRandomPin = rand(1000,9999);


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check if mobile is already registered
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$fMobileIsAlreadyRegistered = false;
$sql = "SELECT `ID` FROM `voter_registration` WHERE `MobilePhone`=?";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fMobile);
$reader = $command->ExecuteReader();
if($reader->Read())
{
	$fMobileIsAlreadyRegistered = true;
}
$reader->Close();

if($fMobileIsAlreadyRegistered )
{
	$connection->Close();
	$data['Error'] = 100;
	$data['ErrorDescr'] = "Το κινητό τηλέφωνο ήδη χρησιμοποιείται!";
	echo json_encode($data);
	exit;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$sql = "UPDATE `voter_registration_temp` SET `MobilePhone`=?,`MobileIsVerified`=0,`MobileVerificationPin`=? WHERE `UniqueKey`=?";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fMobile);
$command->Parameters->setInteger(2, $fRandomPin);
$command->Parameters->setString(3, $fUniqueKey);
$command->ExecuteQuery();
$connection->Close();

//Send Pin to Mobile
try
{
	$fSMSController->SendSMSVerificationPin($fMobile ,$fRandomPin);
}
catch(Exception $ex)
{
	$connection->Close();
	$data['Error'] = 110;
	$data['ErrorDescr'] = "Το sms δεν ηταν δυνατό να σταλθεί. Δοκιμάστε αργότερα";
	echo $data;
	exit;
}


$data['Error'] = 0;
$data['ErrorDescr'] = "";
echo json_encode($data);
exit;
?>