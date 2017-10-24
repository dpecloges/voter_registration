<?php
session_start();
require_once("Classes/DBConnection.php"); 
header('Content-type: application/json');

require_once("Classes/SMSController.php"); 
$fSMSController = new SMSController();

$fSecondsAllowedToSendSMS = 25;

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
// Check if last pin was sent earlier than 2 minutes ago
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$fLastSMSSentSecondsAgo = 999;
$sql = "SELECT UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(`LastSMSSentTimestamp`) FROM `voter_registration_mobilenumbers_timestamps` WHERE `MobileNo`=? LIMIT 0,1";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setInteger(1, $fMobile);
$reader = $command->ExecuteReader();
if($reader->Read())
{
	$fLastSMSSentSecondsAgo = $reader->getValue(0);
}
$reader->Close();

if($fLastSMSSentSecondsAgo>$fSecondsAllowedToSendSMS)
{
	// Delete before Insert
	$sql = "DELETE FROM `voter_registration_mobilenumbers_timestamps` WHERE `MobileNo`=?";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setString(1, $fMobile);
	$command->ExecuteQuery();

	// Insert
	$sql = "INSERT INTO `voter_registration_mobilenumbers_timestamps` (`MobileNo`,`LastSMSSentTimestamp`) VALUES (?,CURRENT_TIMESTAMP)";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setString(1, $fMobile);
	$command->ExecuteQuery();	
}
else
{
	$secondsPassed = time()- $fLastSMSSent;
	if($fLastSMSSentSecondsAgo<$fSecondsAllowedToSendSMS)
	{
		$connection->Close();
		$data['Error'] = 120;
		$data['ErrorDescr'] = "Ο κωδικός επαλήθευσης σας έχει σταλεί.<br>Παρακαλώ περιμένετε άλλα ".($fSecondsAllowedToSendSMS-$fLastSMSSentSecondsAgo).' δευτερόλεπτα πριν στείλετε νέο.';
		echo json_encode($data);
		exit;
	}
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
	echo json_encode($data);
	exit;
}


$data['Error'] = 0;
$data['ErrorDescr'] = "";
echo json_encode($data);
exit;
?>