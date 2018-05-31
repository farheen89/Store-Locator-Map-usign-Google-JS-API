<!DOCTYPE html >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>Dealer Location on Google Maps</title>
  <style>
    #map {
       height: 400px; 
		width: 100%; 
    }  
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
	.button {
		background-color: #3399FF;
		border: none;
		color: white;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		font-size: 15px;
		font-weight: 300;
		margin-left: 12px;
		padding: 12px 11px 10px 13px;
		cursor: pointer;
		input {
			width: 100%;
			}
	}
	
	#start, #end {
        background-color: #fff;
        font-family: tahoma, arial, sans-serif;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 12px 11px 10px 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      #start:focus {
        border-color: #4d90fe;
      }
	  
 </style>
  </head>
  <body style="margin:10px; padding:30;" onload="initMap()"> 	
		
 	
	<h3>Dealer Store Locator</h3>
        <input id="start" type="text" class="tb10"  placeholder="Enter Starting location">
		<span>&nbsp; &nbsp;</span>
		<b>Choose Destination: </b>
		<select id="end">
		  <option value="">Select Dealer</option>
		</select>
		<input type="submit" id="btnSearch" size="40" style="width:231px;" value="Search"  class="button" onclick="return getDirection();"/>
		<br><br>		
    
		<label><b>Distance : </b></label> <span id="distance" name="distance" ></span><br>
		<label><b>Travel Time: </b></label> <span id="duration" name="duration" ></span><br>
		<br>
		
	  <div id="map"></div>
    <div id="infowindow-content">
      <img src="" width="16" height="16" id="place-icon">
      <span id="place-name"  class="title"></span><br>
      <span id="place-address"></span>
    </div>
	<br>
    <div style="display:none;">
        <label for="raddressInput">Search location:</label>
        <input type="text" id="addressInput" size="15"/>
        <label for="radiusSelect">Radius:</label>
        <select id="radiusSelect" label="Radius">
          <option value="10">10 kms</option>
          <option value="20">20 kms</option>
          <option value="30">30 kms</option>
          <option value="50" selected>50 kms</option>
		  <option value="100">100 kms</option>		  
        </select>

        <input type="button" id="searchButton" value="Search"/>
    </div>
    <div style="display:none;"><select id="locationSelect" style="width: 10%; visibility: hidden"></select></div>
    <div id="map" style="width: 100%; height: 90%"></div>
	<script>
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
        //ar types = document.getElementById('type-selector');
        //var strictBounds = document.getElementById('strict-bounds-selector');

        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

        var autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
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
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
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
			//console.log(searchUrl); 
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
			//icon: 'H.png'
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
		  if(document.getElementById('start').value==''){
			  alert('please Select Starting Point'); 
		  }else{			  
			var directionsService = new google.maps.DirectionsService;
			var directionsDisplay = new google.maps.DirectionsRenderer;
			
			directionsDisplay.setMap(map);
			directionsService.route({
			  origin: document.getElementById('start').value,
			  destination: document.getElementById('end').value,
			  travelMode: 'DRIVING'
			}, function(response, status) {
			  if (status === 'OK') {			  
				directionsDisplay.setDirections(response);
				var route = response.routes[0];
                    document.getElementById('duration').innerHTML = route.legs[0].duration.text;
					document.getElementById('distance').innerHTML = route.legs[0].distance.text;							
					
			  } else {
				window.alert('Directions request failed due to ' + status);
			  }
			});
		  }
		}
	  	   
  </script>
  	  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCNdpg4EeqbR-w2V6Ii-8Uqc0q9ujM8miU&callback=createMarker&libraries=places">
    </script>
	<script>
		window.document.onload = searchLocations(); 
	</script>
	
</body>
</html>