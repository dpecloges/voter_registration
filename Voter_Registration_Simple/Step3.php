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
		$sql = "SELECT `EMailIsVerified`,`MobileIsVerified` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
		$command = new MySQLCommand($connection, $sql);
		$command->Parameters->setString(1,$fUniqueKey);
		$reader = $command->ExecuteReader();
		if($reader->Read())
		{
			$voterInfoIsVerified = ($reader->getValue(0)==1) && ($reader->getValue(1)==1);
		}
		$reader->Close();
		
		if(!$voterInfoIsVerified || !$fVoterExists )
		{
			header('refresh: 0; url=Step2.php');
			exit;
		}
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
</head>

<body>
    <div class="register-photo" style="background-color:white!important;">
        <div class="form-container">
	        	<div class="row">
	        		<div class="form-group">
	        			<div  style="width:1200px;margin-left:auto;margin-right:auto;">
	        				
	        				<br>
	        				<br>
	        				<br>
	        				<br>

	        				<div style="text-align:center">
								<font color="#a64d79">Σας παρακαλούμε να 
								πληρώσετε το παράβολο συμμετοχής&nbsp;</font></div>
							<div style="text-align:center">
								<font color="#a64d79">€3.00</font></div>
							<div style="text-align:center">
								<font color="#a64d79">στην VIVA.</font></div>
							<div style="text-align:center">
								<font color="#a64d79">Η διαδικασία είναι απλή 
								και κρατάει μερικά δευτερόλεπτα.&nbsp;</font></div>
							<div style="text-align:center">
								<font color="#a64d79">Καλή συνέχεια μέχρι τις 
								εκλογές!&quot;</font></div>
	        			</div>
	        			
	        			<form action="Step3_GoToViva.php" method="post" id="PayPalForm">
							<input type="hidden" name="invoiceID" value="<?php echo $fUniqueKey;?>"/>
	        			</form>
	        		</div>
	        	</div> 
				<br>
				<br>
				<div class="row">
					<div class="col-sm-6"><button type="button" class="btn btn-danger btn-block" onclick="ResetData();">Επιστροφή <?php if(!$fVoterIsFriend){?>στο Βήμα 2<?php }?></button></div>
					<div class="col-sm-6"><button type="submit" class="btn btn-success btn-block" onclick="SubmitForm();">Μετάβαση σε Viva</button></div>
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