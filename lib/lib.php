<?php
require_once("config.php");
require_once("mailer/class.phpmailer.php");
require_once('mandrill-api-php/src/Mandrill.php');


function CheckAFM($afm){
	if($afm=='000000000') return false;	
	if(!preg_match('/^(EL){0,1}[0-9]{9}$/i', $afm)) return false;
	if(strlen($afm) > 9) $afm = substr($afm, 2);
	$remainder = 0;
	$sum = 0;
	for($nn = 2, $k = 7, $sum = 0; $k >= 0; $k--, $nn += $nn) $sum += $nn * ($afm[$k]);
	$remainder = $sum % 11;
	return ($remainder == 10) ? $afm[8] == '0' : $afm[8] == $remainder;
}


function findEidEklAr($FName, $LName, $PName, $BirthYear){
	$Eponymo = iconv("UTF-8", "CP1253", $LName);
	$Onoma = iconv("UTF-8", "CP1253", $FName);
	$on_pat = iconv("UTF-8", "CP1253", $PName);
	$url = "http://www.ypes.gr/services/eea/eeagr/result.asp";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "Eponymo=" . $Eponymo . "&Onoma=" . $Onoma . 
										 "&on_pat=" . $on_pat . "&etos_gen=" . $BirthYear);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	if(strpos($server_output, 'forma_citiziens_results')===false){
		return false;
	}else{
		$w = iconv("CP1253", "UTF-8", $server_output);
		$p = strpos($w, '>Ειδ.Εκλογικός Αριθμός :</td>');
		return substr($w, $p + 81, 13);
	}
	
	return null;
}

function validateEidEklAr($eid_ekl_ar, $LName){ 
	$Eponymo = iconv("UTF-8", "CP1253", $LName);
	$url = "http://www.ypes.gr/services/eea/eeagr/result.asp";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "eid_ekl_ar=" . $eid_ekl_ar . "&Eponymo=" . $Eponymo);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	return !(strpos($server_output, 'forma_citiziens_results')===false);	
}

function getEidEklArData($eid_ekl_ar, $LName){ 
	$Eponymo = iconv("UTF-8", "CP1253", $LName);
	$url = "http://www.ypes.gr/services/eea/eeagr/result.asp";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "eid_ekl_ar=" . $eid_ekl_ar . "&Eponymo=" . $Eponymo);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	$data = iconv("CP1253", "UTF-8", $server_output);
	$p1 = strpos($data, '<td width="30%" class="t">Ειδ.Εκλογικός Αριθμός :</td>');
	$p2 = strpos($data, '<!-- <tr  class="d">');
	return '<table><colgroup><col width="500px"/><col/></colgroup><tr>' . 
		   substr($data, $p1, $p2 - $p1) . '</table>';
}

function luhn_generate($s){
	$s=$s.'0';
	$sum=0;
	$i=strlen($s);
	$odd_length = $i%2;
	while ($i-- > 0) {
		$sum+=$s[$i];
		($odd_length==($i%2)) ? ($s[$i] > 4) ? ($sum+=($s[$i]-9)) : ($sum+=$s[$i]) : false;
	}
	return (10-($sum%10))%10;
}

function luhn_validate($number){
	settype($number, 'string');
	$sumTable = array(
	array(0,1,2,3,4,5,6,7,8,9),
	array(0,2,4,6,8,1,3,5,7,9));
	$sum = 0;
	$flip = 0;
	for ($i = strlen($number) - 1; $i >= 0; $i--){
		$sum += $sumTable[$flip++ & 0x1][$number[$i]];
	}
	return $sum % 10 === 0;
}


