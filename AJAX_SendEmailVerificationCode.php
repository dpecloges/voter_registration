<?php
session_start();
require_once("Classes/DBConnection.php"); 

require_once("Classes/Mailer.php"); 
$fMailer = new Mailer();

$fEMail = strtolower(trim($_POST['Email']));
$fUniqueKey = $_POST['UniqueKey'];
$fRandomPin = rand(1000,9999);

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


exit;
?>