<?php
	session_start();
	require_once("Classes/DBConnection.php"); 
	
	require_once("Classes/GoogleReCaptcha.php"); 
	$fGoogleReCaptcha = new GoogleReCaptcha();
	
	if(!isset($_GET['UniqueKey']))
	{
		header('refresh: 0; url=index.php');
		exit;
	}
	
	$fUniqueKey = $_GET['UniqueKey'];
	
	if(isset($_POST['TextBoxEmail']))
	{
		$fEmail = $_POST['TextBoxEmail'];
		$fMobilePhone = $_POST['TextBoxMobile'];
		$fPhone = $_POST['TextBoxTelephone'];
	
		$sql = "SELECT `EMailIsVerified`,`MobileIsVerified` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
		$command = new MySQLCommand($connection, $sql);
		$command->Parameters->setString(1,$fUniqueKey);
		$reader = $command->ExecuteReader();
		if($reader->Read())
		{
			$emailIsVerified = $reader->getValue(0);
			$mobileIsVerified = $reader->getValue(1); 
			
			if ($emailIsVerified==1 && $mobileIsVerified ==1)
			{
				header('refresh: 0; url=Step3.php');
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
		
		$sql = "UPDATE `voter_registration_temp` SET `Phone`=?  WHERE `UniqueKey`=?";
		$command = new MySQLCommand($connection, $sql);
		$command->Parameters->setString(1,$fPhone);
		$command->Parameters->setString(2,$fUniqueKey);
		$command->ExecuteQuery();

		
		
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Delete voter_registration_temp older than 2 hours
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$dateTimeNow = time();
 	$dateTimeFrom = date($dateTimeNow - (2 * 3600));
 	$sql = "DELETE FROM `voter_registration_temp` WHERE UNIX_TIMESTAMP(`DateTime`) < ".$dateTimeFrom;
 	$command = new MySqlCommand($connection, $sql);
 	$command->ExecuteQuery();
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	// Get User Data from db
	$tempRecordFound = false;
	$sql = "SELECT `EMail`,`EMailIsVerified`,`MobilePhone`,`MobileIsVerified`,`Phone` FROM `voter_registration_temp` WHERE `UniqueKey`=? LIMIT 0,1";
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
	}
	$reader->Close();
	
	if(!$tempRecordFound)
	{
		header('refresh: 0; url=Step1.php');
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
        
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAHTjDSX9qbea-E-zxAWDQgJpi9hw-RvwU&libraries=places&language=el&callback=InitializeAddressAutoComplete"></script>
    
	<script type="text/javascript">
		function InitializeAddressAutoComplete(){
		  	Autocomplete = new google.maps.places.Autocomplete($("#TextBoxAddress")[0], {});
		    map = new google.maps.Map(document.getElementById('map'), {
		      zoom: 17,
		      center: {lat: 37.9752816, lng: 23.736729}
		    });
			Autocomplete.bindTo('bounds', map);
			var infowindow = new google.maps.InfoWindow();
			var infowindowContent = document.getElementById('infowindow-content');
			infowindow.setContent(infowindowContent);
			var marker = new google.maps.Marker({
				map: map,
				anchorPoint: new google.maps.Point(0, -29)
			});
			infowindowG = infowindow;
			markerG = marker;	
			Autocomplete.addListener('place_changed', function() {
				infowindow.close();
				marker.setVisible(false);
				var place = Autocomplete.getPlace();		
				if (!place.geometry) return;
				
			 	for (var i = 0; i < place.address_components.length; i++) 
			 	{
			      for (var j = 0; j < place.address_components[i].types.length; j++) 
			      {
			        if (place.address_components[i].types[j] == "route") 
			        {	          
			          	myAddress.StreetName = place.address_components[i].long_name;
						$("#TextBoxStreetName").val(myAddress.StreetName);
			        }
			        if (place.address_components[i].types[j] == "street_number") 
			        {	          
			          	myAddress.StreetNumber = place.address_components[i].long_name;
						$("#TextBoxStreetNumber").val(myAddress.StreetNumber);
			        }
			        if (place.address_components[i].types[j] == "postal_code") 
			        {	          
			          	myAddress.Zip = place.address_components[i].long_name;
						$("#TextBoxZipCode").val(myAddress.Zip);
			        }
			        
			        if (place.address_components[i].types[j] == "locality" || place.address_components[i].types[j] == "administrative_area_level_5")
			        {	          
			          	myAddress.Municipality = place.address_components[i].long_name;
						$("#TextBoxMunicipality").val(myAddress.Municipality);
			        }
			        if (place.address_components[i].types[j] == "administrative_area_level_3" ||place.address_components[i].types[j] == "administrative_area_level_4") 
			        {	          
			          	myAddress.Division = place.address_components[i].long_name;
						$("#TextBoxDivision").val(myAddress.Division);
			        }
			        if (place.address_components[i].types[j] == "country") 
			        {
			          	myAddress.Country = place.address_components[i].long_name;
			        }
			        else if (place.address_components[i].types[j] == "locality")
			        {	          
			          	myAddress.Locality = place.address_components[i].long_name;
			        }
			      }
			    }
			    
				if (place.geometry.viewport) 
				{
					map.fitBounds(place.geometry.viewport);
				} 
				else 
				{
					map.setCenter(place.geometry.location);
					map.setZoom(17);  // Why 17? Because it looks good.
				}
				marker.setPosition(place.geometry.location);
				marker.setVisible(true);
				var address = '';
				if (place.address_components) 
				{
					address = myAddress.StreetName + ' ' + myAddress.StreetNumber + ', ' + myAddress.Municipality;
					if(myAddress.StreetName=='') address = myAddress.Municipality;
				}
				infowindowContent.children['place-address'].textContent = address;
				infowindow.open(map, marker);
				AddressValidation();			
			});
		}
	
		function OpenEmailVerificationForm()
		{
			$('#SendEmailMsg').show();
			$('#ButtonSendEmailVerification').show();	
			$("#ButtonSendEmailVerification").attr("disabled", false);
			$('#EmailModal').modal('show');	
			$('#ErrorMsgEmail').hide();
			$('#SendEmailDiv').hide();
			$('#BtnCheckEmailCode').hide();
		}	
				
		function SendEMailVerificationCode()
		{
			$("#ButtonSendEmailVerification").attr("disabled", true);		
			$.post("AJAX_SendEmailVerificationCode.php", { Email: $("#TextBoxEmail").val(), UniqueKey: '<?php echo $fUniqueKey;?>' })
		  	.done(function(data) {	  
		  		alert(data); 	 	
		   	 	$('#SendEmailMsg').hide();
				$('#ButtonSendEmailVerification').hide();		
				$('#SendEmailDiv').show();
				$('#BtnCheckEmailCode').show();
				$('#TextBoxEMailPin').focus();
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
					$("#ButtonCheckEmail").attr("disabled", true);
		   	 	}
		   	 	else
		   	 	{
		   	 		$("#ErrorMsgEmail").html(data.ErrorDescr);
					$('#ErrorMsgEmail').show();
					$('#MobilePIN').focus();
		   	 	}
			});	
		}
		
		function CancelEmailVerification()
		{
			$('#EmailModal').modal('hide');	
		}
		
		
		function OpeMobileVerificationForm()
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
		
		
		function SendMobileSMSVerificationPin()
		{
			$.post("AJAX_SendMobileSMSVerificationCode.php", { Mobile: $("#TextBoxMobile").val(), UniqueKey: '<?php echo $fUniqueKey;?>' })
		  	.done(function(data) {	   	 	
		   	 	$('#ErrorMsgMobile').hide()
				$('#SendMobileMsg').hide();
				$('#SendMobileDiv').show();
				$('#ButtonCheckMobilePin').show();
				$('#MobileModal').modal('show');
				$('#ButtonSendSMSVerificationPin').hide();
				$('#ButtonSendVoiceVerificationPin').hide();
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
        
            <form method="post" action="" id="RegistrationForm">
            	<div class="row">
            		<div class="col-sm-6">
            			<br/><br/>   
		                <div class="form-group">
		                    <input type="email" class="form-control" placeholder="* Email" name="TextBoxEmail" id="TextBoxEmail" value="<?php echo $fEmail;?>" maxlength="50" />
		                	<small class="help-block" style=" color:red;"><?php echo $fEMailError;?></small>
		                </div>
		                
		                <button type="button" class="btn btn-default" <?php if($fEMailIsVerified ==1){?>disabled="disabled"<?php }?> id="ButtonCheckEmail" onclick="OpenEmailVerificationForm();" ><?php echo $fVerifyEmailButtonLabel;?></button>
		                <br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" class="form-control" placeholder="* Τηλέφωνο κινητό" name="TextBoxMobile" id="TextBoxMobile" maxlength="10" value="<?php echo $fMobilePhone;?>" />
		                	<small class="help-block" style=" color:red;"><?php echo $fMobileError;?></small>
		                </div>
						<button type="button" class="btn btn-default" <?php if($fMobileIsVerified ==1){?>disabled="disabled"<?php }?> id="ButtonCheckMobile" onclick="OpeMobileVerificationForm();"><?php echo $fVerifyMobileButtonLabel;?></button>
						<br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" class="form-control" placeholder="Τηλέφωνο σταθερό" name="TextBoxTelephone" id="TextBoxTelephone" maxlength="10" value="<?php echo $fPhone;?>" />
		                </div>            			
            		</div>
            		<div class="col-sm-6">
            			<div id="map" style="width:100%;height:400px;"></div>
            			<div id="infowindow-content">
						  <span id="place-address"></span>
						</div>
            			
            		</div>
            	</div>         	
            	<br/><br/>            
		        <input type="text" class="form-control" placeholder="* Διεύθυνση κατοικίας" id="TextBoxAddress" name="TextBoxAddress" maxlength="100" />
		        <div class="row">		        	
		        	<div class="col-sm-6">
		        		<div class="checkbox">
				  			<label><input type="checkbox" id="CustomAddress" onclick="CustomAddressClick()" value="">Θέλω να εισάγω μόνος μου την διεύθυνση. (Απαιτείται να γνωρίζετε τον ΤΚ)</label>
		        		</div>
					</div>
		        	<div class="col-sm-6">
				        <div class="checkbox">
						  <label><input type="checkbox" id="NoNumbersAddress" onclick="NoNumbersAddressClick()" value="">Δεν υπάρχει αρίθμηση στην διεύθυνση μου</label>
						</div>
				    </div>
		        </div>
		        <br/>
		        <div id="ErrorMsgAddress" class="alert alert-danger"></div>		        
            	<div class="row">
            		<div class="form-group">
            			<div class="col-sm-8"><input type="text" disabled="" class="form-control" id="TextBoxStreetName" name="StreetName" placeholder="Οδός" style="background-color:#fffdf3" /></div>
	            		<div class="col-sm-2"><input type="text" disabled="" class="form-control" id="TextBoxStreetNumber" placeholder="Αριθμός" style="background-color:#fffdf3" /></div>	
	            		<div class="col-sm-2"><input type="text" disabled="" class="form-control" id="TextBoxZipCode" placeholder="Τ.Κ." style="background-color:#fffdf3" /></div>
            		</div>
            	</div> 
            	<br/>
            	<div class="row">
            		<div class="col-sm-6"><input type="text" disabled="" class="form-control" id="TextBoxMunicipality" placeholder="Δήμος" style="background-color:#fffdf3" /></div>	
            		<div class="col-sm-6"><input type="text" disabled="" class="form-control" id="TextBoxDivision" placeholder="Νομός / Τομέας" style="background-color:#fffdf3" /></div>
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
                  ή «<strong>Επαλήθευση μέσω φωνητικής κλήσης</strong>»<br/>
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
          <button type="button" class="btn btn-info" id="ButtonSendSMSVerificationPin" onclick="SendMobileSMSVerificationPin();" >Επαλήθευση μέσω SMS</button>
          <button type="button" class="btn btn-warning" id="ButtonSendVoiceVerificationPin" onclick="SendMobileVerificationVoiceMessage();">Επαλήθευση μέσω φωνητικής κλήσης</button>
          <button type="button" class="btn btn-default" id="ButtonVerifyMobileCancel" onclick="CancelMobileVerification();">Κλείσιμο</button>
          <button type="button" class="btn btn-success" onclick="VerifyMobileFromPin();" id="ButtonCheckMobilePin">Επαλήθευση</button>
        </div>
      </div>
    </div>
  </div>
<!-------------------------------------------------------------------------------------------------------------------------------------------->
</body>
</html>