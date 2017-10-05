<?php
class SMSController
{	
    public function __construct(){ }
    
	public function SendSMSVerificationPin($mobile,$pin)
	{
		$message = "Ο ΚΩΔΙΚΟΣ ΕΠΑΛΗΘΕΥΣΗΣ ΕΙΝΑΙ " . $pin;
		$message = urlencode($message);
		$from = urlencode("A.E.D.D.");
		$mobile = str_replace('+', '', $mobile);
		
		$str = "https://services.yuboto.com/web2sms/api/v2/smsc.aspx?api_key=" . Yuboto_Key . '&action=send&from='.$from.'&to='.$mobile.'&text='.$message;
		$data = file_get_contents($str);
		print $data;
	}
}
?>