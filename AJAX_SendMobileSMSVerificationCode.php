<?php
session_start();
require_once("Classes/DBConnection.php"); 


require_once("Classes/SMSController.php"); 
$fSMSController = new SMSController();



$fMobile = strtolower(trim($_POST['Mobile']));
$fUniqueKey = $_POST['UniqueKey'];
$fRandomPin = rand(1000,9999);


$sql = "UPDATE `voter_registration_temp` SET `MobilePhone`=?,`MobileIsVerified`=0,`MobileVerificationPin`=? WHERE `UniqueKey`=?";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fMobile);
$command->Parameters->setInteger(2, $fRandomPin);
$command->Parameters->setString(3, $fUniqueKey);
$command->ExecuteQuery();

//Send Pin to Mobile
$fSMSController ->SendSMSVerificationPin($fMobile ,$fRandomPin);

$connection->Close();
exit;
?>