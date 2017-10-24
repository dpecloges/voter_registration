<?php 
require_once("Classes/DBConnection.php");
require_once("Classes/VivaPaymentManager.php");
$fVivaPaymentManager = new VivaPaymentManager();

$fInvoiceId = $_POST['invoiceID'];

$fOfflinePaymentsEndsAt = strtotime('10-Nov-2017 20:59:59');
$fDateTimeNow = time();  
$fPaymentTimeOut =  $fOfflinePaymentsEndsAt - $fDateTimeNow;

$fPayWithCash = $_POST['RadioPayment']==2;

$fPaymentType = intval($_POST['RadioPayment']);


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check if user is friend and exists
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
$fVoterIsFriend = false;
$fVoterExists = false;
$sql = "SELECT `IsFriend`,`VoterID` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fInvoiceId);
$reader = $command->ExecuteReader();
if($reader->Read())
{
	$fVoterExists = true;
	$fVoterIsFriend = ($reader->getValue(0)==1);
	$fVoterID = $reader->getValue(1);
}
$reader->close();


// Check if user has already paid
$fUserAlreadyPaied = false;
$sql = "SELECT `ID` FROM `voter_registration` WHERE `UniqueKey`=? LIMIT 0,1";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1, $fInvoiceId);
$reader = $command->ExecuteReader();
if($reader->Read())
{
	$fUserAlreadyPaied = true;
}
$reader->Close();

if(!$fVoterExists || $fUserAlreadyPaied)
{	
	$connection->Close();
	header('refresh: 0; url=index.php');
	exit;
}

if(!$fVoterIsFriend)
{
	// Check if mobile is verified
	$voterInfoIsVerified = false;
	$sql = "SELECT `MobileIsVerified` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setString(1,$fInvoiceId);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		$voterInfoIsVerified = ($reader->getValue(0)==1);
	}
	$reader->Close();
	
	
	if(!$voterInfoIsVerified)
	{	
		Print 'Voter Info not verified';
		exit;

		$connection->Close();
		header('refresh: 0; url=index.php');
		exit;
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////


// The POST URL and parameters
if($fDebugEnvironment)
{
	$request =  'http://demo.vivapayments.com/api/orders';		// demo environment URL
}
else
{
	$request =  'https://www.vivapayments.com/api/orders';	// production environment URL
}



//Set the Payment Amount
$Amount = 300;	// Amount in cents

if($_POST['RadioButtonPaymentValue']==1)
{
	$Amount = 300;
}
else
{
	$Amount = floatval(Str_replace(",",".",$_POST['TextBoxValue']))*100;
}

if($Amount<300)
{
	$Amount = 300;
}

//Set some optional parameters (Full list available here: https://github.com/VivaPayments/API/wiki/Optional-Parameters)
$AllowRecurring = 'false'; // This flag will prompt the customer to accept recurring payments in tbe future.
$RequestLang = 'el-GR'; //This will display the payment page in English (default language is Greek)
$Source = 'Default'; // This will assign the transaction to the Source with Code = "Default". If left empty, the default source will be used.
$DisableCash = 'false';
$DisablePayAtHome = 'false';
$PaymentTimeOut  = $fPaymentTimeOut;
$CustomerTrns = 'Κωδικός Προεγγραφής: '.$fVoterID;

if(!$fPayWithCash)
{
	$DisableCash = 'true';
	$DisablePayAtHome = 'true';

}

$postargs = 'Amount='.urlencode($Amount).'&AllowRecurring='.$AllowRecurring.'&RequestLang='.$RequestLang.'&SourceCode='.$Source.'&DisableCash='.$DisableCash.'&DisablePayAtHome=true&PaymentTimeOut='.$fPaymentTimeOut."&CustomerTrns=".$CustomerTrns;

// Get the curl session object
$session = curl_init($request);
// Set the POST options.
curl_setopt($session, CURLOPT_POST, true);
curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
curl_setopt($session, CURLOPT_HEADER, true);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
curl_setopt($session, CURLOPT_USERPWD, $fVivaPaymentManager->fVivaMerchantID.':'.$fVivaPaymentManager->fVivaAPIKey);
curl_setopt($session, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
// Do the POST and then close the session
$response = curl_exec($session);
// Separate Header from Body
$header_len = curl_getinfo($session, CURLINFO_HEADER_SIZE);
$resHeader = substr($response, 0, $header_len);
$resBody =  substr($response, $header_len);
curl_close($session);

// Parse the JSON response
try 
{
	if(is_object(json_decode($resBody)))
	{
	  	$resultObj=json_decode($resBody);
	}
	else
	{
		preg_match('#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $resHeader, $match);
				throw new Exception("API Call failed! The error was: ".trim($match[1]));
				exit;
	}
} 
catch( Exception $e ) 
{
	echo $e->getMessage();
}
if ($resultObj->ErrorCode==0)
{	//success when ErrorCode = 0
	$orderId = $resultObj->OrderCode;
	
	$sql = "UPDATE `voter_registration_temp` SET `VivaOrderID`=?,`PaymentType`=?,`PaymentValue`=? WHERE `UniqueKey`=?";
	$command = new MySqlCommand($connection,$sql);
	$command->Parameters->setInteger(1,$orderId);
	$command->Parameters->setInteger(2,$fPaymentType);
	$command->Parameters->setDouble(3,$Amount);
	$command->Parameters->setString(4,$fInvoiceId);
	
	$command->ExecuteQuery();
	
	
	$connection->Close();
	if(!$fPayWithCash)
	{
		if($fDebugEnvironment)
		{
			header('refresh: 0; url=https://demo.vivapayments.com/web/newtransaction.aspx?ref='.$orderId);
		}
		else
		{
			header('refresh: 0; url=https://www.vivapayments.com/web/newtransaction.aspx?ref='.$orderId);
		}
	}
	else
	{
		header('refresh: 0; url=Step3_PayWithCash.php?UniqueKey='.$fInvoiceId );
	}
	exit;
}
else
{
	echo 'The following error occured: ' . $resultObj->ErrorText;
}
$connection->Close();


?>
