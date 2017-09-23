<?php
	header('Cache-Control: no-cache, must-revalidate');
	require("lib/lib.php");

	if($_SERVER['HTTP_REFERER']!='http://dpekloges.gr/apps/vreg/p1.php'){
		//die('<h2>System Error!</h2>');
	}

	session_start();
	if($_SESSION['RegistrationSID'] != $_GET['SID']){
		 //die('<h2>System Error!</h2>');	
	}

	$_SESSION['Email_PIN_Validated'] = FALSE;
	$_SESSION['Mobile_PIN_Validated'] = FALSE;


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
    	.register-photo {
			padding: 0px!important;
		}
        #RegistrationForm .form-control-feedback {
            pointer-events: auto;
        }
        #RegistrationForm .form-control-feedback:hover {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="register-photo" style="background-color:white!important;">
        <div class="form-container">
        
            <form onsubmit="return false;" id="RegistrationForm">
            	<div class="row">
            		<div class="col-sm-6">
            			<br/><br/>   
		                <div class="form-group">
		                    <input type="email" class="form-control" placeholder="* Email" name="Email" id="Email" maxlength="50" />
		                </div>
		                <button type="button" class="btn btn-default" id="BtnEmailCheck" onclick="OpenEmailValidation()" >Επαλήθευση Email</button>
		                <br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" class="form-control" placeholder="* Τηλέφωνο κινητό" name="Mobile" id="Mobile" maxlength="10" />
		                </div>
						<button type="button" class="btn btn-default" id="BtnMobileCheck" onclick="OpenMobileValidation()">Επαλήθευση κινητού τηλεφώνου</button>
						 <br/><br/><br/>
		                <div class="form-group">
		                    <input type="tel" class="form-control" placeholder="Τηλέφωνο σταθερό" name="FixedPhone" id="FixedPhone" maxlength="10" />
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
		        <input type="text" class="form-control" placeholder="* Διεύθυνση κατοικίας" id="Address" maxlength="100" />
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
            			<div class="col-sm-8"><input type="text" disabled="" class="form-control" id="StreetName" name="StreetName" placeholder="Οδός" style="background-color:#fffdf3" /></div>
	            		<div class="col-sm-2"><input type="text" disabled="" class="form-control" id="StreetNumber" placeholder="Αριθμός" style="background-color:#fffdf3" /></div>	
	            		<div class="col-sm-2"><input type="text" disabled="" class="form-control" id="zipCode" placeholder="Τ.Κ." style="background-color:#fffdf3" /></div>
            		</div>
            	</div> 
            	<br/>
            	<div class="row">
            		<div class="col-sm-6"><input type="text" disabled="" class="form-control" id="Municipality" placeholder="Δήμος" style="background-color:#fffdf3" /></div>	
            		<div class="col-sm-6"><input type="text" disabled="" class="form-control" id="Division" placeholder="Νομός / Τομέας" style="background-color:#fffdf3" /></div>
		        </div>
                <br/><br/><br/><br/>
                

			    <div class="row">
			    	<div class="col-sm-6"><button class="btn btn-danger btn-block" onclick="ResetData();return false">Επιστροφή στην αρχική</button></div>
			    	<div class="col-sm-6"><button class="btn btn-success btn-block" onclick="submitData();return false">Επόμενο βήμα</button></div>
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
                  	<input type="text" maxlength="4" id="EmailPIN" style="text-align:center;font-size:18px" />
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

        
          <button type="button" class="btn btn-info" onclick="SendEmail()" id="BtnSendEmail">Αποστολή κωδικού στο email μου</button>
          <button type="button" class="btn btn-default" data-dismiss="modal" id="BtnSendEmailCancel">Κλείσιμο</button>
          <button type="button" class="btn btn-success" onclick="CheckEmailCode()" id="BtnCheckEmailCode">Επαλήθευση</button>
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
                  	<input type="text" maxlength="4" id="MobilePIN" style="text-align:center;font-size:18px" />
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
          <button type="button" class="btn btn-info" id="BtnSendMobileSMS" onclick="SendMobileCode(1)" >Επαλήθευση μέσω SMS</button>
          <button type="button" class="btn btn-warning" id="BtnSendMobileVoice" onclick="SendMobileCode(2)">Επαλήθευση μέσω φωνητικής κλήσης</button>
          <button type="button" class="btn btn-default" id="BtnSendMobileCancel" data-dismiss="modal">Κλείσιμο</button>
          <button type="button" class="btn btn-success" onclick="CheckMobileCode()" id="BtnCheckMobileCode">Επαλήθευση</button>
        </div>
      </div>
    </div>
  </div>
