<?php
 $check_key=FALSE;
include_once "../../conn.php";




if (isset($_POST["delete"])){
    $operator=test_input($_POST["operator"]);
    $sql=   "DELETE FROM worker WHERE (operator = '$operator');";
    $query=$conn->query($sql);
    
  
}

if (($_POST["update"])=="operator"){

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $operator=test_input($_POST["operator"]);

      if ($value==''){
      
        $sql="DELETE FROM worker WHERE (operator='$operator');";
  

      }
      else{
   $sql="UPDATE worker  SET $column = '$value' WHERE ( operator = '$operator');";
   
   }
   $query=$conn->query($sql);
   echo $sql;
}



?>