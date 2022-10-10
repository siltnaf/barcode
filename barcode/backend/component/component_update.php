<?php
 $check_key=FALSE;
include_once "../../conn.php";




if (isset($_POST["delete"])){
    $component=test_input($_POST["component"]);
    $sql=   "DELETE FROM component WHERE (component = '$component');";
    $query=$conn->query($sql);
    
  
}

if (($_POST["update"])=="component"){

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $component=test_input($_POST["component"]);

      if ($value==''){
      
        $sql="DELETE FROM component WHERE (component='$component');";
  

      }
      else{
   $sql="UPDATE component  SET $column = '$value' WHERE ( component = '$component');";
   
   }
   $query=$conn->query($sql);
   echo $sql;
}



?>