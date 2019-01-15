<?php
  $conn = mysqli_connect("localhost","root",123456);
  mysqli_select_db($conn, "googleweather");

  $sql = "SELECT * FROM waypoints WHERE name='".$_POST['name']."'";
  $result1  = mysqli_query($conn, $sql);

  if($result1->num_rows == 0){
    $sql = "INSERT INTO waypoints (name, weather, temperature, humidity, lng, lat) VALUES('" .$_POST['name']. "', '" .$_POST['weather']. "', '" .$_POST['temperature']. "', '" .$_POST['humidity']. "', '" .$_POST['lng']."', '" .$_POST['lat']."')";
    //echo '<script>alert('.$_POST['name'].')</script>';
    $result = mysqli_query($conn, $sql);
  }

?>
