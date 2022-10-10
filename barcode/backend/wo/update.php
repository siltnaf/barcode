<?php
 $check_key=FALSE;
include_once "conn.php";




if (isset($_POST["delete"])){
    $WO=test_input($_POST["WO"]);
    
 
    $sql= " DELETE FROM workorder WHERE (WO = '$WO') ;";     
    $query=$conn->query($sql);
  
}

if (($_POST["update"])=="WO"){

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $WO=test_input($_POST["WO"]);


   $sql="UPDATE workorder  SET $column = '$value' WHERE ( WO = '$WO');";
   $query=$conn->query($sql);
   echo $sql;
}






?>

