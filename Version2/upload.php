<?php
  $r1 = 0;
  $r2 = 0;
  $conn = mysqli_connect("localhost","root",123456);
  mysqli_select_db($conn, "googleweather");

  $sql = "SELECT * FROM path WHERE orig='".$_POST['orig']."'";
  $result1  = mysqli_query($conn, $sql);
  if(($result1->num_rows) > 0){
    $r1 = 1;
  }

  $sql = "SELECT * FROM path WHERE dest='".$_POST['dest']."'";
  $result2  = mysqli_query($conn, $sql);
  if(($result2->num_rows) > 0){
    $r2 = 1;
  }

  if(($r1 + $r2) < 2){
    $sql = "INSERT INTO path (orig, dest, waypoints) VALUES('" .$_POST['orig']. "', '" .$_POST['dest']. "', '" .$_POST['waypoints']. "')";
    $result = mysqli_query($conn, $sql);
  }

?>
