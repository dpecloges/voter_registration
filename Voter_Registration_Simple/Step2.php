<?php
	session_start();
	require_once("Classes/DBConnection.php"); 
	
	
	if(!isset($_GET['UniqueKey']))
	{
		header('refresh: 0; url=index.php');
		exit;
	}
	
	$fUniqueKey = $_GET['UniqueKey'];
	$fCountryISO = "GR"; // Default for greece
	
	
	$fErrorCode = 0;
	$fErrorDescription = "";
	
	
	function IsMailValid($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	if(isset($_POST['TextBoxEmail']))
	{
		
	
		$fEmail = $_POST['TextBoxEmail'];
		$fMobilePhone = $_POST['TextBoxMobile'];
		$fPhone = $_POST['TextBoxTelephone'];
		
		$fCountryISO = trim($_POST['ComboBoxCountry']);
		$fRegionCode = trim($_POST['ComboBoxRegion']);
		$fMunicipality = trim($_POST['ComboBoxMunicipality']);

		$fStreet = trim($_POST['TextBoxStreetName']);
		$fStreetNo = trim($_POST['TextBoxStreetNumber']);
		$fArea = trim($_POST['TextBoxArea']);
		$fZip = trim($_POST['TextBoxZipCode']);
		$fEMail = trim($_POST['TextBoxEmail']);
		$fProfessionID = intval($_POST['ComboBoxProfession']);
		
		$fInvolveTypesArray = $_POST['CheckboxInvolveType'];
		$fInvolveProposal = $_POST['TextBoxInvloveProposal'];
		
		
		
		// Check if address is valid
		if(!isset($_POST['AddressChange']))
		{
			if($fCountryISO=="GR")
			{
				$addressIsValid = true;
				
				if($fRegionCode=="")
				{
					$fRegionError = "Επιλέξτε νομό!";
					$addressIsValid = false;
				}
				
				if($fMunicipality=="")
				{
					$fMunicipalityError = 'Επιλέξτε Δήμο';
					$addressIsValid = false;
				}
				
				if($fStreet =="")
				{
					$fStreetError = 'Δέν έχετε εισάγει Οδό';
					$addressIsValid = false;
				}
				
				if($fStreetNo =="" && !isset($_POST['NoNumbersAddress']))
				{
					$fStreetNoError= 'Δέν έχετε εισάγει Αριθμό';
					$addressIsValid = false;
				}
				
				if($fZip =="")
				{
					$fZipError = 'Ο Τ.Κ είναι απαραίτητος!';
					$addressIsValid = false;
				}	
				else if(strlen($fZip)<=4)
				{
					$fZipError = 'Λάθος Τ.Κ!';
					$addressIsValid = false;
				}
			}
			else
			{
				$addressIsValid = true;

				if($fStreet =="")
				{
					$fStreetError = 'Δέν έχετε εισάγει Οδό';
					$addressIsValid = false;
				}
				
				if($fStreetNo =="" && !isset($_POST['NoNumbersAddress']))
				{
					$fStreetNoError= 'Δέν έχετε εισάγει Αριθμό';
					$addressIsValid = false;
				}
				
				if($fZip =="")
				{
					$fZipError = 'Ο Τ.Κ είναι απαραίτητος!';
					$addressIsValid = false;
				}	
				else if(strlen($fZip)<=4)
				{
					$fZipError = 'Λάθος Τ.Κ!';
					$addressIsValid = false;

				}
				
				
				if($fArea =="")
				{
					$fAreaError= 'Δώστε όνομα πόλης ή περιοχής';
					$addressIsValid = false;
				}	
			}
		}
		else
		{
			$addressIsValid = true;
		}
		
		// Address is not valid message	
		if(!$addressIsValid)
		{
			$fErrorCode = 101;
			$fErrorDescription = 'Ελέξτε την διευθυνση σας!';
		}
		
		
		$sql = "UPDATE `voter_registration_temp` SET `Phone`=?,`CountryISO`=?,`RegionCode`=?,`Municipality`=?,`Street`=?,`StreetNo`=?,`Area`=?,`Zip`=?,`EMail`=?,`ProfessionID`=?,`InvolveTypes`=?,`InvolveProposal`=?  WHERE `UniqueKey`=?";
		$command = new MySQLCommand($connection, $sql);
		$command->Parameters->setString(1,$fPhone);
		$command->Parameters->setString(2,$fCountryISO);
		$command->Parameters->setString(3,$fRegionCode);
		$command->Parameters->setString(4,$fMunicipality);		
		$command->Parameters->setString(5,$fStreet);
		$command->Parameters->setString(6,$fStreetNo);
		$command->Parameters->setString(7,$fArea);
		$command->Parameters->setString(8,$fZip);
		$command->Parameters->setString(9,$fEMail);
		$command->Parameters->setInteger(10,$fProfessionID);
		
		$command->Parameters->setString(11,implode(",",$fInvolveTypesArray));
		$command->Parameters->setString(12,$fInvolveProposal);
		
		$command->Parameters->setString(13,$fUniqueKey);
		$command->ExecuteQuery();
		
		
		if(!isset($_POST['AddressChange']))
		{
			$sql = "SELECT `MobileIsVerified`,`EMail` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
			$command = new MySQLCommand($connection, $sql);
			$command->Parameters->setString(1,$fUniqueKey);
			$reader = $command->ExecuteReader();
			if($reader->Read())
			{
				$mobileIsVerified = $reader->getValue(0); 
				
				$emailIsVerified = IsMailValid($reader->getValue(1));		
				
				if ($mobileIsVerified ==1 && $emailIsVerified  && $addressIsValid)
				{
					header('refresh: 0; url=Step3.php?UniqueKey='.$fUniqueKey);
					exit;
				}
				else
				{			
					if ($mobileIsVerified!=1)
					{
						$fMobileError = "Το πεδίο κινητό τηλέφωνο είναι υποχρεωτικό!";
					}
					
					if ($emailIsVerified == false)
					{
						$fEMailError = "Το πεδίο Email είναι υποχρεωτικό!";
					}
				}	
			}
			$reader->Close();
		}
		
	}
	
	
	// Get User Data from db
	$tempRecordFound = false;
	$sql = "SELECT `EMail`,`MobilePhone`,`MobileIsVerified`,`Phone`,`CountryISO`,`RegionCode`,`Municipality`,`Street`,`StreetNo`,`Area`,`Zip`,`InvolveTypes`,`InvolveProposal` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
	$command = new MySQLCommand($connection, $sql);
	$command->Parameters->setString(1,$fUniqueKey);
	$reader = $command->ExecuteReader();
	if($reader->Read())
	{
		$tempRecordFound = true;
		$fEmail = $reader->getValue(0)!=""? $reader->getValue(0) : $fEmail;
		
		$fMobilePhone = $reader->getValue(1) !=""? $reader->getValue(1) : $fMobilePhone ;
		$fMobileIsVerified = $reader->getValue(2);
		$fVerifyMobileButtonLabel = ($fMobileIsVerified ==1) ? "Επαληθέυτηκε" :  'Επαλήθευση κινητού τηλεφώνου';
		
		$fPhone = $reader->getValue(3);
		
		$fCountryISO = ($reader->getValue(4) == null || "") ? "GR"  :$reader->getValue(4);
		$fRegionCode = $reader->getValue(5);
		$fMunicipality = $reader->getValue(6);
		$fStreet = $reader->getValue(7);
		$fStreetNo = $reader->getValue(8);
		$fArea = $reader->getValue(9);
		$fZip = $reader->getValue(10);
		
		$fInvolveTypesArray = explode(",", $reader->getValue(11));
		$fInvolveProposal = $reader->getValue(12);
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
    <script type="text/javascript" src="js/StepFormsJS/Step2_GoogleMap.js?ID=<?php echo time();?>"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo My_Google_API_Key;?>&libraries=places&language=el&callback=GoogleMapLoaded"></script>
    
    <?php include ("lib/config/analytics/php");?>

   
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
			NoNumbersAddressClick();
			
			$('#TextBoxStreetName')[0].oninput = function(){
				//FixTextInputToUpperCaseGreekWithSpaces($('#TextBoxStreetName'));
				ShowAddressOnMapAccordingToUserInput();
	     	};
	     	
	     	$('#TextBoxStreetNumber')[0].oninput = function(){
				//FixTextInputToNumbersOnly($('#TextBoxStreetNumber'));
				ShowAddressOnMapAccordingToUserInput();
	     	};
	     	
	     	$('#TextBoxZipCode')[0].oninput = function(){
				//FixTextInputToNumbersOnly($('#TextBoxZipCode'));
				ShowAddressOnMapAccordingToUserInput();
	     	};

	    	$('#TextBoxArea')[0].oninput = function(){
				//FixTextInputToUpperCaseGreekWithSpaces($('#TextBoxArea'));
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
		
			var country = $("#ComboBoxCountry :selected").text();

			if($("#NoNumbersAddress").is(':checked'))
			{
				if($('#TextBoxStreetName').val()!="" &&  $('#TextBoxZipCode').val()!="" && $('#TextBoxZipCode').val().length>4)
				{
					v//ar address = $('#TextBoxStreetName').val()+ ' ' + $('#TextBoxStreetNumber').val() +"," +  $('#TextBoxZipCode').val() +"," + country ;
					var address =   $('#TextBoxZipCode').val() +"," +country;
					ShowAddressOnGoogleMap(address,false);
				}
			}
			else
			{
				if($('#TextBoxStreetName').val()!="" && $('#TextBoxStreetNumber').val() !="" &&  $('#TextBoxZipCode').val()!="" && $('#TextBoxZipCode').val().length>4)
				{
					//var address = $('#TextBoxStreetName').val()+ ' ' + $('#TextBoxStreetNumber').val() +"," +  $('#TextBoxZipCode').val() +"," +country;
					var address =   $('#TextBoxZipCode').val() +"," +country;
					ShowAddressOnGoogleMap(address,false);
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
		
		function CountryChanged()
		{
			var countryISO = $('#ComboBoxCountry').val();
			SubmitFormOnAddressChange();
		}
		
		function RegionChanged()
		{
			var region = $('#ComboBoxRegion').val();
			SubmitFormOnAddressChange();
		}
		
	
		
		function SubmitFormOnAddressChange()
		{
			$('#PleaseWaitDialog').modal('show');

     		$('#RegistrationForm').append("<input type='hidden' name='AddressChange' value='1' />");
     		$('#RegistrationForm').submit();
     		return true;
		}
		
		
		function MunicipalityChanged()
		{
			$('#municipalityError').hide();
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
        	
            <form method="post" action="" id="RegistrationForm" <?php if(1==1){?>style="visibility:hidden;"<?php }?>>
            	<div class="row">
            		<div class="col-sm-6">
            			<br/><br/>   
		                <div class="form-group">
		                    <input type="text" class="form-control"  placeholder="* Email" name="TextBoxEmail" id="TextBoxEmail" value="<?php echo $fEmail;?>" maxlength="50" />
		                    <small class="help-block" style="color:red;" id="emailError"><?php echo $fEMailError;?></small>
		                </div>
		                <br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" class="form-control" placeholder="* Τηλέφωνο κινητό" name="TextBoxMobile" id="TextBoxMobile" maxlength="11" value="<?php echo $fMobilePhone;?>" />
		                	<small class="help-block" style="color:red;" id="mobileError"><?php echo $fMobileError;?></small>
		                </div>
						<button type="button" class="btn btn-default" <?php if($fMobileIsVerified ==1){?>disabled="disabled"<?php }?> id="ButtonCheckMobile" onclick="OpeMobileVerificationForm();"><?php echo $fVerifyMobileButtonLabel;?></button>
						<br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" style="visibility:collapse;" class="form-control" placeholder="Τηλέφωνο σταθερό" name="TextBoxTelephone" id="TextBoxTelephone" maxlength="18" value="<?php echo $fPhone;?>" />
		                </div>     
		
       					 <div class="form-group">
		                    <select class="form-control" placeholder="Επάγγελμα" name="ComboBoxProfession" id="ComboBoxProfession" style="visibility:hidden">
								<option value="0" disabled selected>Επάγγελμα</option>
								<?php
									$sql = "SELECT `ID`,`Title` FROM `voter_registration_professions` ORDER BY `Title`";
									$command = new MySqlCommand($connection,$sql);
									$reader = $command->ExecuteReader();
									while($reader->Read())
									{
										$selected = "";
										if($fProfessionID == $reader->getValue(0))
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
			        <div class="col-sm-3">
						<select class="form-control" placeholder="Χώρα" name="ComboBoxCountry" id="ComboBoxCountry" onchange="CountryChanged();" >
							<option <?php if($fSelectedCountryISO =="GR" ){ ?> selected="selected" <?php }?> value="GR">Ελλάδα</option>
							<?php
								$sql = "SELECT `iso`,`country` FROM `GeoPC_Countries` WHERE `iso`!='GR' ORDER BY `country`";
								$command = new MySqlCommand($connection,$sql);
								$reader = $command->ExecuteReader();
								while($reader->Read())
								{
									$selected = "";
									if($fCountryISO == $reader->getValue(0))
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
					<div class="col-sm-3">
						<select class="form-control" placeholder="Νομός" name="ComboBoxRegion" id="ComboBoxRegion" onchange="RegionChanged();">
						<option value="0" disabled selected>Νομός</option>
						<?php
							if($fCountryISO=="GR")
							{
								$sql = "SELECT DISTINCT(`NOMOS`) FROM `YPES_DHMOI` ORDER BY `Nomos`";
								$command = new MySqlCommand($connection,$sql);
								$reader = $command->ExecuteReader();
								while($reader->Read())
								{
									$selected = "";
									if($fRegionCode == $reader->getValue(0))
									{
										$selected ='selected="selected"';
									}
							?>
								<option <?php echo $selected;?> value="<?php echo $reader->getValue(0);?>"><?php echo $reader->getValue(0);?></option>
							<?php
								}
								$reader->Close();
							}
							else
							{
								$sql = 'SELECT DISTINCT `name`,`code` FROM `GeoPC_ISO3166-2` WHERE `iso`=? ORDER BY `name`';
								$command = new MySqlCommand($connection,$sql);
								$command->Parameters->setString(1,$fCountryISO);
								$reader = $command->ExecuteReader();
								while($reader->Read())
								{
									$selected = "";
									if($fRegionCode == $reader->getValue(1))
									{
										$selected ='selected="selected"';
									}
							?>
								<option <?php echo $selected;?> value="<?php echo $reader->getValue(1);?>"><?php echo $reader->getValue(0);?></option>
							<?php
								}
								$reader->Close();
							}
						?>
						</select>   
						<?php
							if($fRegionError!="")
							{
						?>
						<small class="help-block" style="color:red;"><?php echo $fRegionError;?></small>
						<?php
							}
						?>
					</div>
					
					<?php
						if($fCountryISO=="GR" && $fRegionCode!="")
						{
					?>
					<div class="col-sm-6">
	            		<select class="form-control" placeholder="Δήμος" name="ComboBoxMunicipality" id="ComboBoxMunicipality" onchange="MunicipalityChanged();">
							<option value="0" disabled selected>Δήμος</option>
							<?php
								
								$sql = "SELECT `ONOMA` FROM `YPES_DHMOTIKES_ENOTITES` WHERE `NOMOS`=? ORDER BY `ONOMA`";
								$command = new MySqlCommand($connection,$sql);
								$command->Parameters->setString(1,$fRegionCode);
								$reader = $command->ExecuteReader();
								while($reader->Read())
								{
									$selected = "";
									if($fMunicipality== $reader->getValue(0))
									{
										$selected ='selected="selected"';
									}
							?>
								<option <?php echo $selected;?>  value="<?php echo $reader->getValue(0);?>"><?php echo $reader->getValue(0);?></option>
							<?php
								}
								$reader->Close();
							?>
						</select>      		
						<?php
							if($fMunicipalityError!="")
							{
						?>
						<small class="help-block" id="municipalityError" style="color:red;"><?php echo $fMunicipalityError;?></small>
						<?php
							}
						?>
            		</div>
            		<?php
						}          		
					?>	
				</div>
            	<br><br>
            	
            	<div class="row">
            		
            		<div class="form-group">
            			<div class="col-sm-6">
							<input type="text" autocomplete="off"  class="form-control" name="TextBoxStreetName" id="TextBoxStreetName" name="StreetName" placeholder="Οδός"  value="<?php echo $fStreet;?>" />
							<?php
								if($fStreetError!="")
								{
								?>
									<small class="help-block" style="color:red;"><?php echo $fStreetError;?></small>
								<?php
								}
							?>
						</div>
						
	            		<div class="col-sm-2">
	            			<input type="text" autocomplete="off"  class="form-control" name="TextBoxStreetNumber" id="TextBoxStreetNumber" placeholder="Αριθμός"  value="<?php echo $fStreetNo;?>" />
	            			<?php
								if($fStreetNoError!="")
								{
								?>
									<small class="help-block" style="color:red;"><?php echo $fStreetNoError;?></small>
								<?php
								}
							?>

	            		</div>	

	            		<div class="col-sm-2">
	            			<input type="text" autocomplete="off"  class="form-control" name="TextBoxArea" id="TextBoxArea" placeholder="Περιοχή"  value="<?php echo $fArea;?>" />
	            			<?php
								if($fAreaError!="")
								{
								?>
									<small class="help-block" style="color:red;"><?php echo $fAreaError;?></small>
								<?php
								}
							?>
	            		</div>	
	            		<div class="col-sm-2">
	            			<input type="text" autocomplete="off"  class="form-control" name="TextBoxZipCode" id="TextBoxZipCode" placeholder="Τ.Κ." value="<?php echo $fZip;?>"  />
	            			<?php
								if($fZipError!="")
								{
								?>
									<small class="help-block" style="color:red;"><?php echo $fZipError;?></small>
								<?php
								}
							?>

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
            	<div class="col-sm-12">
					<br>
    				<b>Θέλω να συμβάλλω στο νέο φορέα:</b>
    				<br>
    				<?php
    					$sql = "SELECT `ID`,`Title` FROM `voter_registration_member_involve_fields`";
    					$command = new MySqlCommand($connection,$sql);
    					$reader = $command->ExecuteReader();
    					while($reader->Read())
    					{
    						$id = $reader->getValue(0);
    						$title = $reader->getValue(1);
    				?>
    				<div><input name="CheckboxInvolveType[]" <?php if(in_array($id,$fInvolveTypesArray)) { ?> checked="checked"<?php } ?> id="CheckboxInvolveType<?php echo $id;?>" value="<?php echo $id;?>" type="checkbox" /> <?php echo $title?></div>
    				<?php
    					}
    					$reader->Close();
    				?>
    			</div>
				<br/>
				<br/>
				<br>&nbsp;
				<div class="col-sm-9">
					<br/>
					<textarea name="TextBoxInvloveProposal" placeholder="Αν θέλετε περιγράψτε τον τρόπο συμβολής σας" style="width: 538px; height: 57px;"><?php echo $fInvolveProposal;?></textarea></div>
					<br/>&nbsp;<br>
                <br/><br/>
                &nbsp;
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
 <div class="modal fade" id="PleaseWaitDialog" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" align="center">Παρακαλώ περιμένετε</h4>
        </div>
        <div class="modal-body">
         Παρακαλώ περιμένετε όσο επεξεργαζόμαστε τις παραμέτρους της διευθυνσης σας.
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>
</body>
</html>