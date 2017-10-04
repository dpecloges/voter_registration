<?php
session_start();
require_once("Classes/DBConnection.php"); 
header('Content-type: application/json');

$fZip = $_POST['Zip'];
$fReply = array();

$sql = "SELECT `region1`,`locality` FROM `GeoPC_GR_Places` WHERE `postcode`=? AND `language`='EL' LIMIT 0,1";
$command = new MySqlCommand($connection, $sql);
$command->Parameters->setString(1,$fZip);
$reader = $command->ExecuteReader();
while($reader->Read())
{
	$fReply["Nomos"] = strtoupper ($reader->getValue(0));
	$fReply["Dhmos"] = strtoupper ($reader->getValue(1));
}
$reader->Close();
$connection->Close();

echo json_encode($fReply);
exit;
?>