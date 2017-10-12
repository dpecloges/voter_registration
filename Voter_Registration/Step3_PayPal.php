<?php
	session_start();
	require_once("Classes/DBConnection.php"); 
	
	if(!isset($_GET['UniqueKey']))
	{
		header('refresh: 0; url=index.php');
		exit;
	}
	
	$fUniqueKey = $_GET['UniqueKey'];
	
	$fErrorCode = 0;
	$fErrorDescription = "";
	
	// Check if email and mobile are verified
	$voterInfoIsVerified = false;
	$sql = "SELECT `EMailIsVerified`,`MobileIsVerified` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
	$command = new MySQLCommand($connection, $sql);
	$command->Parameters->setString(1,$fUniqueKey);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		$voterInfoIsVerified = ($reader->getValue(0)==1) && ($reader->getValue(1)==1);
	}
	$reader->Close();
	
	if(!$voterInfoIsVerified)
	{
		header('refresh: 0; url=Step2.php');
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή</title> 
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
		function ResetData()
		{
			window.location = "index.php";
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
</head>

<body>
    <div class="register-photo" style="background-color:white!important;">
        <div class="form-container">
	        	<div class="row">
	        		<div class="form-group">
	        			<div class="col-sm-8">
	        			<br>
	        			<br>
	        			<br>
		        			Θα μεταβείτε στο PayPal...<br>
		        			ΕΔΩ ΠΡΕΠΕΙ ΝΑ ΒΑΛΟΥΜΕ ΕΝΑ ΚΕΙΜΕΝΟ ΓΙΑ ΕΝΗΜΕΡΩΣΗ ΚΤΛ.....
	        			</div>
	        			
	        			<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" id="PayPalForm">
		        			<input type="hidden" name="cmd" value="_cart"/>
							<input type="hidden" name="lang" value="en"/>
							<input type="hidden" name="business" value="dpekloges@paypal.com"/>
							<input type="hidden" name="upload" value="1"/>
							
							<input type="hidden" name="item_name_1" value="Fee"/>
							<input type="hidden" name="amount_1" value="<?php echo str_replace(",","", number_format(5,2));?>"/>
							<input type="hidden" name="shipping_1" value="0"/>
							<input type="hidden" name="quantity_1" value="1"/>
							
							<input type="hidden" name="currency_code" value="EUR"/>
							<input type="hidden" name="rm" value="2"/>
							<input type="hidden" name="return" value="http://registration.dpekloges.gr/PayPalPaymentStatus.php?Status=OK&UniqueKey=<?php echo $fUniqueKey;?>&t=<?php echo time();?>"/>
							<input type="hidden" name="cancel_return" value="http://registration.dpekloges.gr/PayPalPaymentStatus.php?Status=NOK&UniqueKey=<?php echo $fUniqueKey;?>&t=<?php echo time();?>"/>
							<input type="hidden" name="invoice" value="<?php echo $fUniqueKey;?>"/>
							<input type="hidden" name="custom" value="<?php echo $fUniqueKey;?>"/>
	        			</form>
	        		</div>
	        	</div> 
				<br>
				<br>
				<div class="row">
					<div class="col-sm-6"><button type="button" class="btn btn-danger btn-block" onclick="ResetData();">Επιστροφή στην αρχική</button></div>
					<div class="col-sm-6"><button type="submit" class="btn btn-success btn-block" onclick="SubmitForm();">Μετάβαση σε PayPal</button></div>
				</div> 
        </div>
    </div>

<!--------------------------------------------------------------- ERROR MODAL --------------------------------------------------------------->
  <div class="modal fade" id="ErrModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" align="center">Προσοχή!</h4>
        </div>
        <div class="modal-body">

          <table style="height:100%!important;width:100%!important;border:0">
            <tbody>
              <tr style="height:20px"></tr>
              <tr>
                <td align="center">
                  <div class="alert alert-danger">
                    <span>
                        <div class="row" id="ErrorMsg"></div>
                    </span>
                  </div>
                </td>
              </tr>
              <tr style="height:10px"></tr>
            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Κλείσιμο</button>
        </div>
      </div>
    </div>
  </div>
<!-------------------------------------------------------------------------------------------------------------------------------------------->
</body>
</html>