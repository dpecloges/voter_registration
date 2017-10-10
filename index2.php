<?php
	session_start();
	require_once("Classes/DBConnection.php"); 
		
	require_once("Classes/GoogleReCaptcha.php"); 
	$fGoogleReCaptcha = new GoogleReCaptcha();

	require_once("Classes/VotersManager.php"); 
	$fVotersManager = new VotersManager();
	
	require_once("Classes/TextHelper.php"); 
	$fTextHelper = new TextHelper();

	$fFirstName    = "ΝΙΚΟΛΑΟΣ";
	$fLastName     = "ΣΙΑΤΡΑΣ";
	$fFathersName  = "ΚΩΝΣΤΑΝΤΙΝΟΣ";
	$fMothersName  = "ΜΑΡΙΑ";
	$fBirthYear    = 1986;

	$fVoterIdFound = false;
	if(isset($_POST['TextBoxFirstName']))
	{
	
		$fCaptchaIsVerified = $fGoogleReCaptcha->Verify($_POST['g-recaptcha-response']);		
	
		$fFirstName     = trim($_POST['TextBoxFirstName']);
		$fLastName      = trim($_POST['TextBoxLastName']);
		$fFathersName   = trim($_POST['TextBoxFathersName']);
		$fMothersName   = trim($_POST['TextBoxMothersName']);
		$fBirthYear     = trim($_POST['TextBoxBirthYear']);

		// Step 1 - Validate input data
		$fErrorCode = 0 ;
		
		$userInputErrosFound = false;
		if(mb_strlen($fFirstName) < 2)
		{
			$fFirstNameError = "Παρακαλούμε εισάγετε έγκυρο Όνομα! (τουλάχιστον 4 χαρακτήρες)";
			$userInputErrosFound = true;
		}
		
		if(mb_strlen($fLastName) < 2)
		{
			$fLastNameError = "Παρακαλούμε εισάγετε έγκυρο Επώνυμο! (τουλάχιστον 4 χαρακτήρες)";
			$userInputErrosFound = true;			
		}
		
		if(mb_strlen($fFathersName) < 2)
		{
			$fFathersNameError = "Παρακαλούμε εισάγετε το Πατρώνυμο σας!";
			$userInputErrosFound = true;			
		}
		
		if(mb_strlen($fMothersName) < 2)
		{
			$fMothersNameError = "Παρακαλούμε εισάγετε το Όνομα της Μητέρας σας!";
			$userInputErrosFound = true;			
		}
		
		if($userInputErrosFound )		
		{
			$fErrorCode = 101;
			$fErrorDescription = 'Παρακαλούμε ελέγξτε αν τα στοιχεία που έχετε εισάγει<br>είναι ίδια με αυτά της αστυνομικής σας ταυτότητας';
		}
		else
		{
			if(!$fTextHelper->CheckIfStringContainsOnlyGreekCapitalCharacters($fFirstName ))
			{
				$fErrorCode = 104;
				$fErrorDescription = 'Το όνομα σας περιέχει λατινικούς ή ειδικούς χαρακτήρες!';
			}
			elseif(!$fTextHelper->CheckIfStringContainsOnlyGreekCapitalCharacters($fLastName ))
			{
				$fErrorCode = 105;
				$fErrorDescription = 'Το επώνυμο σας περιέχει λατινικούς ή ειδικούς χαρακτήρες!';
			}
			elseif(!$fTextHelper->CheckIfStringContainsOnlyGreekCapitalCharacters($fFathersName ))
			{
				$fErrorCode = 106;
				$fErrorDescription = 'Το πατρώνυμο σας περιέχει λατινικούς ή ειδικούς χαρακτήρες!';
			}
			elseif($fBirthYear<1900)
			{
				$fErrorCode = 107;
				$fErrorDescription = 'Δεν έχετε συμπληρώσει το έτος γέννησης σας!';
			}
			elseif(!$fCaptchaIsVerified)
			{
				$fErrorCode = 108;
				$fErrorDescription = 'Δεν έχετε επαληθέυσει τον κωδικό ασφαλείας!';
			}
		}

		
		// Step 2 - Get Voter ID and Data
		if($fErrorCode == 0 && $fCaptchaIsVerified)
		{
			$fVoterData  = "";
			$fVoterData = $fVotersManager->GetVoterData($fFirstName, $fLastName, $fFathersName,$fMothersName, $fBirthYear);
			$fVoterID = $fVoterData->Eid_ekl_ar;
			
			if( $fVoterData->Onoma!="" &&  $fVoterData->Onoma!=null)
			{
				$fFirstName = $fVoterData->Onoma;
				$fLastName = $fVoterData->Eponymo;
				$fFathersName = $fVoterData->on_pat;
				$fMothersName = $fVoterData->on_mht;
			}
			
			$fArithmosDimotologiou = $fVoterData->dhmot;
			$fDhmos = $fVoterData->Dimos;
			$fDimotikiEnotita = $fVoterData->Dimotiki_Enotita;
			$fEklogikhPerifereia = $fVoterData->EKL_PERIF;
			$fPeriferiakiEnothta = $fVoterData->per_enotita;
			$fPeriferia = $fVoterData->PERIFER;
			$fNomos =  $fVoterData->NOMOS;
			
			if($fVoterID != "")
			{
				$fVoterIdFound = true;
				
				// Check if voter is already registered
				$voterIsAlreadRegistered = false;
				$sql = "SELECT `ID` FROM `voter_registration` WHERE `VoterID`=?";
				$command = new MySQLCommand($connection, $sql);
				$command->Parameters->setInteger(1, $fVoterID);
				$reader = $command->ExecuteReader();
				if($reader->Read())
				{
					$voterIsAlreadRegistered = true;
				}
				$reader->Close();
				
				if($voterIsAlreadRegistered)
				{
					$fVoterData = "";
					$fVoterIdFound = false;
					$fErrorCode = 111;
					$fErrorDescription = 'Είστε ήδη εγγεγραμμένος!';
				}
				else
				{
					
					// Write voter to temp
				    $fUniqueKey = md5(time().$fLastName.$$fMothersName.rand(1,1000));
					$sql = "INSERT INTO `voter_registration_temp` (`UniqueKey`,`FirstName`,`LastName`,`FathersName`,`MothersName`,`BirthYear`,`VoterID`,`ArithmosDimotologiou`,`Dhmos`,`DhmotikhEnothta`,`EklogikhPerifereia`,`PerifereiakiEnothta`,`Perifereia`,`Nomos`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
					$command = new MySQLCommand($connection, $sql);
					$command->Parameters->setString(1, $fUniqueKey);
					$command->Parameters->setString(2, $fFirstName);
					$command->Parameters->setString(3, $fLastName);
					$command->Parameters->setString(4, $fFathersName);
					$command->Parameters->setString(5, $fMothersName);
					$command->Parameters->setInteger(6, $fBirthYear);
					$command->Parameters->setInteger(7, $fVoterID);
					
					$command->Parameters->setString(8, $fArithmosDimotologiou);
					$command->Parameters->setString(9, $fDhmos);
					$command->Parameters->setString(10, $fDimotikiEnotita);
					$command->Parameters->setString(11, $fEklogikhPerifereia);
					$command->Parameters->setString(12, $fPeriferiakiEnothta );
					$command->Parameters->setString(13, $fPeriferia );
					$command->Parameters->setString(14, $fNomos );
					
					$command->ExecuteQuery();
				}
			}
			else
			{
				$fCaptchaIsVerified = false;
				$fVoterIdFound = false;
				$fErrorCode = 110;
				$fErrorDescription = 'Ο Ειδικός Εκλογικός Αριθμός δεν βρέθηκε!<br/>Παρακαλούμε ελέγξτε αν τα στοιχεία που έχετε εισάγει<br>είναι ίδια με αυτά της αστυνομικής σας ταυτότητας';
			}
		}
	}
	else
	{
		session_destroy();
	}
	
	if($fErrorCode>0)
	{
		$fCaptchaIsVerified = false;
	}
	
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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/dist/js/formValidation.min.js"></script>
    <script type="text/javascript" src="assets/dist/js/framework/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/dist/js/language/el_GR.js"></script>
	
	<script type="text/javascript" src="js/StepFormsJS/TextFunctions.js"></script>
	<script type="text/javascript">
	
	
	$(document).ready(function() {
		$('#TextBoxFirstName')[0].oninput = function(){
			FixTextInputToUpperCaseGreek($('#TextBoxFirstName'));
     	};
		
		$('#TextBoxLastName')[0].oninput = function(){
			FixTextInputToUpperCaseGreek($('#TextBoxLastName'));
		};
	
		$('#TextBoxFathersName')[0].oninput = function(){
			FixTextInputToUpperCaseGreek($('#TextBoxFathersName'));
		};
		
		$('#TextBoxMothersName')[0].oninput = function(){
			FixTextInputToUpperCaseGreek($('#TextBoxMothersName'));
		};

		$('#TextBoxBirthYear')[0].oninput = function(){
			RemoveCharactersThatAreNotNumbers($('#TextBoxBirthYear'));
		};
	});
	

	
	function RestartProcess() { window.location = "/index.php"; }
	function GoToStep2() { window.location = "/Step2.php?UniqueKey=<?php echo $fUniqueKey;?>";}
	<?php
		if($fErrorCode>0)
		{
	?>
			$(document).ready(function() {
				$("#ErrorMsg").html('<?php echo $fErrorDescription;?>');
				$('#ErrModal').modal('show');
			});
	<?php
		}
	?>
	
	  var imNotARobot = function() {
	   	/*if ($('#TextBoxFirstName').val()!="" && $('#TextBoxLastName').val()!="" && $('#TextBoxFathersName').val()!="" && $('#TextBoxMothersName').val()!="" && $('#TextBoxBirthYear').val()!="")
	   	{
	   		$('#RegistrationForm').submit();
	   	}
	   	else
	   	{
		   	$("#ButtonFind").prop("style").visibility="visible";
		   	$('#Captcha').prop("style").visibility="hidden";
	   	}*/
	  };
	</script>
	
	<script src='https://www.google.com/recaptcha/api.js'></script>

    <style type="text/css">    	
    	.register-photo {padding: 0px!important;}
        #RegistrationForm .form-control-feedback { pointer-events: auto;}
        #RegistrationForm .form-control-feedback:hover { cursor: pointer;}
    </style>
