<?php
  $conn = mysqli_connect("localhost","root",123456);
  mysqli_select_db($conn, "googleweather");

  $sql = "SELECT * FROM path WHERE orig='".$_POST['orig']."'AND dest='".$_POST['dest']."'";
  $result1  = mysqli_query($conn, $sql);
  //error_log($result1);
  if(($result1->num_rows) > 0){

    $result = array(); 

    $sql = "SELECT waypoints FROM path WHERE orig='".$_POST['orig']."'AND dest='".$_POST['dest']."'";
    $result1  = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result1,1);
    $row = implode( ", ", $row );
    $name = explode(",", $row );
    
    for ($i = 0 ; $i < count($name); $i++){
      $name_temp = $name[$i];
      $sql = "SELECT * FROM waypoints WHERE name='".$name_temp."'";
      $result  = mysqli_query($conn, $sql);
      $row = mysqli_fetch_array($result,1);
      $result_array[$i] = $row;
    }
    echo json_encode($result_array, JSON_FORCE_OBJECT);
  }
  else {
    echo json_encode(0);
  }

  //echo json_encode($arr);
?>
