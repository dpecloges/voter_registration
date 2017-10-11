<?php
	require_once("../Classes/DBConnection.php");
	
	require_once("../Classes/Mailer.php");
	$fMailer = new Mailer();


	$fVivaAPIURL = "http://demo.vivapayments.com";
    $fVivaWebHookAuthURL  = "/api/messages/config/token";
    
    // Your merchant ID and API Key can be found in the 'Security' settings on your profile.
	$fMerchantId = 'f8e9b647-9676-40e6-ba54-38030689d1ce';
	$fAPIKey = '=14=.|'; 	

	
	///////////////////////////////////////////////////////////////////////////////////////
	// Receive the Webhook Authorization Code
	///////////////////////////////////////////////////////////////////////////////////////
	$fWebHookAuthorizationCodeURL = $fVivaAPIURL . $fVivaWebHookAuthURL;
	$opts = array(
	  'http'=>array(
	    'method'=>"GET",
	    'header' => "Authorization: Basic " . base64_encode($fMerchantId.":".$fAPIKey)                 
	  )
	);
	$context = stream_context_create($opts);
	$fWebhookAuthorizationData = file_get_contents($fWebHookAuthorizationCodeURL, false, $context);
	///////////////////////////////////////////////////////////////////////////////////////
	
	$data = json_decode(file_get_contents('php://input'), true); 
	$orderID = $data['EventData']['OrderCode'];
	
	//$cardCountryCode = $data['EventData']['CardCountryCode'];
	
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
		FROM `voter_registration_temp` WHERE voter_registration_temp.VivaOrderID=?';
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setString(1,json_encode($data));
	$command->Parameters->setInteger(2,$orderID);
	$command->ExecuteQuery();

	
	// Send Verification Email
	$userEmail = "";
	$userFirstName = "";
	$userLastName = "";
	$voterID = "";
	$sql = "SELECT `EMail`,`FirstName`,`LastName`,`VoterID` FROM `voter_registration_temp` WHERE `VivaOrderID`=?";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setInteger(1,$orderID);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		$userEmail = $reader->getValue(0);
		$userFirstName = $reader->getValue(1);
		$userLastName = $reader->getValue(2);
		$voterID = $reader->getValue(3);
		
		$fMailer->SendRegistrationThankYouEmail($userEmail, $userFirstName. " ". $userLastName, $voterID);
	}
	$reader->Close();

		
		
	
	// Delete temp record
	$sql = "DELETE FROM `voter_registration_temp` WHERE `VivaOrderID`=?";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setInteger(1,$orderID);
	$command->ExecuteQuery();	
	
		
		

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ECHO VIVA WEBHOOK AUTHORIZATION!!!!!!!
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo $fWebhookAuthorizationData;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////

		
		
		
?>