<?php
	//session_start();
	require_once("Classes/DBConnection.php"); 
	
	$fUniqueKey = $_GET['UniqueKey'];
	
	//$sql = "SELECT `VivaOrderID`,`FirstName`,`LastName`,`FathersName`,`MothersName`,`BirthYear`,`VoterID`,`PaymentValue` FROM `voter_registration_temp` WHERE `UniqueKey`=?";
	$sql = 'SELECT
			voter_registration_temp.VivaOrderID,
			voter_registration_temp.FirstName,
			voter_registration_temp.LastName,
			voter_registration_temp.FathersName,
			voter_registration_temp.MothersName,
			voter_registration_temp.BirthYear,
			voter_registration_temp.VoterID,
			voter_registration_temp.PaymentValue,
			voter_registration_id_document_types.Title,
			voter_registration_temp.IDDocumentNumber
			FROM
			voter_registration_temp
			INNER JOIN voter_registration_id_document_types ON voter_registration_temp.IDDocumentType = voter_registration_id_document_types.ID 
			WHERE `UniqueKey`=?';
	$command = new MySqlCommand($connection, $sql);
	$command->Parameters->setString(1, $fUniqueKey);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		$fVivaOrderID = $reader->getValue(0);
		
		$fFirstName = $reader->getValue(1);
		$fLastName = $reader->getValue(2);
		$fFathersName = $reader->getValue(3);
		$fMothersName = $reader->getValue(4);
		$fBirthYear = $reader->getValue(5);
		$fVoterID =  $reader->getValue(6);
		$fPaymentValue = $reader->getValue(7)/100;
		
		$fIDDocumentTitle = $reader->getValue(8);
		$fIDDocumentNo = $reader->getValue(9);
	}
	else
	{
		$reader->Close();
		$connection->Close();
		header('refresh: 0; url=index.php');
	}
	
	$reader->Close();
	$connection->Close();

	
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή</title> 
     <link rel="stylesheet" href="style.css?ID=<?php echo time();?>">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="assets/css/Form-Select---Full-Date---Month-Day-Year.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/header1.css">
    <link rel="stylesheet" href="assets/css/header2.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/Registration-Form-with-Photo.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/dist/css/formValidation.min.css">    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="assets/dist/js/formValidation.min.js"></script>
    <script src="assets/dist/js/framework/bootstrap.min.js"></script>
    <script src="assets/dist/js/language/el_GR.js"></script>   
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
    
    <style type="text/css">
    	.register-photo {padding: 0px!important;}
        #RegistrationForm .form-control-feedback {pointer-events: auto;}
        #RegistrationForm .form-control-feedback:hover {cursor: pointer;}
    </style>
    
    <?php include ("lib/config/analytics/php");?>

    
    <script type="text/javascript">
    	
    	$(document).ready(function() {
			JsBarcode("#barCode", "<?php echo $fVivaOrderID ;?>");
		});
    	
    	function PrintDocument()
    	{
    		$('#ButtonPrint').hide();
    		window.print();
    		$('#ButtonPrint').show();
    	}
    	
    	
    </script>
</head>

<body>
    <div class="register-photo" style="background-color:white!important;">
	    <div class="form-container">
        	<div class="row">
        		<div class="form-group">
        			<div  style="width:1200px;margin-left:auto;margin-right:auto;">
        			<br>
        			<br>
        			
        				<div class="PayWithCashNotice">
        					Η πρόθεση συμμετοχής σας στις εκλογές έχει καταγραφεί. Για να μπορέσετε να αποκτήσετε δικαίωμα ψήφου θα πρέπει να εξοφλήσετε τον παρακάτω κωδικό πληρωμής
        				</div>
        				
        				<div class="PayWithCashNotice_Code">
        					
        					<div class="row">
        						<div class="col-sm-3">Κωδικός Πληρωμής:</div>
        						<div class="col-sm-5"> <svg id="barCode"></svg></div>
        					</div>
        					<div class="row">
        						<div class="col-sm-3">Ποσό Πληρωμής:</div>
        						<div class="col-sm-5"><b>&euro;<?php echo number_format($fPaymentValue,2);?></b></div>
        					</div>
        					<br><br>
        				</div>
						
						<div class="PayWithCashNotice_VoterInfo">
							Όνομα: <?php echo $fFirstName;?><br>
	        				Επώνυμο: <?php echo $fLastName;?><br>
	        				Όνομα Πατέρα: <?php echo $fFathersName;?><br>
	        				Όνομα Μητέρας: <?php echo $fMothersName;?><br>
	        				Έτος Γέννησης: <?php echo $fBirthYear;?><br>
	        				Ειδικός Εκλογικός Αριθμός: <?php echo $fVoterID ;?><br>
	        				Έγγραφο ταυτοποίησης: <?php echo $fIDDocumentTitle ;?><br>
	        				Αριθμός εγγράφου: <?php echo $fIDDocumentNo ;?><br>

						</div>
        				
        				<hr>
        		        <div class="PayWithCashNotice_WaysOfPayment">
        		        	Η εξόφληση του παραπάνω κωδικού πληρωμής μπορεί να γίνει στα παρακάτω καταστήματα που συνεργάζονται με την Viva. Για περισσότερες πληροφορίες επισκεφτείτε το
        		        	<a target="_blank" href="https://www.vivapayments.com/el-gr/network">https://www.vivapayments.com/el-gr/network</a>
							<br><br>
							<table cellpadding="0" cellspacing="0" style="width: 100%">	
								<tr>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/88dc8192-6652-4893-825b-e245c87e46d2.png"></td>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/5a58fa72-2e69-4f21-aee4-1a5049d61d9c.png"></td>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/df4398fc-ba3f-4cb4-9ff3-420f8ec9877a.png"></td>
								</tr>
								<tr>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/7a51e9df-d9b7-424a-9f45-96540b1a8ff7.png"></td>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/13bcfbfb-27b4-44e3-a58e-1e3fc99b909b.png"></td>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/3cda0d78-2d57-4b72-b648-46b11dadd668.png"></td>

								</tr>
								<tr>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/d1b62ba7-f233-4d6e-a277-05cca0665b89.png"></td>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/3354082f-b6fe-42db-a7c3-45bc2866cdb6.png"></td>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/bce797e2-54b9-46e9-9686-42e5825d02cb.png"></td>
								</tr>
							</table>
							<table>
								<tr>
									<td><img src="https://www.vivapayments.com/content/img/VivaSpots/logos/dbc3e8ca-d1c2-4485-9226-8ff64d10b18e.png"></td>
								</tr>
							</table>
							<center>
							<br>
								<input id="ButtonPrint" type="button" onclick="PrintDocument();" value="Πατήστε εδώ για εκτύπωση">
							</center>
        		        </div>			
        			</div>
        		</div>
        	</div> 
			<br>
			<br>
        </div>
    </div>
</body>
</html>