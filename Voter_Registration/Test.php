<?php
	require_once("Classes/DBConnection.php");

	
	require_once("Classes/VivaPaymentManager.php");
	$fVivaPaymentManager = new VivaPaymentManager();
	
	
	$fVivaPaymentManager->CancelPreviousOrdersForVoterID($connection,137986000897);
	
	

?>