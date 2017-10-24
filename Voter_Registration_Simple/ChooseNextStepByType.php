<?php
	require_once("Classes/DBConnection.php"); 
	
	$fUniqueKey = $_GET['UniqueKey'];
	//$fIsFriend = intval($_GET['IsFriend']==1) ? 1 : 0;
	
	$isFriend  = - 1;
	$sql = "SELECT `IsFriend` FROM  `voter_registration_temp` WHERE `UniqueKey`=?";
	$command = new MySqlCommand($connection,$sql);
	$command->Parameters->setString(1,$fUniqueKey);
	$reader = $command->ExecuteReader();
	while($reader->Read())
	{
		$isFriend = $reader->getValue(0);
	}
	$reader->Close();
	
	

	switch($isFriend )
	{
		case 1:
			header('refresh: 0; url=Step3.php?UniqueKey='.$fUniqueKey);
			exit;
		break;
		
		
		case 0:
			header('refresh: 0; url=Step2.php?UniqueKey='.$fUniqueKey);
			exit;
		break;
		
		default:
			header('refresh: 0; url=index.php');
			exit;
		break;
	}

	
	
	
?>