  var map;
  var markers = [];
  var infoWindow;
  var locationSelect;

	function initMap() {			
		
	  var india = {lat: 21.7679, lng: 78.8718};
	  map = new google.maps.Map(document.getElementById('map'), {
		center: india,
		zoom: 11,
		mapTypeId: 'roadmap',
		mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
	  });
	  infoWindow = new google.maps.InfoWindow();

	  searchButton = document.getElementById("searchButton").onclick = searchLocations;
	  locationSelect = document.getElementById("locationSelect");
	  locationSelect.onchange = function() {
		var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
		if (markerNum != "none"){
		  google.maps.event.trigger(markers[markerNum], 'click');
		}
	  };	 
	  
	  //auto complete
	var card = document.getElementById('pac-card');
	var input = document.getElementById('start');

	map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

	var autocomplete = new google.maps.places.Autocomplete(input);
	autocomplete.bindTo('bounds', map);

	var infowindow = new google.maps.InfoWindow();
	var infowindowContent = document.getElementById('infowindow-content');
	infowindow.setContent(infowindowContent);
	var marker = new google.maps.Marker({
	  map: map,
	  anchorPoint: new google.maps.Point(0, -29)
	});

	autocomplete.addListener('place_changed', function() {
	  infowindow.close();
	  marker.setVisible(false);
	  var place = autocomplete.getPlace();
	  if (!place.geometry) {
		window.alert("No details available for input: '" + place.name + "'");
		return;
	  }

	  // If the place has a geometry, then present it on a map.
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
		address = [
		  (place.address_components[0] && place.address_components[0].short_name || ''),
		  (place.address_components[1] && place.address_components[1].short_name || ''),
		  (place.address_components[2] && place.address_components[2].short_name || '')
		].join(' ');
	  }

	  infowindowContent.children['place-icon'].src = place.icon;
	  infowindowContent.children['place-name'].textContent = place.name;
	  infowindowContent.children['place-address'].textContent = address;
	  infowindow.open(map, marker);
	});	
	  
	}

       function searchLocations() {		
         //var address = document.getElementById("addressInput").value;
		 var address = 'mumbai';
         var geocoder = new google.maps.Geocoder();
         geocoder.geocode({address: address}, function(results, status) {
           if (status == google.maps.GeocoderStatus.OK) {
            searchLocationsNear(results[0].geometry.location);
           } else {
             alert(address + ' not found');
           }
         });
       }

       function clearLocations() {
         infoWindow.close();
         for (var i = 0; i < markers.length; i++) {
           markers[i].setMap(null);
         }
         markers.length = 0;

         locationSelect.innerHTML = "";
         var option = document.createElement("option");
         option.value = "none";
         option.innerHTML = "See all results:";
         locationSelect.appendChild(option);
       }

       function searchLocationsNear(center) {
         //clearLocations();			 
         //var radius = document.getElementById('radiusSelect').value;
		 var radius = 10; 
		 var loc_html = '' ; 
         var searchUrl = 'storelocator.php?lat=' + center.lat() + '&lng=' + center.lng() + '&radius=' + radius;		
		 downloadUrl(searchUrl, function(data) {
           var xml = parseXml(data);
           var markerNodes = xml.documentElement.getElementsByTagName("marker");
           var bounds = new google.maps.LatLngBounds();
		   for (var i = 0; i < markerNodes.length; i++) {
             var id = markerNodes[i].getAttribute("id");
             var name = markerNodes[i].getAttribute("name");
			 var city_name = markerNodes[i].getAttribute("city_name");
             var address = markerNodes[i].getAttribute("address");
             var distance = parseFloat(markerNodes[i].getAttribute("distance"));
             var latlng = new google.maps.LatLng(
                  parseFloat(markerNodes[i].getAttribute("lat")),
                  parseFloat(markerNodes[i].getAttribute("lng")));

             createOption(name, distance, i);
             createMarker(latlng, name, address);
             bounds.extend(latlng);
           loc_html += '<option value='+city_name+' >'+name+'</option>';
           }
		   var select = document.getElementById('start');
			select.innerHTML = loc_html;
			var select2 = document.getElementById('end');
			select2.innerHTML = loc_html;
		  
           map.fitBounds(bounds);
           locationSelect.style.visibility = "visible";
           locationSelect.onchange = function() {
             var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
             google.maps.event.trigger(markers[markerNum], 'click');
           };
         });
       }

       function createMarker(latlng, name, address) {
		  var html = "<b>" + name + "</b> <br/>" + address;
          var marker = new google.maps.Marker({
            map: map,
            position: latlng,
			icon: 'images/favicon.ico'
          });
          google.maps.event.addListener(marker, 'click', function() {
            infoWindow.setContent(html);
            infoWindow.open(map, marker);
          });
          markers.push(marker);
        }

       function createOption(name, distance, num) {
          var option = document.createElement("option");
          option.value = num;
          option.innerHTML = name;
          locationSelect.appendChild(option);
       }

       function downloadUrl(url, callback) {
          var request = window.ActiveXObject ?
              new ActiveXObject('Microsoft.XMLHTTP') :
              new XMLHttpRequest;

          request.onreadystatechange = function() {
            if (request.readyState == 4) {
              request.onreadystatechange = doNothing;
              callback(request.responseText, request.status);
            }
          };
          request.open('GET', url, true);
          request.send(null);
       }

       function parseXml(str) {
          if (window.ActiveXObject) {
            var doc = new ActiveXObject('Microsoft.XMLDOM');
            doc.loadXML(str);
            return doc;
          } else if (window.DOMParser) {
            return (new DOMParser).parseFromString(str, 'text/xml');
          }
       }

      function doNothing() {}
	   
	  function getDirection() {
			var directionsService = new google.maps.DirectionsService;
			var directionsDisplay = new google.maps.DirectionsRenderer;
			
		  if(document.getElementById('start').value==''){
			  document.getElementById('time_dist').style.display = 'none';
			  alert('please Select Starting Point'); 
		  }else{			  
			
			directionsDisplay.setMap(map);
			directionsService.route({
			  origin: document.getElementById('start').value,
			  destination: document.getElementById('end').value,
			  travelMode: 'DRIVING'
			}, function(response, status) {
			  if (status === 'OK') {			  
				directionsDisplay.setDirections(response);
				var route = response.routes[0];
					document.getElementById('time_dist').style.display = 'block';
                    document.getElementById('duration').innerHTML = route.legs[0].duration.text;
					document.getElementById('distance').innerHTML = route.legs[0].distance.text;							
					
			  } else {
				window.alert('Directions request failed due to ' + status);
			  }
			});
		  }
		}