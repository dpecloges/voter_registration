<?php
session_start();
require_once("Classes/DBConnection.php"); 
if(isset($_GET['Count']))
{
	$sql = "SELECT COUNT(`ID`) FROM `voter_registration`";
	$command = new MySqlCommand($connection, $sql);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		echo $reader->getValue(0);
	}
	$reader->Close();
	$connection->Close();
}
else if (isset($_GET['From']))
{
	header('Content-type: application/json');
	
	$from = intval($_GET['From']);
	$to = intval($_GET['To']);
	
	$dataArray = array();

	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'dp_Ekloges' AND TABLE_NAME = 'voter_registration'";
	$command = new MySqlCommand($connection, $sql);
	$reader = $command->ExecuteReader();
	$tmp = array();
	$i=0;
	while($reader->Read() && $i<29)
	{
		$tmp[$i] = $reader->getValue(0);
		$i++;
	}
	array_push($dataArray,$tmp);

	$sql = 'SELECT
	voter_registration.ID,
	voter_registration.DateTime,
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
	voter_registration.County
	FROM
	voter_registration
	ORDER BY `ID` LIMIT '.$from.",".$to;
	
	$command = new MySqlCommand($connection, $sql);
	$reader = $command->ExecuteReader();
	while($reader->Read())
	{
		$tmp = array();
		for($i=0; $i<29; $i++)
		{
			$tmp[$i] = $reader->getValue($i);
		}
		
		array_push($dataArray,$tmp);
	}
	$reader->Close();
	$connection->Close();
	
	echo trim(json_encode($dataArray));
}
exit;
?>