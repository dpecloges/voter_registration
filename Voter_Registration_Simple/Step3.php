<?php
	require_once("Classes/DBConnection.php"); 
	
	if(!isset($_GET['UniqueKey']))
	{
		header('refresh: 0; url=index.php');
		exit;
	}
	
	$fUniqueKey = $_GET['UniqueKey'];
	
	$fErrorCode = 0;
	$fErrorDescription = "";
	
	// Check if voter is a friend
	$fVoterIsFriend = false;
	$fVoterExists = false;
	$sql = "SELECT `IsFriend` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
	$command = new MySQLCommand($connection, $sql);
	$command->Parameters->setString(1,$fUniqueKey);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		$fVoterExists = true;
		$fVoterIsFriend = $reader->getValue(0)==1;
	}
	$reader->close();
	
	
	if(!$fVoterExists )
	{	
		$connection->Close();
		header('refresh: 0; url=index.php');
		exit;
	}
	
	if(!$fVoterIsFriend)
	{
		// Check if email and mobile are verified
		$voterInfoIsVerified = false;
		$sql = "SELECT `MobileIsVerified` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
		$command = new MySQLCommand($connection, $sql);
		$command->Parameters->setString(1,$fUniqueKey);
		$reader = $command->ExecuteReader();
		if($reader->Read())
		{
			$voterInfoIsVerified = ($reader->getValue(0)==1);
		}
		$reader->Close();
		
		if(!$voterInfoIsVerified || !$fVoterExists )
		{
			header('refresh: 0; url=Step2.php');
			exit;
		}
	}
	
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
    
	<script type="text/javascript">
	
		$(document).ready(function() {
			$('#TextBoxValue').hide();
		});
		
		
		function Change()
		{
			var value = $('#RadioButtonPaymentValue:checked').val();
			if(value ==2)
			{
				$('#TextBoxValue').show();
			}
			else
			{
				$('#TextBoxValue').hide();

			}

		}

	
		function ResetData()
		{
			<?php
				if($fVoterIsFriend)
				{
			?>
					window.location = "index.php";
			<?php
				}
				else
				{
			?>
					window.location = "Step2.php?UniqueKey=<?php echo $fUniqueKey;?>";
			<?php
				}
			?>
		}	
		
		function SubmitForm()
		{
			$('#PayPalForm').submit();
		}
		
	</script>

    <style type="text/css">
    	.register-photo {padding: 0px!important;}
        #RegistrationForm .form-control-feedback {pointer-events: auto;}
        #RegistrationForm .form-control-feedback:hover {cursor: pointer;}
    </style>
    
    <?php include ("lib/config/analytics/php");?>

    
    
</head>

<body>
    <div class="register-photo" style="background-color:white!important;">
        <div class="form-container">
        	<div class="row">
        		<div class="form-group">
    				<div class="PaymentNotice">
    					Παρακαλούμε επιλέξτε τον τρόπο με τον οποίο θα θέλατε να πληρώσετε το παράβολο συμμετοχής
    					<br>
    				</div>
        			<div class="PaymentOptions">
	        			<form action="Step3_GoToViva.php" method="post" id="PayPalForm">
	        				
	        				<div><input id="RadioButtonPaymentValue" name="RadioButtonPaymentValue" value="1" onclick="Change();" checked="checked" type="radio" /> Παράβολο &euro; 3.00</div>
	        				<div><input id="RadioButtonPaymentValue" name="RadioButtonPaymentValue" value="2" onclick="Change();" type="radio" /> Άλλο Ποσό	</div>
	        				<div><input id="TextBoxValue" name="TextBoxValue" placeholder="Συμπληρώστε το ποσό που θέλετε σε ευρώ" type="text" style="width: 338px" /></div>
	        				<div>&nbsp;</div>
	        				
	        				<div class="PaymentType"><input name="RadioPayment" value="1" type="radio" checked="checked" /> Πληρωμή με Κάρτα</div>
	        				<div>Θα μεταφερθείτε στο site της Viva για να πληρώσετε το παράβολο συμμετοχής με χρεωστική ή πιστωτική κάρτα</div>
	        				<br>
	        				<br>
	        				<div class="PaymentType"><input name="RadioPayment" value="2" type="radio" /> Πληρωμή με Μετρητά</div>
	        				<div>Θα εμφανιστούν οδηγίες πληρωμής με μετρητά στην οθόνη σας</div>
							<input type="hidden" name="invoiceID" value="<?php echo $fUniqueKey;?>"/>
	        			</form>
        			</div>
        		</div>
        	</div> 
			<br>
			<div class="row">
				<div class="col-sm-6"><button type="button" class="btn btn-danger btn-block" onclick="ResetData();">Επιστροφή <?php if(!$fVoterIsFriend){?>στο Βήμα 2<?php }?></button></div>
				<div class="col-sm-6"><button type="submit" class="btn btn-success btn-block" onclick="SubmitForm();">Πληρωμή</button></div>
			</div> 
        </div>
    </div>
</body>
</html>