function sendMail($email, $fullname, $subject, $body){	
	try {
		$mandrill = new Mandrill(My_Mandrill_Key);
		$message = array(
			'html' => $body,
			'text' => 'Δεν υποστηρίζεται!',
			'subject' => $subject,
			'from_email' => My_Platform_Email,
			'from_name' => My_Platform_Email_Name,
			'to' => array(
				array(
					'email' => $email,
					'name' => $fullname,
					'type' => 'to'
				)
			),
			'headers' => array('Reply-To' => My_Admin_Email),
			'important' => false,
			'track_opens' => null,
			'track_clicks' => null,
			'auto_text' => null,
			'auto_html' => null,
			'inline_css' => null,
			'url_strip_qs' => null,
			'preserve_recipients' => null,
			'view_content_link' => null,
			'bcc_address' => null,
			'tracking_domain' => null,
			'signing_domain' => null,
			'return_path_domain' => null,

	    );
		$async = false;
		$ip_pool = 'Main Pool';
		$result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
	} catch(Mandrill_Error $e) {
		throw $e;
	}
}


function sendMailLocal($email, $fullname, $subject, $body){
	$mail             = new PHPMailer();
	$mail->CharSet = "UTF-8";
	$mail->From       = ToPotami_Platform_Email;
	$mail->FromName   = "Το Ποτάμι";
	$mail->Subject    = $subject;
	$mail->AltBody    = "Δεν υποστηρίζεται!";
	$mail->MsgHTML($body);
	$mail->AddReplyTo(ToPotami_Admin_Email, "Το Ποτάμι");
	$mail->AddAddress($email, $fullname);
	$mail->IsHTML(true);
	if(!$mail->Send()){
		return $mail->ErrorInfo;
	}else{
		return 0;		
	}	
}


function sendSMS($mobile, $text){
	$from = urlencode(My_Org_SMS_Name);
	$phone = str_replace('+', '', $mobile);
	$txt = urlencode($text);
	$fiveKBs = 5 * 1024;
	$fp = fopen("php://temp/maxmemory:$fiveKBs", 'r+');
	$str = "https://services.yuboto.com/web2sms/api/v2/smsc.aspx?api_key=" . 
		   My_Yuboto_Key . "&action=send&from=$from&to=$phone&text=$txt";
	$ch = curl_init($str);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	rewind($fp);
	$data = stream_get_contents($fp);
	fclose($fp);
	$obj = json_decode($data);
	if($obj->{'ok'}){		
		$id = $obj->{'ok'}->{$phone};
		return $id;
	}else{
		return false;
	}
}

function sendVoiceMsg($fixphone, $text){
	$phone = str_replace('+', '', $fixphone);
	$txt = urlencode($text);
	$fiveKBs = 5 * 1024;
	$fp = fopen("php://temp/maxmemory:$fiveKBs", 'r+');	
	$str = "https://services.yuboto.com/autodialer/api/autodialer.aspx?user=" .
		   My_Yuboto_Username . "&pass=" . My_Yuboto_Password . 
		   "&action=send&to=$phone&msg=$txt&call_type=8";
	$ch = curl_init($str);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);	
	rewind($fp);
	$data = stream_get_contents($fp);
	fclose($fp);
	if(substr($data, 0, 5)=='sent:'){
		$id = substr($data, 5);
		return $id;
	}else{
		return false;
	}
}

function getSMSstatus($YubotoID){
	$txt = urlencode($text);
	$fiveKBs = 5 * 1024;
	$fp = fopen("php://temp/maxmemory:$fiveKBs", 'r+');
	$str = "https://services.yuboto.com/web2sms/api/v2/smsc.aspx?api_key=" . 
		   My_Yuboto_Key . "&action=dlr&id=$YubotoID";
	$ch = curl_init($str);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	rewind($fp);
	$data = stream_get_contents($fp);
	fclose($fp);
	$obj = json_decode($data);
	if($obj->{'ok'}){
		$Status = $obj->{'ok'}->{'Status'};
		return $Status;
	}else{
		return '';
	}
}

