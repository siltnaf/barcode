<?php
// Database for ESP logger
$servername = "st_mysql";

$api_key_value = "tPmAT5Ab3j7F9";
$mysql_user="root";
$mysql_password="ddkM5EOnowGde2d7";


$conn = mysqli_connect($servername, $mysql_user, $mysql_password, 'barcode');


if ($_SERVER["REQUEST_METHOD"] == "POST") $api_key = test_input($_POST["api_key"]);
if ($_SERVER["REQUEST_METHOD"] == "GET") $api_key = test_input($_GET["api_key"]);

if(($api_key != $api_key_value)&&($check_key==TRUE)) {
 
    die("API Key incorrect " );
 
}


function test_input($data) {
    $data = trim($data);
  //  $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}







?>