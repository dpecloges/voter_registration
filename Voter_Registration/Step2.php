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
	
	if(isset($_POST['TextBoxEmail']))
	{
		$fEmail = $_POST['TextBoxEmail'];
		$fMobilePhone = $_POST['TextBoxMobile'];
		$fPhone = $_POST['TextBoxTelephone'];
		$fStreet = trim($_POST['TextBoxStreetName']);
		$fStreetNo = trim($_POST['TextBoxStreetNumber']);
		$fArea = trim($_POST['TextBoxArea']);
		$fZip = trim($_POST['TextBoxZipCode']);
		
		$fMunicipalityID = intval($_POST['ComboBoxMunicipality']);
		$sql = "SELECT `KOD_DHM`,`ONOMA` FROM `YPES_DHMOI` WHERE `KOD_DHM`=".$fMunicipalityID;
		$command = new MySqlCommand($connection,$sql);
		$reader = $command->ExecuteReader();
		while($reader->Read())
		{
			$fMunicipality = $reader->getValue(1);
		}
		$reader->Close();
		
		$fCounty = trim($_POST['TextBoxDivision']);
		
		$addressIsValid = false;
				
		if(isset($_POST['NoNumbersAddress']))
		{
			$addressIsValid = $fStreet!="" && $fZip !="" && $fMunicipality !="";
		}
		else
		{
			$addressIsValid = $fStreet!="" && $fStreetNo!="" && $fZip !="" && $fMunicipality !="";
		}
		
		if(!$addressIsValid)
		{
			$fErrorCode = 101;
			$fErrorDescription = 'Δεν έχετε εισάγει την Διεύθυνση κατοικίας σας !';
		}
		
		
		$sql = "UPDATE `voter_registration_temp` SET `Phone`=?,`Street`=?,`StreetNo`=?,`Zip`=?,`Municipality`=?,`MunicipalityID`=?,`County`=?  WHERE `UniqueKey`=?";
		$command = new MySQLCommand($connection, $sql);
		$command->Parameters->setString(1,$fPhone);
		$command->Parameters->setString(2,$fStreet);
		$command->Parameters->setString(3,$fStreetNo);
		$command->Parameters->setString(4,$fZip);
		$command->Parameters->setString(5,$fMunicipality);
		$command->Parameters->setInteger(6,$fMunicipalityID);
		$command->Parameters->setString(7,$fCounty);
		$command->Parameters->setString(8,$fUniqueKey);
		$command->ExecuteQuery();

		
		$sql = "SELECT `EMailIsVerified`,`MobileIsVerified` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
		$command = new MySQLCommand($connection, $sql);
		$command->Parameters->setString(1,$fUniqueKey);
		$reader = $command->ExecuteReader();
		if($reader->Read())
		{
			$emailIsVerified = $reader->getValue(0);
			$mobileIsVerified = $reader->getValue(1); 
			
			if ($emailIsVerified==1 && $mobileIsVerified ==1 && $addressIsValid)
			{
				header('refresh: 0; url=Step3.php?UniqueKey='.$fUniqueKey);
				exit;
			}
			else
			{
				if($emailIsVerified!=1)
				{
					$fEMailError = "Το πεδίο Email είναι υποχρεωτικό!";
				}
				
				if ($mobileIsVerified!=1)
				{
					$fMobileError = "Το πεδίο κινητό τηλέφωνο είναι υποχρεωτικό!";
				}
			}	
		}
		$reader->Close();
		
	}
	
	
	// Get User Data from db
	$tempRecordFound = false;
	$sql = "SELECT `EMail`,`EMailIsVerified`,`MobilePhone`,`MobileIsVerified`,`Phone`,`Street`,`StreetNo`,`Zip`,`Municipality`,`County`,`MunicipalityID`FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
	$command = new MySQLCommand($connection, $sql);
	$command->Parameters->setString(1,$fUniqueKey);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		$tempRecordFound = true;
		$fEmail = $reader->getValue(0)!=""? $reader->getValue(0) : $fEmail;
		$fEMailIsVerified = $reader->getValue(1);
		$fVerifyEmailButtonLabel = ($fEMailIsVerified ==1) ? "Επαληθέυτηκε" :  'Επαλήθευση Email';
		
		$fMobilePhone = $reader->getValue(2) !=""? $reader->getValue(2) : $fMobilePhone ;
		$fMobileIsVerified = $reader->getValue(3);
		$fVerifyMobileButtonLabel = ($fMobileIsVerified ==1) ? "Επαληθέυτηκε" :  'Επαλήθευση κινητού τηλεφώνου';
		
		$fPhone = $reader->getValue(4);
		$fStreet = $reader->getValue(5);
		$fStreetNo = $reader->getValue(6);
		$fZip = $reader->getValue(7);
		$fMunicipality = $reader->getValue(8);
		$fCounty = $reader->getValue(9);
		$fMunicipalityID = $reader->getValue(10);
	}
	$reader->Close();
	
	if(!$tempRecordFound)
	{
		header('refresh: 0; url=index.php');
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
    
    <script type="text/javascript" src="js/StepFormsJS/TextFunctions.js?ID=<?php echo time();?>"></script>

    
    <!-- Google Map Scripts -->
    <script type="text/javascript" src="js/StepFormsJS/Step2_GoogleMap.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo My_Google_API_Key;?>&libraries=places&language=el&callback=GoogleMapLoaded"></script>
   
    <!--------------------------->
    
	<script type="text/javascript">
		
		function GoogleMapLoaded()
		{
			$('#LoadingText').hide();
			$("#RegistrationForm").css("visibility", "visible");

			InitGoogleMap();
			ShowAddressOnMapAccordingToUserInput();
		}

		$(document).ready(function() {

			PreventFormSubmitOnEnterButtonHit();
			
			$('#TextBoxStreetName')[0].oninput = function(){
				FixTextInputToUpperCaseGreekWithSpaces($('#TextBoxStreetName'));
				ShowAddressOnMapAccordingToUserInput();
	     	};
	     	
	     	$('#TextBoxStreetNumber')[0].oninput = function(){
				FixTextInputToNumbersOnly($('#TextBoxStreetNumber'));
				ShowAddressOnMapAccordingToUserInput();
	     	};
	     	
	     	$('#TextBoxZipCode')[0].oninput = function(){
				FixTextInputToNumbersOnly($('#TextBoxZipCode'));
				ShowAddressOnMapAccordingToUserInput();
	     	};

	    	$('#TextBoxArea')[0].oninput = function(){
				FixTextInputToUpperCaseGreekWithSpaces($('#TextBoxArea'));
				ShowAddressOnMapAccordingToUserInput();
	     	};
	     	

			$('#ErrorMsgAddress').hide();
		
			<?php
				if($fErrorCode>0)
				{
			?>
					$("#ErrorMsg").html('<?php echo $fErrorDescription;?>');
					$('#ErrModal').modal('show');
			<?php
				}
			?>
		});
		
		
		function ShowAddressOnMapAccordingToUserInput()
		{
			if($("#NoNumbersAddress").is(':checked'))
			{
				if($('#TextBoxStreetName').val()!="" &&  $('#TextBoxZipCode').val()!="" && $('#TextBoxZipCode').val().length>4)
				{
					var address = $('#TextBoxStreetName').val()+ ' ' + $('#TextBoxStreetNumber').val() +"," +  $('#TextBoxZipCode').val() +",Greece";
					ShowAddressOnGoogleMap(address);
				}
			}
			else
			{
				if($('#TextBoxStreetName').val()!="" && $('#TextBoxStreetNumber').val() !="" &&  $('#TextBoxZipCode').val()!="" && $('#TextBoxZipCode').val().length>4)
				{
					var address = $('#TextBoxStreetName').val()+ ' ' + $('#TextBoxStreetNumber').val() +"," +  $('#TextBoxZipCode').val() +",Greece";
					ShowAddressOnGoogleMap(address);
				}
			}
		}
		
		function PreventFormSubmitOnEnterButtonHit()
		{
			$(window).keydown(function(event)
			{
				if(event.keyCode == 13) 
				{
					event.preventDefault();
					return false;
				}
			});
		}
		
		// Google Map
      	function GetCountyFromMunicipality()
		{
			var municipality = $('#ComboBoxMunicipality').val();
			$.post('AJAX_GetCountyFromMunicipality.php', {Municipality:municipality }, 
				function(data){
					$("#TextBoxDivision").val(data);
					ShowAddressOnMap();
				});	
		}

	    function NoNumbersAddressClick()
	    {
	    	if($("#NoNumbersAddress").is(':checked'))		
			{
		    	$('#TextBoxStreetNumber').attr('disabled', true);
				$('#TextBoxStreetNumber').css('background-color', '#fffdf3');
			}
			else
			{
				$('#TextBoxStreetNumber').attr('disabled', false);
				$('#TextBoxStreetNumber').css('background-color', '');
			}
	    }
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		function OpenEmailVerificationForm()
		{
			try
			{
				$('#emailError').html("");
				if(IsEMailValid($('#TextBoxEmail').val()))
				{
					$('#SendEmailMsg').show();
					$('#ButtonSendEmailVerification').show();	
					$("#ButtonSendEmailVerification").attr("disabled", false);
					$('#EmailModal').modal('show');	
					$('#ErrorMsgEmail').hide();
					$('#SendEmailDiv').hide();
					$('#BtnCheckEmailCode').hide();
				}
				else
				{
					$('#emailError').html("Το πεδίο Email είναι υποχρεωτικό!");
				}
			}
			catch(e)
			{
				alert(e);
			}
		}	
				
		function SendEMailVerificationCode()
		{
			$("#ButtonSendEmailVerification").attr("disabled", true);		
			$.post("AJAX_SendEmailVerificationCode.php", { Email: $("#TextBoxEmail").val(), UniqueKey: '<?php echo $fUniqueKey;?>' })
		  	.done(function(data) {	  
		  		if(data.Error==0)
		   	 	{
			   	 	$('#SendEmailMsg').hide();
					$('#ButtonSendEmailVerification').hide();		
					$('#SendEmailDiv').show();
					$('#BtnCheckEmailCode').show();
					$('#TextBoxEMailPin').focus();
				}
				else if(data.Error==120)
				{
					$('#SendEmailMsg').hide();
					$('#ButtonSendEmailVerification').hide();		
					$('#SendEmailDiv').show();
					$('#BtnCheckEmailCode').show();
					$('#TextBoxEMailPin').focus();
					$("#ErrorMsgEmail").html(data.ErrorDescr);
					$('#ErrorMsgEmail').show();
				}
				else
				{
					$("#ErrorMsgEmail").html(data.ErrorDescr);
					$('#ErrorMsgEmail').show();
				}
		  });
		}
		
		function VerifyEmailFromPin()
		{
			$.post('AJAX_VerifyEmailFromPin.php', {Pin: $("#TextBoxEMailPin").val(), UniqueKey: '<?php echo $fUniqueKey;?>'}, 
			function(data){
				if(data.Error==0)
		   	 	{
					$('#EmailModal').modal('hide');	
					$('#ButtonCheckEmail').text("Επαληθεύτηκε");
					$('#TextBoxEmail').attr("readonly", true);
					$("#ButtonCheckEmail").attr("disabled", true);
		   	 	}
		   	 	else
		   	 	{
		   	 		$("#ErrorMsgEmail").html(data.ErrorDescr);
					$('#ErrorMsgEmail').show();
					$('#MobilePIN').focus();
					$('#ErrorMsgEmail').hide();
		   	 	}
			});	
		}
		
		function CancelEmailVerification()
		{
			$('#EmailModal').modal('hide');	
		}
		
		
		function OpeMobileVerificationForm()
		{
			var mobileNo = $('#TextBoxMobile').val();
			if(mobileNo.length<10 || mobileNo .substring(0, 2)!="69")
			{
				$('#mobileError').html("Το πεδίο κινητό τηλέφωνο είναι υποχρεωτικό!");
			}
			else
			{
				$("#TextBoxMobilePin").val("");
				$('#ErrorMsgMobile').hide()
				$('#SendMobileMsg').show();
				$('#SendMobileDiv').hide();
				$('#ButtonCheckMobilePin').hide();
				$('#ButtonSendSMSVerificationPin').show();
				$('#ButtonSendVoiceVerificationPin').show();
				$('#MobileModal').modal('show');
			}
		}	
		
		function SendMobileSMSVerificationPin()
		{
			$.post("AJAX_SendMobileSMSVerificationCode.php", { Mobile: $("#TextBoxMobile").val(), UniqueKey: '<?php echo $fUniqueKey;?>' })
		  	.done(function(data) {	  
	   	 		if(data.Error==0)
		   	 	{
			   	 	$('#ErrorMsgMobile').hide()
					$('#SendMobileMsg').hide();
					$('#SendMobileDiv').show();
					$('#ButtonCheckMobilePin').show();
					$('#MobileModal').modal('show');
					$('#ButtonSendSMSVerificationPin').hide();
					$('#ButtonSendVoiceVerificationPin').hide();
				}
				else if(data.Error==120)
				{
					$('#ErrorMsgMobile').hide()
					$('#SendMobileMsg').hide();
					$('#SendMobileDiv').show();
					$('#ButtonCheckMobilePin').show();
					$('#MobileModal').modal('show');
					$('#ButtonSendSMSVerificationPin').hide();
					$('#ButtonSendVoiceVerificationPin').hide();
					$("#ErrorMsgMobile").html(data.ErrorDescr);
					$('#ErrorMsgMobile').show();
				}
				else
				{
					$("#ErrorMsgMobile").html(data.ErrorDescr);
					$('#ErrorMsgMobile').show();
					//$('#TextBoxMobilePin').focus();
				}
				
		  });
		}
		
		function SendMobileVerificationVoiceMessage()
		{
			alert("VOICE");
		}
		
		
		function VerifyMobileFromPin()
		{
			$.post('AJAX_VerifyMobileFromPin.php', {Pin: $("#TextBoxMobilePin").val(), UniqueKey: '<?php echo $fUniqueKey;?>'}, 
			function(data){
				if(data.Error==0)
		   	 	{
					$('#MobileModal').modal('hide');	
					$('#ButtonCheckMobile').text("Επαληθεύτηκε");
					$("#ButtonCheckMobile").attr("disabled", true);
					$('#TextBoxMobile').attr("readonly", true);
					$('#mobileError').hide();
		   	 	}
		   	 	else
		   	 	{
		   	 		$("#ErrorMsgMobile").html(data.ErrorDescr);
					$('#ErrorMsgMobile').show();
					$('#TextBoxMobilePin').focus();
		   	 	}
			});	

		}
		
		function CancelMobileVerification()
		{
			$('#MobileModal').modal('hide');	
		}
		
		function ResetData()
		{
			window.location = "index.php";
		}
		
		function IsEMailValid(email) 
		{
		    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		    if (filter.test(email)) 
		    {
			    return true;
		    }
			return false;		   
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
        	
        	<div id="LoadingText">
        		<div class="row">
            		<div class="col-sm-6">
        				Παρακαλω περιμένετε....
        			</div>
        		</div>
        	</div>
        	
            <form method="post" action="" id="RegistrationForm" style="visibility:hidden;">
            	<div class="row">
            		<div class="col-sm-6">
            			<br/><br/>   
		                <div class="form-group">
		                    <input type="email" class="form-control" <?php if($fEMailIsVerified){?> readonly="readonly"<?php }?> placeholder="* Email" name="TextBoxEmail" id="TextBoxEmail" value="<?php echo $fEmail;?>" maxlength="50" />
		                	<small class="help-block" style="color:red;" id="emailError"><?php echo $fEMailError;?></small>
		                </div>
		                
		                <button type="button" class="btn btn-default" <?php if($fEMailIsVerified ==1){?>disabled="disabled"<?php }?> id="ButtonCheckEmail" onclick="OpenEmailVerificationForm();" ><?php echo $fVerifyEmailButtonLabel;?></button>
		                <br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" class="form-control" placeholder="* Τηλέφωνο κινητό" name="TextBoxMobile" id="TextBoxMobile" maxlength="10" value="<?php echo $fMobilePhone;?>" />
		                	<small class="help-block" style=" color:red;" id="mobileError"><?php echo $fMobileError;?></small>
		                </div>
						<button type="button" class="btn btn-default" <?php if($fMobileIsVerified ==1){?>disabled="disabled"<?php }?> id="ButtonCheckMobile" onclick="OpeMobileVerificationForm();"><?php echo $fVerifyMobileButtonLabel;?></button>
						<br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" style="visibility:collapse;" class="form-control" placeholder="Τηλέφωνο σταθερό" name="TextBoxTelephone" id="TextBoxTelephone" maxlength="10" value="<?php echo $fPhone;?>" />
		                </div>            			
            		</div>
            		<div class="col-sm-6">
            			<div id="map" style="width:100%;height:400px;"></div>
            			<div id="infowindow-content">
						  <span id="place-address"></span>
						</div>
            		</div>
            	</div>         		      
		        <br/>
		        <div id="ErrorMsgAddress" class="alert alert-danger" style=""></div>		        
            	<div class="row">
            		<div class="form-group">
            			<div class="col-sm-6">
							<input type="text" autocomplete="off"  class="form-control" name="TextBoxStreetName" id="TextBoxStreetName" name="StreetName" placeholder="Οδός"  value="<?php echo $fStreet;?>" />
						</div>
						
	            		<div class="col-sm-2">
	            			<input type="text" autocomplete="off"  class="form-control" name="TextBoxStreetNumber" id="TextBoxStreetNumber" placeholder="Αριθμός"  value="<?php echo $fStreetNo;?>" />
	            		</div>	

	            		<div class="col-sm-2">
	            			<input type="text" autocomplete="off"  class="form-control" name="TextBoxArea" id="TextBoxArea" placeholder="Περιοχή"  value="<?php echo $fArea;?>" />
	            		</div>	
	            		<div class="col-sm-2">
	            			<input type="text" autocomplete="off"  class="form-control" name="TextBoxZipCode" id="TextBoxZipCode" placeholder="Τ.Κ." value="<?php echo $fZip;?>"  />
	            		</div>
            		</div>
            	</div> 
            	<div class="row">
            		<div class="col-sm-6">&nbsp;</div>
            		<div class="col-sm-6">
	            		<div class="checkbox">
						  <label><input type="checkbox" name="NoNumbersAddress" <?php if(isset($_POST['NoNumbersAddress'])){?> checked="checked"<?php }?> id="NoNumbersAddress" onclick="NoNumbersAddressClick();" value="">Δεν υπάρχει αρίθμηση στην διεύθυνση μου</label>
						</div>
            		</div>
            	</div>
            	<br/>
            	<div class="row">
            		<div class="col-sm-6">
            		<select class="form-control" placeholder="Δήμος" name="ComboBoxMunicipality" id="ComboBoxMunicipality"  onchange="GetCountyFromMunicipality();">
						<option value="0" disabled selected>Δήμος</option>
						
						<?php
							$sql = "SELECT `KOD_DHM`,`ONOMA` FROM `YPES_DHMOI` ORDER BY `ONOMA`";
							$command = new MySqlCommand($connection,$sql);
							$reader = $command->ExecuteReader();
							while($reader->Read())
							{
								$selected = "";
								if($fMunicipalityID == $reader->getValue(0))
								{
									$selected ='selected="selected"';
								}
						?>
							<option <?php echo $selected;?> value="<?php echo $reader->getValue(0);?>"><?php echo $reader->getValue(1);?></option>
						<?php
							}
							$reader->Close();
						?>
					</select>      		
            		</div>	
            		<div class="col-sm-6"><input type="text" class="form-control" name="TextBoxDivision" id="TextBoxDivision" placeholder="Νομός / Τομέας" readonly="readonly" style="background-color:#fffdf3;" value="<?php echo $fCounty;?>" /></div>
		        </div>
                <br/><br/>
                <br/><br/>
			    <div class="row">
			    	<div class="col-sm-6"><button type="button" class="btn btn-danger btn-block" onclick="ResetData();return false;">Επιστροφή στην αρχική</button></div>
			    	<div class="col-sm-6"><button  type="submit" class="btn btn-success btn-block" onclick="submitData();return false;">Επόμενο βήμα</button></div>
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

<!--------------------------------------------------------------- EMAIL VALIDATION MODAL --------------------------------------------------------------->
  <div class="modal fade" id="EmailModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" align="center">Επαλήθευση Email</h4>
        </div>
        <div class="modal-body">
          <table style="height:100%!important;width:100%!important;border:0">
            <tbody>
              <tr style="height:20px"></tr>
              <tr>
                <td align="center">
                  <p id="SendEmailMsg">Θα σας αποσταλεί κωδικός επαλήθευσης στο email σας.<br /><br />
                  Παρακαλούμε πατήστε «<strong>Αποστολή Κωδικού στο email μου</strong>»<br/>
                  και στην συνέχεια εισάγετε τον κωδικό που θα παραλάβατε.</p>
                  <div id="SendEmailDiv">
                  	Εισάγετε τον κωδικό επαλήθευσης που λάβατε στο<br/>
                  	email σας και πατήστε το κουμπί «<strong>Επαλήθευση</strong>».
                  	<br/><br/>
                  	<input type="text" maxlength="4" id="TextBoxEMailPin" name="TextBoxEMailPin" style="text-align:center;font-size:18px" />
                  	<br/><br/>
                  </div>
                  <div id="ErrorMsgEmail" class="alert alert-danger"></div>
                </td>
              </tr>
              <tr style="height:10px"></tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info" onclick="SendEMailVerificationCode();" id="ButtonSendEmailVerification">Αποστολή κωδικού στο email μου</button>
          <button type="button" class="btn btn-default" onclick="CancelEmailVerification();"  id="ButtonSendEmailVerificationCancel">Κλείσιμο</button>
          <button type="button" class="btn btn-success" onclick="VerifyEmailFromPin();" id="BtnCheckEmailCode">Επαλήθευση</button>
        </div>
      </div>
    </div>
  </div>
<!-------------------------------------------------------------------------------------------------------------------------------------------->


<!--------------------------------------------------------------- MOBILE VALIDATION MODAL --------------------------------------------------------------->
  <div class="modal fade" id="MobileModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" align="center">Επαλήθευση κινητού τηλεφώνου</h4>
        </div>
        <div class="modal-body">
          <table style="height:100%!important;width:100%!important;border:0">
            <tbody>
              <tr style="height:20px"></tr>
              <tr>
                <td align="center">
                  <p id="SendMobileMsg">Θα λάβετε κωδικό επαλήθευσης στο κινητό σας.<br /><br />
                  Παρακαλούμε πατήστε «<strong>Επαλήθευση μέσω SMS</strong>»<br/>
                  και στην συνέχεια εισάγετε τον κωδικό που θα παραλάβατε.</p>
                  <div id="SendMobileDiv">
                  	Εισάγετε τον κωδικό επαλήθευσης που λάβατε στο<br/>
                  	κινητό σας και πατήστε το κουμπί «<strong>Επαλήθευση</strong>».
                  	<br/><br/>
                  	<input type="text" maxlength="4" id="TextBoxMobilePin" style="text-align:center;font-size:18px" />
                  	<br/><br/>
                  </div>
                  <div id="ErrorMsgMobile" class="alert alert-danger"></div>
                </td>
              </tr>
              <tr style="height:10px"></tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
		  <button type="button" class="btn btn-warning"  id="ButtonSendVoiceVerificationPin" style="visibility:collapse" onclick="SendMobileVerificationVoiceMessage();">Επαλήθευση μέσω φωνητικής κλήσης</button>       
   		  <button type="button" class="btn btn-info" id="ButtonSendSMSVerificationPin" onclick="SendMobileSMSVerificationPin();" >Επαλήθευση μέσω SMS</button>
          <button type="button" class="btn btn-default" id="ButtonVerifyMobileCancel" onclick="CancelMobileVerification();">Κλείσιμο</button>
          <button type="button" class="btn btn-success" onclick="VerifyMobileFromPin();" id="ButtonCheckMobilePin">Επαλήθευση</button>
        </div>
      </div>
    </div>
  </div>
<!-------------------------------------------------------------------------------------------------------------------------------------------->
</body>
</html>