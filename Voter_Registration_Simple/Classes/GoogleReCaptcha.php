<?php
class GoogleReCaptcha
{
    public function __construct(){ }
    
    // Get new key from
    // https://www.google.com/recaptcha/admin#list
   	//private $fGoogleRecaptchaKey = "6Ld37TIUAAAAAJzds1aK1Ac6BjZOEuAX-pBfUfE5";

   	
	public function Verify($response,$recaptchaSecretKey)
	{
		try
		{
			$url = "https://www.google.com/recaptcha/api/siteverify";
			$data = array('secret' => $recaptchaSecretKey, 'response' => $response);
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
			if($reply->success == true)
			{
				return true;
			}
		}
		catch(Exception $ex)
		{
			return false;
		}
		return false;
	}
}
?>