<!-------------------------------------------------------------------------------------------------------------------------------------------->



<script>

	  	
	var Autocomplete = null;
	var map =  null;
	var myAddress = new fullAddress();
	var infowindowG = null;
	var markerG = null;
	
	function NoNumbersAddressClick(){
		$('#ErrorMsgAddress').hide();
		var StreetName = $("#StreetName").val();
		var NoNumbersAddress = $(this.NoNumbersAddress).prop('checked');
		var CustomAddress = $("#CustomAddress").prop('checked');	
		if(StreetName=='' && NoNumbersAddress && myAddress.Locality!='' && !CustomAddress){
			$("#StreetName").val(myAddress.Locality);
			myAddress.StreetName = myAddress.Locality;
		}
	}
	
	function zipCodefocusout(){
		var CustomAddress = $("#CustomAddress").prop('checked');
		if(!CustomAddress) return;
		var address_components = null;
		var geometry = null;
		$('#Address').val('');
		$('#ErrorMsgAddress').hide();
		var zip = $("#zipCode").val();
		
		if(zip.length==5){
			zip = zip.substr(0, 3) + ' ' + zip.substr(3, 2);
		}
		$("#zipCode").val(zip);
		
		$.getJSON('http://maps.googleapis.com/maps/api/geocode/json?language=el&address=GR' + zip).done(function(response){
			var res = response.results[0];
			address_components_exist = false;
			$.each(res, function(index, val) {
			    if(index=="address_components"){
					address_components_exist = true;
					address_components = response.results[0].address_components;
				}
			    if(index=="geometry"){
					geometry = response.results[0].geometry;
				}
			});
		    if (!address_components_exist || address_components==null){
				myAddress.Locality = '';
				myAddress.Municipality = '';
				myAddress.Division = '';
				myAddress.Country = '';
				myAddress.IsValid = false;
				$("#Municipality").val('');
				$("#Division").val('');
				return;
			}
		 	for (var i = 0; i < address_components.length; i++) {
		      for (var j = 0; j < address_components[i].types.length; j++) {
		      	//-------------------------------------------------------------
		      	console.log('--->' + address_components[i].types[j]);	      	
		      	console.log(address_components[i].long_name);
		      	//-------------------------------------------------------------
		        if (address_components[i].types[j] == "country") {
		          	myAddress.Country = address_components[i].long_name;
		        }
		        if (address_components[i].types[j] == "locality" ||
		        	address_components[i].types[j] == "administrative_area_level_4" ||
		        	address_components[i].types[j] == "administrative_area_level_5"){	          
		          	myAddress.Municipality = address_components[i].long_name;
					$("#Municipality").val(myAddress.Municipality);
		        }
		        if (address_components[i].types[j] == "administrative_area_level_3" ||
		        	address_components[i].types[j] == "administrative_area_level_4") {	          
		          	myAddress.Division = address_components[i].long_name;
					$("#Division").val(myAddress.Division);
		        }
		        if (address_components[i].types[j] == "locality"){	          
		          	myAddress.Locality = address_components[i].long_name;
		        }		        
		      }
		    }


			if(myAddress.Country!="Ελλάδα"){
				myAddress.Locality = '';
				myAddress.Municipality = '';
				myAddress.Division = '';
				myAddress.Country = '';
				myAddress.IsValid = false;
				$("#Municipality").val('');
				$("#Division").val('');
				return;
			}		        
	        if (geometry!=null){
			    var center = new google.maps.LatLng(geometry.location.lat, geometry.location.lng);
			    map.panTo(center);
			    map.setZoom(15);
	        }
		});
	}

	$('#zipCode')[0].oninput = function(){
		myAddress.Zip = $("#zipCode").val();
		$('#ErrorMsgAddress').hide();
	}

	$('#StreetName')[0].oninput = function(){
		myAddress.StreetName = $("#StreetName").val();
		$('#ErrorMsgAddress').hide();
	}	
	
	$('#StreetNumber')[0].oninput = function(){
		myAddress.StreetNumber = $("#StreetNumber").val();
		$('#ErrorMsgAddress').hide();
	}
	
	function CustomAddressClick(){
		myAddress.Locality = '';
		myAddress.StreetName = '';
		myAddress.StreetNumber = '';
		myAddress.Zip = '';
		myAddress.Municipality = '';
		myAddress.Division = '';
		myAddress.Country = '';
		myAddress.IsValid = false;
		$("#StreetName").val('');
		$("#StreetNumber").val('');
		$("#zipCode").val('');
		$("#Municipality").val('');
		$("#Division").val('');		
		$('#ErrorMsgAddress').hide();
		var CustomAddress = $("#CustomAddress").prop('checked');
		bgcolor = CustomAddress ? "#f7f9fc": "#fffdf3";
		$("#StreetName, #StreetNumber, #zipCode").attr("disabled", !CustomAddress).css("background-color", bgcolor);
		bgcolor = CustomAddress ? "#fffdf3": "#f7f9fc";		
		$("#Address").attr("disabled", CustomAddress).css("background-color", bgcolor);
		if(CustomAddress){
		 	$("#Address").val('');	
			infowindowG.close();
			markerG.setVisible(false);
		}
		
	}
	function fullAddress(){
		this.Locality = '';
		this.StreetName = '';
		this.StreetNumber = '';
		this.Zip = '';
		this.Municipality = '';
		this.Division = '';
		this.Country = '';
		this.IsValid = false;
	}

	$('#Address')[0].oninput = function(){
		$("#StreetName").val('');
		$("#StreetNumber").val('');
		$("#zipCode").val('');
		$("#Municipality").val('');
		$("#Division").val('');
		myAddress.StreetName = '';
		myAddress.StreetNumber = '';
		myAddress.Zip = '';
		myAddress.Municipality = '';
		myAddress.Division = '';
		myAddress.Country = '';	
		myAddress.Locality = '';	
	};


	  function initMap(){
	  	Autocomplete = new google.maps.places.Autocomplete($("#Address")[0], {});
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
			
		 	for (var i = 0; i < place.address_components.length; i++) {
		      for (var j = 0; j < place.address_components[i].types.length; j++) {
		      	//-------------------------------------------------------------
		      	console.log('--->' + place.address_components[i].types[j]);	      	
		      	console.log(place.address_components[i].long_name);
		      	//-------------------------------------------------------------
		        if (place.address_components[i].types[j] == "route") {	          
		          	myAddress.StreetName = place.address_components[i].long_name;
					$("#StreetName").val(myAddress.StreetName);
		        }
		        if (place.address_components[i].types[j] == "street_number") {	          
		          	myAddress.StreetNumber = place.address_components[i].long_name;
					$("#StreetNumber").val(myAddress.StreetNumber);
		        }
		        if (place.address_components[i].types[j] == "postal_code") {	          
		          	myAddress.Zip = place.address_components[i].long_name;
					$("#zipCode").val(myAddress.Zip);
		        }
		        if (place.address_components[i].types[j] == "locality" ||
		        	place.address_components[i].types[j] == "administrative_area_level_5"){	          
		          	myAddress.Municipality = place.address_components[i].long_name;
					$("#Municipality").val(myAddress.Municipality);
		        }
		        if (place.address_components[i].types[j] == "administrative_area_level_3" ||
		        	place.address_components[i].types[j] == "administrative_area_level_4") {	          
		          	myAddress.Division = place.address_components[i].long_name;
					$("#Division").val(myAddress.Division);
		        }
		        if (place.address_components[i].types[j] == "country") {
		          	myAddress.Country = place.address_components[i].long_name;
		        }
		        if (place.address_components[i].types[j] == "locality"){	          
		          	myAddress.Locality = place.address_components[i].long_name;
		        }
		      }
		    }
			if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
			} else {
				map.setCenter(place.geometry.location);
				map.setZoom(17);  // Why 17? Because it looks good.
			}
			marker.setPosition(place.geometry.location);
			marker.setVisible(true);
			var address = '';
			if (place.address_components) {
				address = myAddress.StreetName + ' ' + myAddress.StreetNumber + ', ' + myAddress.Municipality;
				if(myAddress.StreetName=='') address = myAddress.Municipality;
			}
			infowindowContent.children['place-address'].textContent = address;
			infowindow.open(map, marker);
			AddressValidation();			
		});
	  }
	  
	function AddressValidation(){		
		var NoNumbersAddress = $(this.NoNumbersAddress).prop('checked');
		var CustomAddress = $("#CustomAddress").prop('checked');
		myAddress.IsValid =
				myAddress.StreetName != '' && 
				(myAddress.StreetNumber != '' || NoNumbersAddress) && 
				myAddress.Zip != '' && 
				myAddress.Municipality!='' &&
				myAddress.Country == 'Ελλάδα';
		if(!myAddress.IsValid && myAddress.Locality!='' && myAddress.Zip!='' && myAddress.Country=='Ελλάδα' && NoNumbersAddress && !CustomAddress){
			myAddress.StreetName = myAddress.Locality;
			myAddress.IsValid = true;
			$("#StreetName").val(myAddress.Locality);
		}
		if(!CustomAddress){
			$('#Address').val(myAddress.StreetName + ' ' + myAddress.StreetNumber + ' ' + myAddress.Municipality + ', ' + myAddress.Zip + ', ' + myAddress.Country);
		}
		if(myAddress.IsValid){
			$('#ErrorMsgAddress').hide();
		}else if(myAddress.Country!='Ελλάδα'){
			$('#ErrorMsgAddress').html('Παρακαλούμε εισάγετε έγκυρη διεύθυνση εντός της ελληνικής επικράτειας!');
			$('#ErrorMsgAddress').show();
		}else if(NoNumbersAddress){
			$('#ErrorMsgAddress').html('Παρακαλούμε εισάγετε έγκυρη διεύθυνση! (Τ.Κ., Πόλη)');
			$('#ErrorMsgAddress').show();
		}else{
			$('#ErrorMsgAddress').html('Παρακαλούμε εισάγετε έγκυρη διεύθυνση! (Οδός, Αριθμός, Πόλη)');
			$('#ErrorMsgAddress').show();
		}
		return myAddress.IsValid;
	}
		

	function OpenMobileValidation(){
		$('#RegistrationForm').data('formValidation').validateField('Mobile');
		var isValid = $('#RegistrationForm').data('formValidation').isValidField('Mobile');
		if(!isValid) return;	
		$('#ErrorMsgMobile').hide();
		$('#SendMobileDiv').hide();
		$('#SendMobileMsg').show();	
		$('#BtnCheckMobileCode').hide();
		$('#BtnSendMobileSMS').show();
		$('#BtnSendMobileVoice').show();
		$('#MobileModal').modal('show');		
	}

	function CheckMobileCode(){
		$.post('mobile_vc.php', {PIN: $("#MobilePIN").val()}, function(data){
			if(data.Error == 0){
				$("#Mobile").attr("disabled", true);
				$("#BtnMobileCheck").attr("disabled", true);
				$("#BtnMobileCheck" ).removeClass( "btn-default" ).addClass( "btn-info" );
				$("#BtnMobileCheck").text('Επαληθεύτηκε');
				$('#MobileModal').modal('hide');	
			}else{			
				$("#ErrorMsgMobile").html(data.ErrorDescr);
				$('#ErrorMsgMobile').show();
				$('#MobilePIN').focus();
			}			
		}, "json");	
	}

	function SendMobileCode(CodeType){
		$('#RegistrationForm').data('formValidation').validateField('Mobile');
		var isValid = $('#RegistrationForm').data('formValidation').isValidField('Mobile');
		if(!isValid) return;	
		$("body").css({"cursor":"progress"});
		$("#BtnSendMobileSMS").attr("disabled", true);
		$("#BtnSendMobileVoice").attr("disabled", true);
		$("#BtnSendMobileCancel").attr("disabled", true);			
		$.post('mobile_sc.php', {Mobile: $("#Mobile").val(), CodeType: CodeType}, function(data){
			$("body").css({"cursor":"auto"});
			$("#BtnSendMobileSMS").attr("disabled", false);
			$("#BtnSendMobileVoice").attr("disabled", false);
			$("#BtnSendMobileCancel").attr("disabled", false);		
			if(data.Error == 0){
				$('#SendMobileMsg').hide();				
				$('#BtnSendMobileSMS').hide();
				$('#BtnSendMobileVoice').hide();
				$('#SendMobileDiv').show();
				$('#BtnCheckMobileCode').show();
				$('#MobilePIN').focus();
			}else{
				$('#MobileModal').modal('hide');	
				$("#ErrorMsg").html(data.ErrorDescr);
				$('#ErrModal').modal('show');		
			}
		}, "json");	
	}

	function OpenEmailValidation(){
		$('#RegistrationForm').data('formValidation').validateField('Email');
		var isValid = $('#RegistrationForm').data('formValidation').isValidField('Email');
		if(!isValid) return;	
		$('#ErrorMsgEmail').hide();
		$('#SendEmailDiv').hide();
		$('#SendEmailMsg').show();	
		$('#BtnCheckEmailCode').hide();
		$('#BtnSendEmail').show();	
		$('#EmailModal').modal('show');		
	}

	function CheckEmailCode(){
		$.post('email_vc.php', {PIN: $("#EmailPIN").val()}, function(data){
			if(data.Error == 0){
				$("#Email").attr("disabled", true);
				$("#BtnEmailCheck").attr("disabled", true);
				$("#BtnEmailCheck" ).removeClass( "btn-default" ).addClass( "btn-info" );
				$("#BtnEmailCheck").text('Επαληθεύτηκε');
				$('#EmailModal').modal('hide');	
			}else{			
				$("#ErrorMsgEmail").html(data.ErrorDescr);
				$('#ErrorMsgEmail').show();
				$('#EmailPIN').focus();
			}			
		}, "json");	
	}

	function SendEmail(){
		$('#RegistrationForm').data('formValidation').validateField('Email');
		var isValid = $('#RegistrationForm').data('formValidation').isValidField('Email');
		if(!isValid) return;		
		$("body").css({"cursor":"progress"});
		$("#BtnSendEmail").attr("disabled", true);
		$("#BtnSendEmailCancel").attr("disabled", true);	
		$.post('email_sc.php', {Email: $("#Email").val()}, function(data){
			$("body").css({"cursor":"auto"});
			$("#BtnSendEmail").attr("disabled", false);
			$("#BtnSendEmailCancel").attr("disabled", false);
			if(data.Error == 0){
				$('#SendEmailMsg').hide();
				$('#BtnSendEmail').hide();		
				$('#SendEmailDiv').show();
				$('#BtnCheckEmailCode').show();
				$('#EmailPIN').focus();
			}else{
				$('#EmailModal').modal('hide');	
				$("#ErrorMsg").html(data.ErrorDescr);
				$('#ErrModal').modal('show');		
			}
		}, "json");	
	}
	
		
	function submitData(){		
		$('#RegistrationForm').data('formValidation').validate();
		var isValid = AddressValidation();
		if(!isValid) return;
		var NoNumbersAddress = $(this.NoNumbersAddress).prop('checked') ? 1: 0;
		$.post('u2.php', {StreetName: myAddress.StreetName, StreetNumber: myAddress.StreetNumber, Zip: myAddress.Zip,
						  Municipality: myAddress.Municipality, Division: myAddress.Division, Country: myAddress.Country,
						  NoNumbersAddress: NoNumbersAddress, FixedPhone: $('#FixedPhone').val()}, function(data){
			if(data.Error == 0){
				location.replace("p3.php?SID=" + data.SID);	
			}else{
				$("#ErrorMsg").html(data.ErrorDescr);
				$('#ErrModal').modal('show');
			}
		}, "json");
	}

	function ResetData(){
		location.replace("p1.php");
	}

	$(document).ready(function() {
		$('#Email').focus();
		$(this.NoNumbersAddress).prop('checked', false);
		$("#CustomAddress").prop('checked', false);
		
		$( "#zipCode" ).focusout(function(){
			zipCodefocusout();
		});		
		
		
		
		$('#ErrorMsgAddress').hide();
	    $('#RegistrationForm')
	     	.on('init.field.fv', function(e, data) {
	            var $parent = data.element.parents('.form-group'),
	                $icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
	            $icon.on('click.clearing', function() {
	                if ($icon.hasClass('glyphicon-remove')) {
	                	var reset = data.field != "HomePlace";
	                    data.fv.resetField(data.element, reset);
	                }
	            });
	        })    
		    .formValidation({
		        framework: 'bootstrap',
		        icon: {
		            valid: 'glyphicon glyphicon-ok',
		            invalid: 'glyphicon glyphicon-remove',
		            validating: 'glyphicon glyphicon-refresh'
		        },
		        fields: {
		            Mobile: {
		                validators: {
		                	callback: {
		                        message: 'Παρακαλούμε εισάγετε έγκυρο κινητό τηλέφωνο!',
		                        callback: function(value, validator, $field) {
		                        	return MobileValidator(value);
		                        }
		                    },
		                    notEmpty: {
		                        message: 'Το πεδίο κινητό τηλέφωνο είναι υποχρεωτικό!'
		                    }
		                }
		            },
		        	FixedPhone: {
		                validators: {
		                	callback: {
		                        message: 'Παρακαλούμε εισάγετε έγκυρο σταθερό τηλέφωνο!',
		                        callback: function(value, validator, $field) {
		                        	return FixedPhoneValidator(value);
		                        }
		                    }
		                }
		            },
					Email: {
						validators: {
							emailAddress: {
								message: 'Παρακαλούμε εισάγετε έγκυρο Email!'
							},
		                    notEmpty: {
		                        message: 'Το πεδίο Email είναι υποχρεωτικό!'
		                    }
						}
					}
		        }
		    });
	});



	function FixedPhoneValidator(FixedPhone){
		if(FixedPhone=='') return true;
		var s = FixedPhone.length;
		var b = FixedPhone.substr(0, 1);
		return ((s==10) && (b=='2'));		
	}

	function MobileValidator(Mobile){
		if(Mobile=='') return true;
		var s = Mobile.length;
		var b = Mobile.substr(0, 2);
		return ((s==10) && (b=='69')); 	
	}

	
	var vEmailPIN = "";
	$('#EmailPIN')[0].oninput = function(){
		var EmailPIN = $("#EmailPIN").val();
		var b = isNormalIntegerDigits(EmailPIN);
		if(b){
			vEmailPIN = EmailPIN;
		}else{
			$("#EmailPIN").val(vEmailPIN);
		}
	};

	var vzipCode = "";
	$('#zipCode')[0].oninput = function(){
		$('#ErrorMsgAddress').hide();
		var zipCode = $("#zipCode").val();
		var b = isNormalIntegerDigits(zipCode);
		if(b && zipCode.length<6){
			vzipCode = zipCode;
		}else{
			$("#zipCode").val(vzipCode);
		}
	};


	function isNormalIntegerDigits(str) {	
		var n = true;
		for (var i = 0, len = str.length; i < len; i++) {
			n = n && isNormalInteger(str[i]);
		}
		return n;
	}

	function isNormalInteger(str) {
		return /^\+?(0|[1-9]\d*)$/.test(str);
	}



</script>	

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD4F-S7Cwcm6oG5IU2B7uw6mDE1_5KFKwg&libraries=places&language=el&callback=initMap"></script>

</body>

</html>


