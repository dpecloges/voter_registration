<?php

header('Content-type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require("lib/lib.php");


if($_SERVER['HTTP_REFERER']!='http://dpekloges.gr/apps/vreg/p1.php'){
	$data['Error'] = 100;
	$data['ErrorDescr'] = '<h2>System Error!</h2>';
	die(json_encode($data));
}
$errcode = 0;



$FName = trim($_POST['FName']);
$LName = trim($_POST['LName']);
$PName = trim($_POST['PName']);
$BirthYear = $_POST['BirthYear'];



if(mb_strlen($FName, 'utf-8') < 3){
	$errcode = 101;
	$errdescr = 'Δεν έχετε συμπληρώσει το όνομα σας!';
}elseif(mb_strlen($LName, 'utf-8') < 3){
	$errcode = 102;
	$errdescr = 'Δεν έχετε συμπληρώσει το επώνυμο σας!';
}elseif(mb_strlen($PName, 'utf-8') < 3){
	$errcode = 103;
	$errdescr = 'Δεν έχετε συμπληρώσει το πατρώνυμο σας!';
}elseif(findInvalidChars($FName)){
	$errcode = 104;
	$errdescr = 'Το όνομα σας περιέχει λατινικούς ή ειδικούς χαρακτήρες!';
}elseif(findInvalidChars($LName)){
	$errcode = 105;
	$errdescr = 'Το επώνυμο σας περιέχει λατινικούς ή ειδικούς χαρακτήρες!';
}elseif(findInvalidChars($PName)){
	$errcode = 106;
	$errdescr = 'Το πατρώνυμο σας περιέχει λατινικούς ή ειδικούς χαρακτήρες!';
}elseif(empty($BirthYear)){
	$errcode = 107;
	$errdescr = 'Δεν έχετε συμπληρώσει το έτος γέννησης σας!';
}elseif($Age < 16 && $Age >= 120){
	$errcode = 110;
	$errdescr = 'Το έτος γέννησης δεν είναι έγκυρο!';
}


if($errcode!=0){
	$data['Error'] = $errcode;
	$data['ErrorDescr'] = $errdescr;
	die(json_encode($data));
}
	

$EidEklAr = findEidEklAr($FName, $LName, $PName, $BirthYear);

if($EidEklAr){
	$html = getEidEklArData($EidEklAr, $LName); 	
}else{
	$data['Error'] = 110;
	$data['ErrorDescr'] = '<span style="color:red">Ο Ειδικός Εκλογικός Αριθμός δεν βρέθηκε!<br/>Παρακαλούμε ελέγξτε τα στοιχεία που έχετε εισάγει.</span>';
	die(json_encode($data));	
}

		
$p1 = mb_strpos($html, '<td class="t">Όνομα :</td>');		
$FName = mb_substr($html, $p1 + 46);		
$p2 = mb_strpos($FName, '</td>');			
$FName = mb_substr($FName, 0, $p2);

$p1 = mb_strpos($html, '<td class="t">Όνομα Πατέρα :</td>');		
$PName = mb_substr($html, $p1 + 53);		
$p2 = mb_strpos($PName, '</td>');			
$PName = mb_substr($PName, 0, $p2);

$p1 = mb_strpos($html, '<td class="t">Όνομα Μητέρας :</td>');		
$MName = mb_substr($html, $p1 + 54);		
$p2 = mb_strpos($MName, '</td>');			
$MName = mb_substr($MName, 0, $p2);

session_start();
$_SESSION['FName'] = $FName;
$_SESSION['LName'] = $LName;
$_SESSION['PName'] = $PName;
$_SESSION['MName'] = $MName;
$_SESSION['BirthYear'] = $BirthYear;
$_SESSION['EidEklAr'] = $EidEklAr;



$data['html'] = $html;
$data['EidEklAr'] = $EidEklAr;
$data['FName'] = $FName;
$data['PName'] = $PName;
$data['MName'] = $MName;
$data['Error'] = $errcode;
$data['ErrorDescr'] = $errdescr;
echo json_encode($data);


function findInvalidChars($str){
	$gr1 = array ('Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ',
				  'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω');
	$gr2 = array ( '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',
				   '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '');
	$str  = str_replace($gr1, $gr2, $str);
	$r  = (strlen($str) > 0);
	return $r;
}

?>