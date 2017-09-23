<?php

header('Content-type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require("lib/lib.php");


session_start();
$referer = "http://dpekloges.gr/apps/vreg/p2.php?SID=" . $_SESSION['RegistrationSID'];

if($_SERVER['HTTP_REFERER']!=$referer){
	$data['Error'] = 100;
	$data['ErrorDescr'] = '<h2>System Error!</h2>';
	//die(json_encode($data));
}
$errcode = 0;
$con = openDB();

$Mobile = trim($_POST['Mobile']);

if($Mobile==''){
	$errcode = 101;
	$errdescr = 'Δεν έχετε εισάγει αριθμό κινητού τηλεφώνου!';
}elseif($Mobile!='' && !(ValidateMobile($Mobile, 1))){
	$errcode = 102;
	$errdescr = 'Το κινητό τηλέφωνο δεν είναι έγκυρο!';
}elseif(MobileExist($Mobile, $con)){
	$errcode = 104;
	$errdescr = 'Αυτό το κινητό τηλέφωνο είναι ήδη καταχωρημένο!';
}else{
	$errcode = 0;
	$errdescr = '';
}

if($errcode!=0){
	$data['Error'] = $errcode;
	$data['ErrorDescr'] = $errdescr;
	die(json_encode($data));	
}

$PIN = rand(1000, 9999);
$_SESSION['Mobile_PIN'] = $PIN;
$_SESSION['Mobile'] = $Mobile;
$data = $_POST['CodeType'] == 1 ?
			sendSMS_PIN($PIN, $Mobile, $con):
			sendVoicePIN($PIN, $Mobile, $con);

mysqli_close($con);
echo json_encode($data);


function MobileExist($Mobile, $con){
	$sql = "SELECT ID FROM vreg_volunteers WHERE Mobile = '$Mobile'";
	$result = mysqli_query($con, $sql);
	$num_rows = mysqli_num_rows($result);
	$r = ($num_rows > 0);
	return $r;
}

function sendSMS_PIN($PIN, $Phone, $con){
	$Phone = "30$Phone";
	$t = date('H');
	$sql = "SELECT Count(ID) AS NoMsg FROM vreg_yuboto_list WHERE Phone = '$Phone'";
	$result = mysqli_query($con,$sql);	
	$row = mysqli_fetch_array($result);
	$errcode = 0;
	if($row['NoMsg']>9){
		$errcode = 504;
		$errdescr ='Επικοινωνήστε μαζί μας. (code 504)';
	}elseif($row['NoMsg']>0){
		$sql = "SELECT MAX(RegDateTime) AS LastMsg FROM vreg_yuboto_list WHERE Phone = '$Phone'";
		$result = mysqli_query($con, $sql);	
		$row = mysqli_fetch_array($result);
		$time_past = strtotime($row['LastMsg']) + 15;
		if($time_past < time()){
			$errcode = 0;
		}else{
			$errcode = 509;
			$errdescr = 'Παρακαλούμε περιμένετε 20 δευτερόλεπτα και ξαναπροσπαθήστε!';
		}
	}
	if($errcode!=0){
		$data['Error'] = $errcode;
		$data['ErrorDescr'] = $errdescr;
		return $data;
	}
	$txt = "Ο ΚΩΔΙΚΟΣ ΕΠΑΛΗΘΕΥΣΗΣ ΕΙΝΑΙ $PIN";
	$YubotoID = sendSMS($Phone, $txt);
	if($YubotoID){
		$sql = "INSERT INTO vreg_yuboto_list(Phone, YubotoID, IDType, RegDateTime) VALUES ('$Phone', '$YubotoID', 1, NOW())";
		mysqli_query($con, $sql);
		$errcode = 0;
		$errdescr ='';
	}else{
		$errcode = 502;
		$errdescr = 'Τεχνικό πρόβλημα. Επικοινωνήστε μαζί μας. (code 502)';
	}
	$data['Error'] = $errcode;
	$data['ErrorDescr'] = $errdescr;
	return $data;
}


function sendVoicePIN($PIN, $Phone, $con){
	$Phone = "30$Phone";
	$t = date('H');
	$sql = "SELECT Count(ID) AS NoMsg FROM vreg_yuboto_list WHERE Phone = '$Phone'";
	$result = mysqli_query($con, $sql);	
	$row = mysqli_fetch_array($result);
	$errcode = 0;

	if($row['NoMsg']>9){
		$errcode = 504;
		$errdescr ='Επικοινωνήστε μαζί μας. (code 504)';
	}elseif($t>20 || $t<9){
		$errcode = 510;
		$errdescr ='Αποστολή κωδικών με ηχητικό μήνυμα 9πμ – 3μμ & 5μμ – 9μμ';
	}elseif($row['NoMsg']>0){
		$sql = "SELECT MAX(RegDateTime) AS LastMsg FROM vreg_yuboto_list WHERE Phone = '$Phone'";
		$result = mysqli_query($con, $sql);	
		$row = mysqli_fetch_array($result);
		$time_past = strtotime($row['LastMsg']) + 30;
		if($time_past < time()){
			$errcode = 0;
		}else{
			$errcode = 509;
			$errdescr = 'Παρακαλούμε περιμένετε 30 δευτερόλεπτα και ξαναπροσπαθήστε!';
		}
	}
	
	if($errcode!=0){
		$data['Error'] = $errcode;
		$data['ErrorDescr'] = $errdescr;
		return $data;
	}
	
	$sPIN = substr($PIN,0,1) . '. ' . substr($PIN, 1, 1) . '. ' . 
			substr($PIN, 2, 1) . '. ' . substr($PIN, 3) . '.' ;
	$txt = "Ο κωδικός επαλήθευσης είναι, $sPIN . . Επανάληψη . . Ο κωδικός επαλήθευσης είναι, $sPIN .";
	$YubotoID = sendVoiceMsg($Phone, $txt);
	if($YubotoID){
		$sql = "INSERT INTO vreg_yuboto_list(Phone, YubotoID, IDType, RegDateTime) VALUES ('$Phone', '$YubotoID', 2, NOW())";			
		mysqli_query($con, $sql);
		$errcode = 0;
		$errdescr ='';
	}else{
		$errcode = 503;
		$errdescr ='Τεχνικό πρόβλημα. Επικοινωνήστε μαζί μας. (code 503)';
	}
	$data['Error'] = $errcode;
	$data['ErrorDescr'] = $errdescr;	
	return $data;
}	
	
?>