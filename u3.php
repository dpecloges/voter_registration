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

session_start();
$con = openDB();
$UID = $_SESSION['RegistrationUID'];
$errcode = 0;
if(empty($UID)){	
	$data['Error'] = 500;
	$data['ErrorDescr'] = 'Session timeout!';
	die(json_encode($data));
}

if(!$_SESSION['Email_PIN_Validated'] || !$_SESSION['Mobile_PIN_Validated']){
	$data['Error'] = 101;
	$data['ErrorDescr'] = 'Invalid registration!';
	die(json_encode($data));
}

$FName = mysqli_real_escape_string($con, $_SESSION['FName']);
$LName = mysqli_real_escape_string($con, $_SESSION['LName']);
$PName = mysqli_real_escape_string($con, $_SESSION['PName']);
$MName = mysqli_real_escape_string($con, $_SESSION['MName']);
$BirthYear = $_SESSION['BirthYear'];
$EidEklAr  = $_SESSION['EidEklAr'];
$Mobile = $_SESSION['Mobile'];
$FixedPhone = $_SESSION['FixedPhone'];
$Email = mysqli_real_escape_string($con, $_SESSION['Email']);
$NoNumbersAddress = $_SESSION['NoNumbersAddress'] ? 1: 0;
$StreetName = mysqli_real_escape_string($con, $_SESSION['StreetName']);
$StreetNumber = mysqli_real_escape_string($con, $_SESSION['StreetNumber']);
$Zip = $_SESSION['ZipCode'];
$Municipality = mysqli_real_escape_string($con, $_SESSION['Municipality']);
$Division = mysqli_real_escape_string($con, $_SESSION['Division']);
$slider1 = $_POST['slider1'];
$slider2 = $_POST['slider2'];
$slider3 = $_POST['slider3'];
$slider4 = $_POST['slider4'];
$slider5 = $_POST['slider5'];
$slider6 = $_POST['slider6'];
$Help1 = $_POST['Help1'];
$Help1a = $_POST['Help1a'];
$Help1b = $_POST['Help1b'];
$Help1c = $_POST['Help1c'];
$Help1d = $_POST['Help1d'];
$Help2 = $_POST['Help2'];
$Help3 = $_POST['Help3'];
$Help4 = $_POST['Help4'];
$Job = mysqli_real_escape_string($con, $_POST['Job']);
$MiscSkills = mysqli_real_escape_string($con, $_POST['MiscSkills']);


$sql = "INSERT INTO vreg_volunteers (UID, RegDateTime) VALUES ('$UID', NOW())";
mysqli_query($con, $sql);
$sql = "SELECT ID FROM vreg_volunteers WHERE UID = '$UID'";
$result = mysqli_query($con, $sql);	
$row = mysqli_fetch_array($result);
$ID = $row['ID'];
$ID_Number = $ID + 10000;
$ID_Number = $ID_Number . luhn_generate($ID_Number);

$sql = "UPDATE vreg_volunteers SET 
				ID_Number = $ID_Number,
				FName = '$FName',
				LName = '$LName',
				PName = '$PName',
				MName = '$MName',
				Email = '$Email',
				Mobile = '$Mobile',
				FixedPhone = '$FixedPhone',
				EidEklAr = '$EidEklAr',
				StreetName = '$StreetName',
				StreetNumber = '$StreetNumber',
				Zip = '$Zip',
				Municipality = '$Municipality',
				Division = '$Division',
				BirthYear = $BirthYear,
				NoNumbersAddress = $NoNumbersAddress,
				Q1 = $slider1,
				Q2 = $slider2,
				Q3 = $slider3,
				Q4 = $slider4,
				Q5 = $slider5,
				Q6 = $slider6,
				H1 = $Help1,
				H1a = $Help1a,
				H1b = $Help1b,
				H1c = $Help1c,
				H1d = $Help1d,
				H2 = $Help2,
				H3 = $Help3,
				H4 = $Help4,
				Job = '$Job',
				MiscSkills = '$MiscSkills'
		WHERE UID = '$UID'";



if(!mysqli_query($con, $sql)){
	$errdescr = mysqli_error($con);
	$data['Error'] = 102;
	$data['ErrorDescr'] = 'DB: ' . $errdescr;
	$data  = mysqli_real_escape_string($con, $sql);
	$sql = "INSERT INTO vreg_err_log (Data, RegDateTime) VALUES ('$data', '$errdescr', NOW())";
	mysqli_query($con, $sql);
	mysqli_close($con);
	die(json_encode($data));
}


