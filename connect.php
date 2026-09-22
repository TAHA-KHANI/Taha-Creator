<?php

static $con = null;
if($con===null){
$con=railway_mysql();
}
// Check connection
if (mysqli_connect_errno($con))
 {
   sm(1377243724,"Failed to connect $userbott to MySQL: " . mysqli_connect_error());
   mysqli_close($con);

   
 }
 
 ?>
