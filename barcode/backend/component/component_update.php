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
    $status=test_input($_POST["status"]);

    if ($status=='true') $status=1; else $status=0;

      if ($value==''){
      
        $sql="DELETE FROM component WHERE (component='$component');";
  

      }
      else{
   $sql="UPDATE component SET full = '$status', description = '$value' WHERE (`component` = '$component')";
   
   }
   $query=$conn->query($sql);
   echo $sql;
}



?>