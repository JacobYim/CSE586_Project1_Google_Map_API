<!DOCTYPE html>
<?php
  // $conn = mysqli_connect("localhost","root",123456);
  // mysqli_select_db($conn, "googleweather");
  // $result = mysqli_query($conn, "SELECT * FROM PATH");
  // while( $row = mysqli_fetch_assoc($result)){
  //   echo '<p>'.$row['orig'].' '.$row['dest'].'</p>';
  // }
?>
<html>
  <head>
    <title>Find The Path with Weather</title>
    <meta name="viewprot" content="width = device-width, initial-scale =1">
    <meta http-equiv ="Content-Type" content = "text/html; charset=UTF-8">
	  <link rel = "stylesheet" href = "css/bootstrap.css">
    <style>
    #map {
       	height: 500px;
       	width: 100%;
      }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  </head>
  <body>
    <script type = text/javascript>

      var waypointsName = [];
      var waypointsWeather = [];
      var waypointsTemperature = [];
      var waypointsHumidity = [];

      var waypointsLong=[];
      var waypointsLat=[];
      
      var original_point = "";
      var destination_point = "";

      function initMap() {

        var map = new google.maps.Map(document.getElementById('map'), {
          mapTypeControl: false,
          center: {lat: 42.4401, lng: -79.3315},
          zoom: 13
        });

        new AutocompleteDirectionsHandler(map);
      }

      function AutocompleteDirectionsHandler(map) {

        this.map = map;

        this.originPlaceId = null;      // 초기 출발 지점
        this.destinationPlaceId = null; // 도착 지점
        this.travelMode = 'DRIVING';    // 이동 방법

        var markerArray1 = [];
        this.ma = markerArray1;

        var originInput = document.getElementById('origin-input');
        var destinationInput = document.getElementById('destination-input');
        var modeSelector = document.getElementById('mode-selector');

        this.directionsService = new google.maps.DirectionsService;
        this.directionsDisplay = new google.maps.DirectionsRenderer;
        this.directionsDisplay.setMap(map);

        // 새로운 장소를 autocomplete에서 받아옴
        var originAutocomplete = new google.maps.places.Autocomplete(originInput, {placeIdOnly: true});
        var destinationAutocomplete = new google.maps.places.Autocomplete(destinationInput, {placeIdOnly: true});

        this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
        this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');
        this.route();
      }

      // 목적지 혹은 출발지 지정 함수
      AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function(autocomplete, mode) {
        var me = this;
        autocomplete.bindTo('bounds', this.map);
        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();
          if (!place.place_id) {
            window.alert("Please select an option from the dropdown list.");
            return;
          }
          if (mode === 'ORIG') {
            me.originPlaceId = place.place_id;
            original_point = place.name;
          } else {
            me.destinationPlaceId = place.place_id;
            destination_point = place.name;
          }
          me.route();
        });

      };

      AutocompleteDirectionsHandler.prototype.route = function() {
        // 출발지와 목적지가 같으면 바로 돌아감
        if (!this.originPlaceId || !this.destinationPlaceId) {
          return;
        }
        
        var me = this;
        var map = me.map;
        var markerArray = me.ma;

        for (var i = 0; i < markerArray.length; i++) {
            markerArray[i].setMap(null);
        }

        waypointsName = [];
        waypointsWeather = [];
        waypointsTemperature = [];
        waypointsHumidity = [];
        waypointsLong=[];
        waypointsLat=[];

        $.post('http://localhost:8080/check.php', { orig: original_point, dest: destination_point}, function( check ){
            if (check == 0){
              //alert(check);
              me.directionsService.route({
                origin: {'placeId': me.originPlaceId},
                destination: {'placeId': me.destinationPlaceId},
                travelMode: me.travelMode
              }, function(response, status) {
                if (status === 'OK') {
                  me.directionsDisplay.setDirections(response);
                  showSteps(response, markerArray, map);
                } else {
                  window.alert('Directions request failed due to ' + status);
                }
              });
            }
            else{
              //alert(check);
              var obj = JSON.parse(check);
                $.each(obj, function(j, field){
                  //alert(field.name);
                  //console.log(field);
                  //takes temperature
                  if(field.temperature != null){
                    waypointsTemperature.push(field.temperature);
                  }
                  
                  // takes humidity
                  if(field.humidity != null){
                    waypointsHumidity.push(field.humidity);
                  }

                  // takes weather
                  if(field.weather != null){
                    waypointsWeather.push(field.weather);
                  }

                  // takes name
                  if(field.name != null){
                    waypointsName.push(field.name);
                  }

                  if(field.lng != null){
                    waypointsLong.push(field.lng);
       
                  }
                  if(field.lat != null){
                    waypointsLat.push(field.lat);
                  } 
                });
              
              // bring google marker;
              
              showMarkerfromDatabase(waypointsLong, waypointsLat, map);
              // alert(waypointsLat);
              showdata();
            }
        });
      };

      // google marker function
      function showMarkerfromDatabase(waypointsLong, waypointsLat, map){
        var startplace;
        for (var i = 0; i < waypointsLong.length; i++) {
          var marker = new google.maps.Marker({
            position : {lat: Number(waypointsLat[i]), lng: Number(waypointsLong[i])},
            map : map
          });
          if (i == 0){
             startplace = marker;
          }
          marker.setMap(map);
          //marker.setPosition(startplace);
          //marker.setMap(map);
          //alert(marker);
        }
      }

      function showSteps(directionResult, markerArray, map) {

        var myRoute = directionResult.routes[0].legs[0];
        
        for (var i = 0; i < myRoute.steps.length; i++) {
          var marker = markerArray[i] = markerArray[i] || new google.maps.Marker;
          marker.setMap(map);
          marker.setPosition(myRoute.steps[i].start_location);
          var lat = marker.getPosition().lat();
          var log = marker.getPosition().lng();
          //alert(lat);
          waypointsLong.push(log);
          waypointsLat.push(lat);        

          $.getJSON("https://api.openweathermap.org/data/2.5/weather?lat="+lat+"&lon="+log+"&appid=a5f6ad8919513f23ddd164907a046020",function(result){

              $.each(result, function(j, field){
                  //console.log(field);
                  // takes temperature
                  if(field.temp!=null){
                  // alert('Temperature :' + (field.temp-273.00)+'°C');
                    waypointsTemperature.push((field.temp-273.00).toFixed(2)+'°C');
                  }

                  // takes humidity
                  if(field.humidity!=null){
                    //alert('humidity :' + field.humidity+'%');
                    waypointsHumidity.push(field.humidity);
                  }

                  // takes weather
                  if(j == "weather"){
                    if(field[0].main != null)
                    //alert('weather : ' + field[0].description);
                    waypointsWeather.push(field[0].main);
                  }

                  // takes name
                  if(j == "name"){
                    waypointsName.push(field);
                  }
              });

              if(waypointsName.length == myRoute.steps.length && waypointsName.length != 0){
                showdata();
              }
          });
        }
      }

      function showdata(){
        var table = document.getElementById("weatherDataTable");

        while(table.rows.length!=0){
          table.deleteRow(table.rows.length-1);
        }
        //console.log('리턴 값11');
        $.post('http://localhost:8080/upload.php', { orig: original_point, dest: destination_point, waypoints: waypointsName.toString()});

        var x = waypointsName.length;
        console.log(waypointsName.length);
        console.log(waypointsWeather.length);
        //alert(waypointsName);
        console.log(waypointsWeather);
        console.log(waypointsTemperature);
        console.log(waypointsHumidity);
        var y = 5;
        var inputRow = "";
        table.innerHTML = inputRow;
        for (i = 0 ; i < x; i ++){
          inputRow = inputRow + "<tr><td>"+(i+1)+"</td>";
            for (j = 0 ; j < (y-1); j++){
              if (j == 0){
                inputRow += "<td>"+waypointsName[i]+"</td>";
              }else if (j == 1) {
                inputRow += "<td>"+waypointsWeather[i]+"</td>";
              }else if (j == 2){
                inputRow += "<td>"+waypointsTemperature[i]+"</td>";
              }else{
                inputRow += "<td>"+waypointsHumidity[i]+"%</td>";
              }
            }
            inputRow += "</tr>";
            $.post('http://localhost:8080/uploadpoint.php', { name: waypointsName[i], weather: waypointsWeather[i], temperature: waypointsTemperature[i], humidity: waypointsHumidity[i], lng: waypointsLong[i], lat: waypointsLat[i]});
          }
          table.innerHTML += inputRow;

      }

    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmEGj10qQ2ccjXzeWA7Diia-G_GIIS60c&libraries=places&callback=initMap"
      type="text/javascript"></script>
     <!-- id로 다 변경함 -->

    
    
    
    <div class="container">
    
          <h1>Find Your Path with Weather</h1>

          <h3> Welcome to Junghwan Yim's google map api and weather api project website. </h3>
            
          <h5> This web application will show the path once you type the departure and the destination in the box. Select the name listed by the google auto complete search engine. If you do not select your destination among the listed names, the path is not supported in this application. On the map, the path will show the waypoints. Waypoints' information are shown in the table next to the map along with the weather information.</h5>
          
          <br>

          <div class="col-xs-3">
            Departure
          <input id="origin-input" class="controls" type="text"
            placeholder="Enter an origin location" size = "20">
          </div>
          <div class="col-xs-3">
            Destination
          <input id="destination-input" class="controls" type="text"
            placeholder="Enter a destination location" size = "20">
          </div>
          <br>
          <br>
        <br>
        <br>

        <!-- Google Map parts-->
        <div class="col-xs-12">
          <div id="map"></div>
        </div>
        
        <div class="col-xs-12">
        <br><br>
          <table class="table"
            style="text-align: center; border: 1px solid #dddddd">
            <thead>
              <tr>
                <th style="background-color: #fafafa; text-align: center;">Number</th>
                <th style="background-color: #fafafa; text-align: center;">Waypoints</th>
                <th style="background-color: #fafafa; text-align: center;">Weather</th>
                <th style="background-color: #fafafa; text-align: center;">Temperature</th>
                <th style="background-color: #fafafa; text-align: center;">humidity</th>
              </tr>
            </thead>
            <tbody id="weatherDataTable">
            </tbody>
          </table>
        </div>
    </div>

  </body>
</html>
