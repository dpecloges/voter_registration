<?php
	include("Config.php");
	if(file_exists("Classes/DB/MySQLConnection.php"))
	{
		require_once("Classes/DB/MySQLConnection.php"); 
		require_once("Classes/DB/MySQLCommand.php");
		require_once("Classes/DB/MySQLCommandParameter.php");
		require_once("Classes/DB/MySQLCommandParameters.php");
		require_once("Classes/DB/MySQLDataReader.php");
	}
	else if(file_exists("../Classes/DB/MySQLConnection.php"))
	{
		require_once("../Classes/DB/MySQLConnection.php"); 
		require_once("../Classes/DB/MySQLCommand.php");
		require_once("../Classes/DB/MySQLCommandParameter.php");
		require_once("../Classes/DB/MySQLCommandParameters.php");
		require_once("../Classes/DB/MySQLDataReader.php");
	}
	
	try{$connection = new MySQLConnection(Database_IP, Database_Name, Database_User, Database_Password, 'utf8');}
	catch (Exception $ex){echo $ex->getMessage();exit;}
?>