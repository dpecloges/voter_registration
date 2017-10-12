<?php
session_start();
require_once("Classes/DBConnection.php"); 
header('Content-type: application/json');

require_once("Classes/Mailer.php"); 
$fMailer = new Mailer();

$fSecondsAllowedToSendEMail = 20;

$fEMail = strtolower(trim($_POST['Email']));
$fUniqueKey = $_POST['UniqueKey'];
$fRandomPin = rand(1000,9999);

// Check if email is already registered
$fEmailIsAlreadyInUse = false;
$sql = "SELECT `ID` FROM `voter_registration` WHERE `EMail`=?";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fEMail);
$reader = $command->ExecuteReader();
if($reader->Read())
{
	$fEmailIsAlreadyInUse = true;
}
$reader->Close();

if($fEmailIsAlreadyInUse)
{
	$data['Error'] = 100;
	$data['ErrorDescr'] = "Η διεύθυνση email ήδη χρησιμοποιείται!";
	echo json_encode($data);
	exit;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check if last pin was sent earlier than 2 minutes ago
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$fLastEMailSentSecondsAgo = 999;
$sql = "SELECT UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(`LastEMailSentTimestamp`) FROM `voter_registration_emails_timestamps` WHERE `EMail`=? LIMIT 0,1";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fEMail);
$reader = $command->ExecuteReader();
if($reader->Read())
{
	$fLastEMailSentSecondsAgo = $reader->getValue(0);
}
$reader->Close();

if($fLastEMailSentSecondsAgo >$fSecondsAllowedToSendEMail)
{
	// Delete before Insert
	$sql = "DELETE FROM `voter_registration_emails_timestamps` WHERE `EMail`=?";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setString(1, $fEMail);
	$command->ExecuteQuery();

	// Insert
	$sql = "INSERT INTO `voter_registration_emails_timestamps` (`EMail`,`LastEMailSentTimestamp`) VALUES (?,CURRENT_TIMESTAMP)";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setString(1, $fEMail);
	$command->ExecuteQuery();	
}
else
{
	if($fLastEMailSentSecondsAgo < $fSecondsAllowedToSendEMail)
	{
		$connection->Close();
		$data['Error'] = 120;
		$data['ErrorDescr'] = "Ο κωδικός επαλήθευσης σας έχει σταλεί.<br>Παρακαλώ περιμένετε άλλα ".($fSecondsAllowedToSendEMail-$fLastEMailSentSecondsAgo ).' δευτερόλεπτα πριν στείλετε νέο.';
		echo json_encode($data);
		exit;
	}
}



// Get First and Last Name
$sql = "SELECT `FirstName`,`LastName` FROM `voter_registration_temp` WHERE `UniqueKey`=?";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fUniqueKey);
$reader = $command->ExecuteReader();
while($reader->Read())
{
	$fFirstName = $reader->getValue(0);
	$fLastName = $reader->getValue(1);
	$fFullname = $fFirstName . ' ' . $fLastName;
}
$reader->Close();


$sql = "UPDATE `voter_registration_temp` SET `EMail`=?,`EMailIsVerified`=0,`EMailVerificationPin`=? WHERE `UniqueKey`=?";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fEMail);
$command->Parameters->setInteger(2, $fRandomPin);
$command->Parameters->setString(3, $fUniqueKey);
$command->ExecuteQuery();

$connection->Close();

// Send Pin to email
$fMailer->SendEMailVerificationPin($fEMail,$fFullname , $fRandomPin);

$data['Error'] = 0;
$data['ErrorDescr'] = $errdescr;
echo json_encode($data);


exit;
?>