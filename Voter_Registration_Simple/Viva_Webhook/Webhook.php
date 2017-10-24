<?php
	require_once("../Classes/DBConnection.php");
	
	require_once("../Classes/VivaPaymentManager.php");
	$fVivaPaymentManager = new VivaPaymentManager();
	
	require_once("../Classes/Mailer.php");
	$fMailer = new Mailer();

    $fVivaWebHookAuthURL  = "/api/messages/config/token";
    
	///////////////////////////////////////////////////////////////////////////////////////
	// Receive the Webhook Authorization Code
	///////////////////////////////////////////////////////////////////////////////////////
	/*$fWebHookAuthorizationCodeURL = $fVivaPaymentManager->fVivaURL   . $fVivaWebHookAuthURL;
	$opts = array(
	  'http'=>array(
	    'method'=>"GET",
	    'header' => "Authorization: Basic " . base64_encode($fVivaPaymentManager->fVivaMerchantID .":".$fVivaPaymentManager->fVivaAPIKey)                 
	  )
	);
	$context = stream_context_create($opts);
	$fWebhookAuthorizationData = file_get_contents($fWebHookAuthorizationCodeURL, false, $context);
	echo $fWebhookAuthorizationData;
	exit;*/
	///////////////////////////////////////////////////////////////////////////////////////
	

	
	$data = json_decode(file_get_contents('php://input'), true); 
	$orderID = $data['EventData']['OrderCode'];
	
	//$cardCountryCode = $data['EventData']['CardCountryCode'];
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// GetVoterID from VivaOrder
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$voterID = "";
	$sql = "SELECT `VoterID` FROM `voter_registration_temp` WHERE voter_registration_temp.VivaOrderID=?";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setInteger(1, $orderID);
    $reader = $command->ExecuteReader();
    while($reader->Read())
    {
    	$voterID = $reader->getValue(0);
    }
    $reader->Close();
    
	    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Check if voter ID exists in voter_registration
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $voterAlreadyPaied = false;
    $sql = "SELECT `ID` FROM `voter_registration` WHERE `VoterID`=?";
    $command = new MySqlCommand($connection, $sql);
	$command->Parameters->setInteger(1, $orderID);
 	$reader = $command->ExecuteReader();
    if($reader->Read())
    {
    	$voterAlreadyPaied = true;
    }
    $reader->Close();
    
    if($voterAlreadyPaied)
    {
    	exit;
    }

	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sql = 'INSERT INTO `voter_registration` (
							voter_registration.IsFriend,
							voter_registration.UniqueKey,
							voter_registration.FirstName,
							voter_registration.LastName,
							voter_registration.FathersName,
							voter_registration.MothersName,
							voter_registration.BirthYear,
							voter_registration.VoterID,
							voter_registration.ProfessionID,
							voter_registration.ArithmosDimotologiou,
							voter_registration.Dhmos,
							voter_registration.DhmotikhEnothta,
							voter_registration.EklogikhPerifereia,
							voter_registration.PerifereiakiEnothta,
							voter_registration.Perifereia,
							voter_registration.Nomos,
							voter_registration.EMail,
							voter_registration.MobilePhone,
							voter_registration.Phone,
							voter_registration.CountryISO,
							voter_registration.RegionCode,
							voter_registration.Municipality,
							voter_registration.Street,
							voter_registration.StreetNo,
							voter_registration.Area,
							voter_registration.Zip,
							voter_registration.VivaOrderID,
							voter_registration.PaymentType,
							voter_registration.PaymentValue,
							voter_registration.InvolveTypes,
							voter_registration.InvolveProposal,
							voter_registration.IDDocumentType,
							voter_registration.IDDocumentNumber,
							voter_registration.PaymentGatewayResponse
							)
			SELECT 
							voter_registration_temp.IsFriend,
							voter_registration_temp.UniqueKey,
							voter_registration_temp.FirstName,
							voter_registration_temp.LastName,
							voter_registration_temp.FathersName,
							voter_registration_temp.MothersName,
							voter_registration_temp.BirthYear,
							voter_registration_temp.VoterID,
							voter_registration_temp.ProfessionID,
							voter_registration_temp.ArithmosDimotologiou,
							voter_registration_temp.Dhmos,
							voter_registration_temp.DhmotikhEnothta,
							voter_registration_temp.EklogikhPerifereia,
							voter_registration_temp.PerifereiakiEnothta,
							voter_registration_temp.Perifereia,
							voter_registration_temp.Nomos,
							voter_registration_temp.EMail,
							voter_registration_temp.MobilePhone,
							voter_registration_temp.Phone,
							voter_registration_temp.CountryISO,
							voter_registration_temp.RegionCode,
							voter_registration_temp.Municipality,
							voter_registration_temp.Street,
							voter_registration_temp.StreetNo,
							voter_registration_temp.Area,
							voter_registration_temp.Zip,
							voter_registration_temp.VivaOrderID,
							voter_registration_temp.PaymentType,
							voter_registration_temp.PaymentValue,
							voter_registration_temp.InvolveTypes,
							voter_registration_temp.InvolveProposal,
							voter_registration_temp.IDDocumentType,
							voter_registration_temp.IDDocumentNumber,
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
		
		if($userEmail !=null && $userEmail!="")
		{
			$fMailer->SendRegistrationThankYouEmail($userEmail, $userFirstName. " ". $userLastName, $voterID);
		}
	}
	$reader->Close();

		
		
	// Delete temp record
	$sql = "DELETE FROM `voter_registration_temp` WHERE `VivaOrderID`=?";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setInteger(1,$orderID);
	$command->ExecuteQuery();	
	
	// Delete all other orders
	$fVivaPaymentManager->CancelPreviousOrdersForVoterID($connection,$voterID );
?>