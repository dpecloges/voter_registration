<?php  
	require_once("../lib/config/config.php");
 	require_once("../Classes/DBConnection.php");

	require_once("../Classes/Mailer.php");
 	$fMailer = new Mailer();
 	


	$fUniqueKey = $_POST['invoice'];
	$fPayPalParameters = "";
	try
	{
		foreach ($_POST as $param_name => $param_val) { $fPayPalParameters .= $param_name ."=". $param_val."\n"; }
	}
	catch(Exception $ex)
	{
	}
	
	
	$sql = 'INSERT INTO `voter_registration` (
							voter_registration.UniqueKey,
							voter_registration.FirstName,
							voter_registration.LastName,
							voter_registration.FathersName,
							voter_registration.MothersName,
							voter_registration.BirthYear,
							voter_registration.VoterID,
							voter_registration.ArithmosDimotologiou,
							voter_registration.Dhmos,
							voter_registration.DhmotikhEnothta,
							voter_registration.EklogikhPerifereia,
							voter_registration.PerifereiakiEnothta,
							voter_registration.Perifereia,
							voter_registration.Nomos,
							voter_registration.EMail,
							voter_registration.EMailVerificationPin,
							voter_registration.EMailIsVerified,
							voter_registration.MobilePhone,
							voter_registration.MobileVerificationPin,
							voter_registration.MobileIsVerified,
							voter_registration.Phone,
							voter_registration.Street,
							voter_registration.StreetNo,
							voter_registration.Area,
							voter_registration.Zip,
							voter_registration.Municipality,
							voter_registration.County,
							voter_registration.PaymentGatewayResponse)
			SELECT 
							voter_registration_temp.UniqueKey,
							voter_registration_temp.FirstName,
							voter_registration_temp.LastName,
							voter_registration_temp.FathersName,
							voter_registration_temp.MothersName,
							voter_registration_temp.BirthYear,
							voter_registration_temp.VoterID,
							voter_registration_temp.ArithmosDimotologiou,
							voter_registration_temp.Dhmos,
							voter_registration_temp.DhmotikhEnothta,
							voter_registration_temp.EklogikhPerifereia,
							voter_registration_temp.PerifereiakiEnothta,
							voter_registration_temp.Perifereia,
							voter_registration_temp.Nomos,
							voter_registration_temp.EMail,
							voter_registration_temp.EMailVerificationPin,
							voter_registration_temp.EMailIsVerified,
							voter_registration_temp.MobilePhone,
							voter_registration_temp.MobileVerificationPin,
							voter_registration_temp.MobileIsVerified,
							voter_registration_temp.Phone,
							voter_registration_temp.Street,
							voter_registration_temp.StreetNo,
							voter_registration_temp.Area,
							voter_registration_temp.Zip,
							voter_registration_temp.Municipality,
							voter_registration_temp.County,
							?									
		FROM `voter_registration_temp` WHERE voter_registration_temp.UniqueKey=?';
		$command = new MySqlCommand($connection, $sql);
		$command->Parameters->setString(1,$fPayPalParameters);
		$command->Parameters->setString(2,$fUniqueKey);
		$command->ExecuteQuery();
		
		// Delete temp record
		$sql = "DELETE FROM `voter_registration_temp` WHERE `UniqueKey`=?";
		$command = new MySqlCommand($connection, $sql);
		$command->Parameters->setString(1,$fUniqueKey);
		$command->ExecuteQuery();	
?>