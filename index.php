<?php 
define('API_KEY','AIzaSyCNdpg4EeqbR-w2V6Ii-8Uqc0q9ujM8miU');
?>
<!DOCTYPE html >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>Dealer Locator - Google Maps</title>	
	<link rel="icon" type="image/png" href="images/favicon.ico">
	<script type="text/javascript" src="js/custom.js?v=<?php echo time(); ?>"></script>        
	<link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">

  </head>
  <body onload="initMap()" > 	
 	
	<h3>Dealer Store Locator</h3>
        <input id="start" type="text"  placeholder="Enter Starting location">
		<span>&nbsp; &nbsp;</span>
		<b>Choose Destination: </b>
		<select id="end">
		  <option value="">Select Dealer</option>
		</select>
		<input type="submit" id="btnSearch" size="40" style="width:231px;" value="Search"  class="button" onclick="return getDirection();"/>
		<br><br>
	  
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
          <option value="50" selected>50 kms</option>
        </select>
        <input type="button" id="searchButton" value="Search"/>
    </div>
    <div style="display:none;">
		<select id="locationSelect" style="width: 10%; visibility: hidden"></select>
	</div>
	
	<div id="time_dist">
			<!--<label class="lbl"><b>Distance : </b></label> <span id="distance" ></span><br><br>
			<label class="lbl"><b>Travel Time: </b></label> <span id="duration" ></span><br><br>-->
			<label class="lbl"><br></label> <span id="steps" ></span><br>
	</div>

    <div id="map"></div>
	<script src="js/custom.js?v=<?php echo time(); ?>"></script>
  	<script src="https://maps.googleapis.com/maps/api/js?key=<?php API_KEY; ?>&callback=createMarker&libraries=places">
    </script>
	<script>
		window.document.onload = searchLocations(); 		
	</script>
	
</body>
</html>