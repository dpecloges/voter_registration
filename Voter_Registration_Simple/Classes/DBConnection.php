<?php
	$fDebugEnvironment = false;
	
	if($fDebugEnvironment)
	{
		$fRecaptchaSiteKey = '6Ld5JTUUAAAAACWj_JtupAAzU6nOhRI2Ge-60mdJ';
		$fRecaptchaSecretKey = '6Ld5JTUUAAAAAPuNjDSlaX2EKNnUTspDU3PiwiHn';
	}
	else
	{
		$fRecaptchaSiteKey = '6Ld37TIUAAAAAEbyCPZTv2OgcYwBgVo4fJXKzWgP';
		$fRecaptchaSecretKey = '6Ld37TIUAAAAAJzds1aK1Ac6BjZOEuAX-pBfUfE5';
	}
	
	
	
	if(file_exists("Classes/DB/MySQLConnection.php"))
	{
		if($fDebugEnvironment)
		{
			include("lib/config/config_debug.php");
		}
		else
		{
			include("lib/config/config.php");
		}

		require_once("Classes/DB/MySQLConnection.php"); 
		require_once("Classes/DB/MySQLCommand.php");
		require_once("Classes/DB/MySQLCommandParameter.php");
		require_once("Classes/DB/MySQLCommandParameters.php");
		require_once("Classes/DB/MySQLDataReader.php");
	}
	else if(file_exists("../Classes/DB/MySQLConnection.php"))
	{
		if($fDebugEnvironment)
		{
			include("../lib/config/config_debug.php");
		}
		else
		{
			include("../lib/config/config.php");
		}
		require_once("../Classes/DB/MySQLConnection.php"); 
		require_once("../Classes/DB/MySQLCommand.php");
		require_once("../Classes/DB/MySQLCommandParameter.php");
		require_once("../Classes/DB/MySQLCommandParameters.php");
		require_once("../Classes/DB/MySQLDataReader.php");
	}
	
	try{$connection = new MySQLConnection(My_Database_Host, My_Database_Name, My_Database_User, My_Database_Password, 'utf8');}
	catch (Exception $ex){echo $ex->getMessage();exit;}
?>