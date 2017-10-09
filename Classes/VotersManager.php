<?php
class VotersManager
{
    public function __construct()
    {
    }
    
    public function GetVoterData($firstName,$lastName,$fathersName,$mothersName,$birthYear)
    {
		$FName = $firstName;//iconv("UTF-8", "CP1253", $firstName);
		$LName = $lastName;//iconv("UTF-8", "CP1253", $lastName);
		$PName = $fathersName;//iconv("UTF-8", "CP1253", $fathersName);
		$MName = $mothersName;//iconv("UTF-8", "CP1253", $mothersName);

		$url = "https://catalog.dpekloges.gr/find_catalog_data_strict.php";
		$data = array(
		'FName' => $FName, 
		'LName' => $LName,
		'PName' => $PName,
		'MName' => $MName,
		'BirthYear' => (string)$birthYear);
		
		$options = array(
		        'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    )
		);
		
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);		
		$reply = json_decode ($result);
	
		return $reply;
    }
}
?>