function getVoiceMsgstatus($YubotoID, $phone){
	$txt = urlencode($text);
	$fiveKBs = 5 * 1024;
	$fp = fopen("php://temp/maxmemory:$fiveKBs", 'r+');	
	$str = "https://services.yuboto.com/autodialer/api/autodialer.asp?user=" .
		   My_Yuboto_Username . "&pass=" . My_Yuboto_Password . 
		   "&action=dlr&phonenumber=$phone&id=$YubotoID";
	$ch = curl_init($str);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);	
	rewind($fp);
	$data = stream_get_contents($fp);
	fclose($fp);
	return $data;
}

function ValidateFixedPhone($aFixedPhone, $aCCFixedPhone){
	if($aCCFixedPhone==1){
		$s = strlen($aFixedPhone);
		$b = substr($aFixedPhone, 0, 1);
		return (($s==10) && ($b=='2')); 
	}else{
		return ValidateGPhone($aFixedPhone);
	}
}

function ValidateMobile($aMobile, $aCCMobile){
	if($aCCMobile==1){
		$s = strlen($aMobile);
		$b = substr($aMobile, 0, 2);
		return (($s==10) && ($b=='69')); 
	}else{
		return ValidateGPhone($aMobile);
	}
}

function ValidateGPhone($aPhone){
	$s = strlen($aPhone);
	return ($s>4);
}



function UppercaseGreek($str){
	$lower = array ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'α', 'ά', 'Ά', 'β', 'γ', 'δ', 'ε', 'έ', 'Έ', 'ζ', 'η', 'ή', 'Ή', 'θ', 'ι', 'ί', 'ϊ', 'ΐ', 'Ί', 'Ϊ', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'ό', 'Ό', 'π', 'ρ', 'σ', 'ς', 'τ', 'υ', 'ύ', 'ϋ', 'ΰ', 'Ύ', 'Ϋ', 'φ', 'χ', 'ψ', 'ω', 'ώ', 'Ώ');
	$upper = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Α', 'Α', 'Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ε', 'Ε', 'Ζ', 'Η', 'Η', 'Η', 'Θ', 'Ι', 'Ι', 'Ι', 'Ι', 'Ι', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Ο', 'Ο', 'Π', 'Ρ', 'Σ', 'Σ', 'Τ', 'Υ', 'Υ', 'Υ', 'Υ', 'Υ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω', 'Ω', 'Ω');
	$str2  = str_replace($lower, $upper, $str);
	return $str2;
}


function openDB(){
	$conn = mysqli_connect(My_Database_Host, My_Database_User, My_Database_Password, My_Database_Name);
	if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	mysqli_query($conn, "SET CHARACTER SET 'utf8'");		
	return $conn;
}



function getip(){
	if(validip($_SERVER["HTTP_CLIENT_IP"])){
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	foreach(explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip){
		if(validip(trim($ip))){
			return $ip;
		}
	}
	if(validip($_SERVER["HTTP_X_FORWARDED"])){
		return $_SERVER["HTTP_X_FORWARDED"];
	}elseif(validip($_SERVER["HTTP_FORWARDED_FOR"])){
		return $_SERVER["HTTP_FORWARDED_FOR"];
	}elseif(validip($_SERVER["HTTP_FORWARDED"])){
		return $_SERVER["HTTP_FORWARDED"];;
	}else{
		return $_SERVER["REMOTE_ADDR"];
	}
}

function validip($ip){
	if(!empty($ip) && ip2long($ip)!=-1){
		$reserved_ips = array(
			array('0.0.0.0','2.255.255.255'),
			array('10.0.0.0','10.255.255.255'),
			array('127.0.0.0','127.255.255.255'),
			array('169.254.0.0','169.254.255.255'),
			array('172.16.0.0','172.31.255.255'),
			array('192.0.2.0','192.0.2.255'),
			array('192.168.0.0','192.168.255.255'),
			array('255.255.255.0','255.255.255.255')
		);
		foreach($reserved_ips as $r){
			$min = ip2long($r[0]);
			$max = ip2long($r[1]);
			if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}else{
		return false;
	}
}


?>