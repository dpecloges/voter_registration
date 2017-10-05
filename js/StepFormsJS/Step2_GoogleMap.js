var placeSearch, autocomplete;
var componentForm = {
	street_number: 'short_name',
	route: 'long_name',
	locality: 'long_name',
	administrative_area_level_1: 'short_name',
	country: 'long_name',
	postal_code: 'short_name'
};

var fAddressAutoComplete = null;
var fGoogleMap = null;
var fGoogleMapMarker = null;

function InitAddressAutoComplete() 
{
    fAddressAutoComplete = new google.maps.places.Autocomplete((document.getElementById('TextBoxAddress')),{types: ['geocode']});
    fAddressAutoComplete.addListener('place_changed', FillInAddress);
    
	fGoogleMap = new google.maps.Map(document.getElementById('map'), {
		zoom: 17,
		center: {lat: 37.9752816, lng: 23.736729}
	});
}


function FillInAddress() 
{
	// Get the place details from the autocomplete object.
    var place = fAddressAutoComplete.getPlace();
    
    // Set map to that place
    fGoogleMap.setCenter(place.geometry.location);
	fGoogleMap.setZoom(17); 

  	// Set market to place
  	if(fGoogleMapMarker != null) { fGoogleMapMarker.setMap(null); }
  	fGoogleMapMarker = new google.maps.Marker({ position: place.geometry.location, map: fGoogleMap,title: ''});
    
    // Get each component of the address from the place details
    // and fill the corresponding field on the form.
    for (var i = 0; i < place.address_components.length; i++) 
    {
		var addressType = place.address_components[i].types[0];
		if (componentForm[addressType]) 
		{
			var val = place.address_components[i][componentForm[addressType]];
			try
			{
				for (var j = 0; j < place.address_components[i].types.length; j++) 
		      	{
			        if (place.address_components[i].types[j] == "route") 
			        {	          
						$("#TextBoxStreetName").val(place.address_components[i].long_name);
			        }
			        else if (place.address_components[i].types[j] == "street_number") 
			        {	          
						$("#TextBoxStreetNumber").val(place.address_components[i].long_name);
			        }
			        else if(place.address_components[i].types[j] == "postal_code") 
			        {	          
						$("#TextBoxZipCode").val(place.address_components[i].long_name);
						
						// Get division from zip
						$.post('AJAX_GetDhmosAndNomosFromZip.php', {Zip:place.address_components[i].long_name}, 
							function(data){
								var dhmos = data["Dhmos"];
								var nomos = data["Nomos"];
								$("#TextBoxMunicipality").val(dhmos);
								$("#TextBoxDivision").val(nomos);
							});	
			        }
			        else if(place.address_components[i].types[j] == "locality" || place.address_components[i].types[j] == "administrative_area_level_5")
			        {	          
						//$("#TextBoxMunicipality").val(place.address_components[i].long_name);
			        }
		      	}
	      	}
	      	catch(ex)
	      	{
	      		alert(ex);
	      	}
		}
    }
}

