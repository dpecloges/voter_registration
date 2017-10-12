<?php
session_start();
require_once("Classes/DBConnection.php"); 

$fMunicipality = intval($_POST['Municipality']);
$fRegion = "";

$sql = "SELECT `NOMOS` FROM `YPES_DHMOI` WHERE `KOD_DHM`=".$fMunicipality." LIMIT 0,1";
$command = new MySqlCommand($connection, $sql);
$reader = $command->ExecuteReader();
while($reader->Read())
{
	echo strtoupper($reader->getValue(0));
}
$reader->Close();
$connection->Close();
exit;
?>