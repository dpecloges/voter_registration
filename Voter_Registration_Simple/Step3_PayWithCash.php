<?php
	session_start();
	require_once("Classes/DBConnection.php"); 
	
	$fUniqueKey = $_GET['UniqueKey'];
	
	$sql = "SELECT `VivaOrderID`,`FirstName`,`LastName`,`FathersName`,`MothersName`,`BirthYear` FROM `voter_registration_temp` WHERE `UniqueKey`=?";
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
	        				
	        				Κωδικός Πληρωμής: <b><?php echo $fVivaOrderID;?></b><br><br>
	        				
	        				Όνομα: <?php echo $fFirstName;?><br>
	        				Επώνυμο: <?php echo $fLastName;?><br>
	        				Όνομα Πατέρα: <?php echo $fFathersName;?><br>
	        				Όνομα Μητέρας: <?php echo $fMothersName;?><br>
	        				Έτος Γέννησης: <?php echo $fBirthYear;?><br>
	        				
	        				
	        			</div>
	        		</div>
	        	</div> 
				<br>
				<br>
				
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