$h1 = $Help1==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';
$h1a = $Help1a==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';
$h1b = $Help1b==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';
$h1c = $Help1c==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';
$h1d = $Help1d==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';
$h2 = $Help2==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';
$h3 = $Help3==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';
$h4 = $Help4==1 ? '<b>ΝΑΙ</b>': '<b>ΟΧΙ</b>';



$Subject = 'Εγγραφή εθελοντή';

$Body =  
<<<EOT
<br/>
<h1>Εκλογές για τον Πρόεδρο του Νέου Φορέα</h1>
<h3>Ανεξάρτητη Επιτροπή Διαδικασιών & Δεοντολογίας (ΑΕΔΔ)</h3>
<hr/>
<br/>
<br/>
<span style="font:Verdana, Geneva, sans-serif;font-size:14px">


Σε ευχαριστούμε για την εγγραφή σου στους καταλόγους των εθελοντών.<br/><br/>
						
Ο προσωπικός σου κωδικός εθελοντή είναι <h3>$ID_Number</h3><br/>
<br/>

Τα στοιχεία που μας έχεις δώσει είναι:
<br/><br/><br/>
<b>Ειδικός εκλογικός αριθμός : </b>$EidEklAr<br/>
<b>Όνομα : </b>$FName<br/>
<b>Επώνυμο : </b>$LName<br/>
<b>Όνομα πατρός : </b>$PName<br/>
<b>Όνομα μητρός : </b>$MName<br/>
<b>Email : </b>$Email<br/>
<b>Κινητό τηλέφωνο : </b>$Mobile<br/>
<b>Σταθερό τηλέφωνο : </b>$FixedPhone<br/>
<b>Διεύθυνση : </b>$StreetName $StreetNumber, $Municipality<br/>
<b>Επάγγελμα : </b>$Job<br/><br/>
<b>Άλλα ενδιαφέροντα : </b><br/>$MiscSkills<br/>
<h3>Οι απαντήσεις σου</h3>
Είστε εξοικειωμένη/ος με τους Η/Υ; <b>$slider1/10</b><br/><br/>
Είστε εξοικειωμένη/ος με τις οθόνες αφ$slider1ής; <b>$slider2/10</b><br/><br/>
Είστε εξοικειωμένη/ος με το πληκτρολόγιο; <b>$slider3/10</b><br/><br/>
Χειρίζεστε με άνεση απλές εφαρμογές στο κινητό; <b>$slider4/10</b><br/><br/>
Είστε οργανωτικός τύπος; <b>$slider5/10</b><br/><br/>
Δημιουργείτε εύκολα καλή σχέση με τους ανθρώπους; <b>$slider6/10</b><br/><br/>
<br/>
<b><u>Θέλω να συμβάλλω στο νέο φορέα της κεντροαριστεράς:</u></b><br/><br/>

Θέλω να συμβάλω στην οργάνωση και διεξαγωγή των εκλογών του νέου φορέα της κεντροαριστεράς: $h1<br/><br/>
Την Κυριακή 5 Νοεμβρίου ποια βάρδια προτιμάτε;<br/>
7πμ-2μμ: $h1a<br/>
2μμ-9μμ: $h1b<br/>
Την Κυριακή 12 Νοεμβρίου ποια βάρδια προτιμάτε;<br/>
7πμ-2μμ: $h1c<br/>
2μμ-9μμ: $h1d<br/><br/><br/>
Στην επεξεργασία τεκμηριωμένων προτάσεων πολιτικής σε εθνικό επίπεδο: $h2<br/><br/>
Στην επεξεργασία τεκμηριωμένων προτάσεων πολιτικής για την τοπική αυτοδιοίκηση: $h3<br/><br/>
Την συμμετοχή σε δράσεις γύρω από θέματα που αφορούν την πόλη μου ή/και την εργασία μου: $h4<br/><br/>

</span>
<br/><br/>

EOT;



$Fullname = $FName . ' ' . $LName;


sendMail($Email, $Fullname, $Subject, $Body);











mysqli_query($con, $sql);
mysqli_close($con);

$data['ID_Number'] = $ID_Number;
$data['Error'] = $errcode;
$data['ErrorDescr'] = $errdescr;
echo json_encode($data);


function EidEklArExist($EidEklArithm, $con){
	$sql = "SELECT ID FROM vreg_volunteers WHERE EidEklAr = '$EidEklArithm'";
	$result = mysqli_query($con, $sql);
	$num_rows = mysqli_num_rows($result);
	$r = ($num_rows > 0);
	return $r;
}


?>