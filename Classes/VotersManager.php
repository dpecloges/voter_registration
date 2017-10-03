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

		$url = "http://catalog.dpekloges.gr/find_catalog_data_strict.php";
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
    
    public function GetVoterInfoForVisualPurposes($voterID, $lastName)
    {
    	$Eponymo = iconv("UTF-8", "CP1253", $lastName);
		$url = "http://www.ypes.gr/services/eea/eeagr/result.asp";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "eid_ekl_ar=" . $voterID . "&Eponymo=" . $Eponymo);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		$data = iconv("CP1253", "UTF-8", $server_output);
		$p1 = strpos($data, '<td width="30%" class="t">Ειδ.Εκλογικός Αριθμός :</td>');
		$p2 = strpos($data, '<!-- <tr  class="d">');
		return '<table><colgroup><col width="500px"/><col/></colgroup><tr>' . substr($data, $p1, $p2 - $p1) . '</table>';
    }
}
?>