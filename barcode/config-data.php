<?php

$check_key=FALSE;
include_once "conn.php";

$station=test_input($_POST["station"]);
$operator=test_input($_POST["operator"]);
$BOM=test_input($_POST["BOM"]);


        $sql="INSERT INTO worker (operator) VALUES ('$operator')";
        $query=$conn->query($sql);



    //    $sql = "SELECT BOM FROM workorder WHERE (station = '$station')  (WO='$WO'); 
    $sql = "SELECT station,stationOrder,QRcomponent,componentCount,BOM FROM BOM_station      
    where (station='$station') and (BOM='$BOM')  ;";
        
        $query=$conn->query($sql);
        $rows=$query->fetch_assoc();
                      

        #echo $rows;
        #******************************************************
        #if($rows== null) $rows=404;  #如果輸入錯誤 輸出404
        #******************************************************
        echo json_encode($rows);
        mysqli_free_result($query);
        mysqli_close($conn);
        
   ?>    
  
