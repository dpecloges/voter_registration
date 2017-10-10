var fGoogleMap = null;
var fGoogleMapMarker = null;

function InitGoogleMap()
{
	fGoogleMap = new google.maps.Map(document.getElementById('map'), {
		zoom: 17,
		center: {lat: 37.9752816, lng: 23.736729}
	});
}

function ShowAddressOnGoogleMap(address)
{
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({'address': address}, function(results, status) {
		if (status === 'OK') 
		{
			fGoogleMap.setCenter(results[0].geometry.location);
			fGoogleMapMarker  = new google.maps.Marker({
			map: fGoogleMap,
			position: results[0].geometry.location
			});
		} 
		else 
		{
			alert('Geocode was not successful for the following reason: ' + status);
		}
	});
}

