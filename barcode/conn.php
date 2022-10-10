<?php
// Database for ESP logger
$servername = "mysql";

$api_key_value = "tPmAT5Ab3j7F9";

$conn = mysqli_connect($servername, 'root', 'root', 'barcode');


if ($_SERVER["REQUEST_METHOD"] == "POST") $api_key = test_input($_POST["api_key"]);
if ($_SERVER["REQUEST_METHOD"] == "GET") $api_key = test_input($_GET["api_key"]);

if(($api_key != $api_key_value)&&($check_key==TRUE)) {
 
    die("API Key incorrect " );
 
}


function test_input($data) {
   // $data = trim($data);
    $data= trim(preg_replace('/\s+/', ' ', $data));
  //  $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function decode_UDI($UDI){



  $UDI_check=$UDI;
  global $conn; 

  if ((is_numeric($UDI_check)==true) && (substr($UDI_check,0,2)=='01')) {
      $mid= substr($UDI_check,2,14); 


      
      $sql="UPDATE UDI SET Id = '$mid' WHERE (QRcode = '$UDI')";
     
      $query=$conn->query($sql);

     

      $UDI_check= substr($UDI_check,16);

      //hydrocision UDI format

      if  (substr($UDI_check,0,2)=='11')
          {
              $mDate= substr($UDI_check,2,6);

              $sql="UPDATE UDI SET manufactureDate = '$mDate' WHERE (QRcode = '$UDI')";
              $query=$conn->query($sql);
            

              $UDI_check=substr($UDI_check,8);

              if  (substr($UDI_check,0,2)=='17'){

                  $eDate= substr($UDI_check,2,6);

                  $sql="UPDATE UDI SET expireDate = '$eDate' WHERE (QRcode = '$UDI')";
                  $query=$conn->query($sql);   

                  $UDI_check=substr($UDI_check,8);

                  if  (substr($UDI_check,0,2)=='10'){

                      $lot= substr($UDI_check,2);

                      $sql="UPDATE UDI SET lot = '$lot' WHERE (QRcode = '$UDI')";
                      $query=$conn->query($sql);   
                  }

              }

          }

          // Therabody UDI format

      if  (substr($UDI_check,0,2)=='10')
          {
              $lot= substr($UDI_check,2,4);

             

              $sql="UPDATE UDI SET lot = '$lot' WHERE (QRcode = '$UDI')";
              $query=$conn->query($sql);

              $UDI_check=substr($UDI_check,6);

              if  (substr($UDI_check,0,2)=='21'){

                  $sn= substr($UDI_check,2,5);

                  $sql="UPDATE UDI SET SN = '$sn' WHERE (QRcode = '$UDI')";
                  $query=$conn->query($sql);   

                  
              }

          }

        }


}





?>