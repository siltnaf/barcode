<?php
 $check_key=FALSE;
include_once "../../conn.php";




if (isset($_POST["delete"])){
    $station=test_input($_POST["station"]);
    $sql=   "DELETE FROM station WHERE (station = '$station');";
    $query=$conn->query($sql);
    
  
}

if (($_POST["update"])=="station"){

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $station=test_input($_POST["station"]);

   /*   if ($value==''){
      
        $sql="DELETE FROM station WHERE (station='$station');";
  

      }
      else{ */
   $sql="UPDATE station  SET $column = '$value' WHERE ( station = '$station');";
   
   //}
   $query=$conn->query($sql);
   echo $sql;
}



?>