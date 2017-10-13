<?php
	require_once("Classes/DBConnection.php"); 
	
	$fUniqueKey = $_GET['UniqueKey'];
	$fIsFriend = intval($_GET['IsFriend']==1) ? 1 : 0;
	
	$sql = "UPDATE `voter_registration_temp` SET `IsFriend`=".$fIsFriend ." WHERE `UniqueKey`=?";
	$command = new MySqlCommand($connection,$sql);
	$command->Parameters->setString(1,$fUniqueKey);
	$command->ExecuteQuery();
	$connection->Close();
	
	switch($fIsFriend)
	{
		case 1:
			header('refresh: 0; url=Step3.php?UniqueKey='.$fUniqueKey);
			exit;
		break;
		
		default:
			header('refresh: 0; url=Step2.php?UniqueKey='.$fUniqueKey);
			exit;
		break;
	}
?>