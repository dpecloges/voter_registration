<?php
if(file_Exists("lib/config/config.php"))
{

	require_once("lib/config/config.php");
}
require_once("Mandrill/Mandrill.php");


class Mailer
{
    public function __construct(){ }
    
	public function SendEMailVerificationPin($email,$fullName,$pin)
	{
		$subject = 'Επαλήθευση EMAIL';
		
		$body =  '
		<br/>
		<h1>Εκλογές για τον Πρόεδρο του Νέου Φορέα</h1>
		<h3>Ανεξάρτητη Επιτροπή Διαδικασιών & Δεοντολογίας (ΑΕΔΔ)</h3>
		<hr/>
		<br/>
		<br/>
		<span style="font:Verdana, Geneva, sans-serif;font-size:14px">Παρακαλούμε χρησιμοποιήστε τον παρακάτω κωδικό για να επαληθεύσετε το email σας.
		<br/><br/>
		Κωδικός επαλήθευσης : </span>
		<span style="font:Verdana, Geneva, sans-serif; font-size:24px">'.$pin.'</span>
		<br/><br/>';

		try 
		{
			$mandrill = new Mandrill(My_Mandrill_Key);
			$message = array(
				'html' => $body,
				'text' => 'Δεν υποστηρίζεται!',
				'subject' => $subject,
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
		catch(Mandrill_Error $e) 
		{
			throw $e;
		}
	}
	
	public function SendRegistrationThankYouEmail($email,$fullName,$id)
	{
		$subject = 'Ολοκληρωση Προεγγραφής';
		
		$body =  '
		<br/>
		<h1>Εκλογές για τον Πρόεδρο του Νέου Φορέα</h1>
		<h3>Ανεξάρτητη Επιτροπή Διαδικασιών & Δεοντολογίας (ΑΕΔΔ)</h3>
		<hr/>
		<br/>
		<br/>
		<br/>
		<br/>
		<span style="font:Verdana, Geneva, sans-serif;font-size:14px">H προεγγραφή σας εχει ολοκληρωθεί με επιτυχία!<br></span>
		<span style="font:Verdana, Geneva, sans-serif;font-size:14px">
			Για την δική σας διευκόλυνση παρακαλείστε κατα την μετάβαση σας στο εκλογικό κέντρο<br>
			μαζί με την αστυνομική σας ταυτότητα να έχετε και τον κωδικό προεγγραφής.
		</span>
		<br>
		<br>
		<br>
		<span style="font:Verdana, Geneva, sans-serif; font-size:24px">Κωδικός Προεγγραφής<b>: '.$id.'</b></span>
		<br/>';

		try 
		{
			$mandrill = new Mandrill(My_Mandrill_Key);
			$message = array(
				'html' => $body,
				'text' => 'Δεν υποστηρίζεται!',
				'subject' => $subject,
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
		catch(Mandrill_Error $e) 
		{
			throw $e;
		}
	}

}
?>