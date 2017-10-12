<?php
class VivaPaymentManager
{
	
	public $fVivaURL   =  'http://demo.vivapayments.com';		// demo environment URL
	//private $fVivaURL = 'https://www.vivapayments.com/api/orders';	// production environment URL
	public $fVivaMerchantID =  'f8e9b647-9676-40e6-ba54-38030689d1ce';
	public $fVivaAPIKey = '=14=.|';

    public function __construct()
    {
    }
    
   

    
    public function CancelPreviousOrdersForVoterID($connection,$voterID)
    {
		$sql = "SELECT `VivaOrderID` FROM `voter_registration_temp` WHERE `VoterID`=?";
		$command = new MySqlCommand($connection,$sql);
		$command->Parameters->setInteger(1, $voterID);
		$reader = $command->ExecuteReader();
		while($reader->Read())
		{
			$orderID = $reader->getValue(0);
			$this->CancelVivaOrder($orderID);
		}
		$reader->Close();
    }
    
    public function CancelVivaOrder($vivaOrderID)
    {    	
    	// Get the curl session object
		$session = curl_init($this->fVivaURL."/api/orders/".$vivaOrderID);
		
		curl_setopt($session, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_USERPWD, $this->fVivaMerchantID .':'.$this->fVivaAPIKey );
		curl_setopt($session, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
		// Do the GET and then close the session
		$response = curl_exec($session);
		curl_close($session);
    }
    
    
}
?>