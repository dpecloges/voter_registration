var fGoogleMap = null;
var fGoogleMapMarker = null;

function InitGoogleMap()
{
	fGoogleMap = new google.maps.Map(document.getElementById('map'), {
		zoom: 15,
		center: {lat: 37.9752816, lng: 23.736729}
	});
}

function ShowAddressOnGoogleMap(address,addMarker)
{
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({'address': address}, function(results, status) {
		if (status === 'OK') 
		{
			fGoogleMap.setCenter(results[0].geometry.location);
			
			if(addMarker)
			{
				fGoogleMapMarker = new google.maps.Marker({
					map: fGoogleMap,
					position: results[0].geometry.location
				});
			}
			else
			{
				fGoogleMapMarker = null;
			}
		} 
		else 
		{
			fGoogleMapMarker = null;
			//alert('Geocode was not successful for the following reason: ' + status);
		}
	});
}

