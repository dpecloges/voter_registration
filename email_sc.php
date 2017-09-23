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
$Email = strtolower(trim($_POST['Email']));

if((!filter_var($Email, FILTER_VALIDATE_EMAIL))){
	$errcode = 101;
	$errdescr = 'Το email δεν είναι έγκυρο!';
}elseif(EmailExist($Email, $con)){
	$errcode = 102;
	$errdescr = 'Αυτό το email είναι ήδη καταχωρημένο!';
}

if($errcode!=0){
	$data['Error'] = $errcode;
	$data['ErrorDescr'] = $errdescr;
	die(json_encode($data));	
}

$PIN = rand(1000 , 9999);
$_SESSION['Email_PIN'] = $PIN;
$_SESSION['Email'] = $Email;

$Subject = 'Επαλήθευση EMAIL';
$Body =  
<<<EOT
<br/>
<h1>Εκλογές για τον Πρόεδρο του Νέου Φορέα</h1>
<h3>Ανεξάρτητη Επιτροπή Διαδικασιών & Δεοντολογίας (ΑΕΔΔ)</h3>
<hr/>
<br/>
<br/>
<span style="font:Verdana, Geneva, sans-serif;font-size:14px">Παρακαλούμε χρησιμοποιήστε τον παρακάτω κωδικό για να επαληθεύσετε το email σας.
<br/><br/>
Κωδικός επαλήθευσης : </span>
<span style="font:Verdana, Geneva, sans-serif; font-size:24px">$PIN</span>
<br/><br/>

EOT;


$Fullname = $_SESSION['FName'] . ' ' . $_SESSION['LName'];


sendMail($Email, $Fullname, $Subject, $Body);
$errcode = 0;
$errdescr = '';


mysqli_close($con);
$data['Error'] = $errcode;
$data['ErrorDescr'] = $errdescr;
echo json_encode($data);


function EmailExist($Email, $con){
	$sql = "SELECT ID FROM vreg_volunteers WHERE Email = '$Email'";
	$result = mysqli_query($con, $sql);
	$num_rows = mysqli_num_rows($result);
	$r = ($num_rows > 0);
	return $r;
}

?>