</head>
<body>
    <div class="register-photo" style="background-color:white!important;">
        <div class="form-container">
            <form method="post" action="" id="RegistrationForm" name="RegistrationForm<?php echo time();?>">
                <div class="form-group">
                    <input class="form-control" type="text" placeholder="* Όνομα" name="TextBoxFirstName" id="TextBoxFirstName" value="<?php echo $fFirstName;?>" maxlength="30" />
                	<small class="help-block" style="color:red;"><?php echo $fFirstNameError;?></small>
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" placeholder="* Επώνυμο" name="TextBoxLastName" id="TextBoxLastName" value="<?php echo $fLastName;?>" maxlength="30" />
                	<small class="help-block" style="color:red;"><?php echo $fLastNameError;?></small>
                </div>
                <div class="form-group"> 
                    <input class="form-control" type="text" placeholder="* Πατρώνυμο" name="TextBoxFathersName" id="TextBoxFathersName" value="<?php echo $fFathersName;?>" maxlength="30" />
                	<small class="help-block" style="color:red;"><?php echo $fFathersNameError;?></small>
                </div>
                <div class="form-group"> 
                    <input class="form-control" type="text" placeholder="* Ονομα Μητέρας" name="TextBoxMothersName" id="TextBoxMothersName" value="<?php echo $fMothersName;?>" maxlength="30" />
                	<small class="help-block" style="color:red;"><?php echo $fMothersNameError;?></small>
                </div>
                
                 <div class="form-group"> 
                    <div class="row">
	                    <div class="col-sm-3">
							<input class="form-control" type="text" placeholder="* Έτος Γέννησης" name="TextBoxBirthYear" id="TextBoxBirthYear" value="<?php echo $fBirthYear;?>" maxlength="4" style="width: 232px" /></div>
						<div class="col-sm-9"> 								
						<?php
								if(!$fCaptchaIsVerified)
								{
							?>
							<div class="g-recaptcha" id="Captcha" data-callback="imNotARobot" data-sitekey="6Ld37TIUAAAAAEbyCPZTv2OgcYwBgVo4fJXKzWgP"></div>
							<?php
								}
							?>
						</div>
					</div>
                </div>


                <div class="well" style="height:500px!important">
                    <span>* Ειδικός Εκλογικός Αριθμός <b>(Μάθε που ψηφίζεις)</b></span>
                    
                    <button class="btn btn-primary-small" type="button" id="ButtonFind" <?php if($fVoterIdFound){ ?> disabled="disabled" <?php }?> onclick="RegistrationForm.submit();">Αναζήτηση</button>
                    <br />
                    <br />
                    <div class="row">
                        
                    </div>
                    <div class="row">&nbsp;</div>
                    <div class="row">&nbsp;</div>
					<div class="row">
						<?php 
							if($fVoterID != "")
							{
						?>
						<div class="col-sm-12">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-3">Ειδικός Εκλογικός Αριθμός:</div>
									<div class="col-sm-9"><b><?php echo $fVoterID;?></b></div>
								</div>
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="col-sm-3">Αριθμός Δημοτολογίου:</div>
									<div class="col-sm-9"><b><?php echo $fArithmosDimotologiou;?></b></div>
								</div>
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="col-sm-3">Όνομα Πατέρα:</div>
									<div class="col-sm-9"><b><?php echo $fFathersName;?></b></div>
								</div>
								<div class="row">
									<div class="col-sm-3">Όνομα Μητέρας: </div>
									<div class="col-sm-9"><b><?php echo $fMothersName;?></b></div>
								</div>
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="col-sm-3">Δήμος:</div>
									<div class="col-sm-9"><b><?php echo $fDhmos;?></b></div>
								</div>
								<div class="row">
									<div class="col-sm-3">Δημοτική Ενότητα:</div>
									<div class="col-sm-9"><b><?php echo $fDimotikiEnotita;?></b></div>
								</div>
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="col-sm-3">Εκλογική Περιφέρεια:</div>
									<div class="col-sm-9"><b><?php echo $fEklogikhPerifereia ;?></b></div>
								</div>
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="col-sm-3">Περιφερειακή Ενότητα:</div>
									<div class="col-sm-9"><b><?php echo $fPeriferiakiEnothta ;?></b></div>
								</div>
								<div class="row">
									<div class="col-sm-3">Περιφέρεια:</div>
									<div class="col-sm-9"><b><?php echo $fPeriferia ;?></b></div>
								</div>
								<div class="row">
									<div class="col-sm-3">Νομός:</div>
									<div class="col-sm-9"><b><?php echo $fNomos ;?></b></div>
								</div>
							</div>
						</div>
					<?php
						}
					?>		
					</div>
                </div>
				<div class="row">
					<div class="col-sm-6"><div class="btn btn-danger btn-block" onclick="RestartProcess();">Καθαρισμός φόρμας</div></div>
					<div class="col-sm-6"><div class="btn btn-success btn-block" <?php if(!$fVoterIdFound){ ?> style="visibility:hidden;"<?php }?> onclick="GoToStep2();">Επόμενο βήμα</div></div>
				</div>
            </form>
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
                        <div class="row" id="ErrorMsg">&nbsp;</div>
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