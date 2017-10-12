<?php
if(file_Exists("lib/config/config.php")) {require_once("lib/config/config.php");}
require_once("Classes/Mandrill/Mandrill.php");

$fEMails = array();
$fEmails[0] = "nsiatras@gmail.com";
/*$fEmails[1] = "ianast1955@gmail.com";
$fEmails[2] = "t.karounos@gmail.com";*/


for($i=0; $i<sizeof($fEmails); $i++)
{	
	$email = $fEmails[$i];
	
	$timeSend = date("d/m/Y H:i:s",strtotime('+3 hours'));

	$mandrill = new Mandrill(My_Mandrill_Key);
		$message = array(
		'html' => $timeSend,
		'text' => $timeSend,
		'subject' => "Δοκιμή Ταχύτητας Email",
		'from_email' => My_Admin_Email,
		'from_name' => My_Platform_Email_Name,
		'to' => array(
			array(
				'email' => $email,
				'name' => $fullName,
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
}	
?>