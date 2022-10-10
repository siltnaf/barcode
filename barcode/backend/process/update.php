<?php
 $check_key=FALSE;
include_once "../../conn.php";




if (isset($_POST["delete"])){
    $BOM=test_input($_POST["BOM"]);
    $sql=   "DELETE FROM BOM_component WHERE (BOM = '$BOM');";
    $query=$conn->query($sql);
    $sql= " DELETE FROM BOM_station WHERE (BOM = '$BOM') ;";
    $query=$conn->query($sql);
    $sql= " DELETE FROM BOM WHERE (BOM = '$BOM') ;";     
    $query=$conn->query($sql);
  
}

if (($_POST["update"])=="BOM"){

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $BOM=test_input($_POST["BOM"]);


   $sql="UPDATE BOM  SET $column = '$value' WHERE ( BOM = '$BOM');";
   $query=$conn->query($sql);
   echo $sql;
}


if (($_POST["update"])=="station"){

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $BOM =test_input($_POST["BOM"]);
    $station=test_input($_POST["station"]);
 


    if ($column=='station'){
          
            if ($value=="") $sql="DELETE FROM BOM_station WHERE (BOM = '$BOM') and (station = '$station');";

            else if ($station==null) {
                
                
                $sql="INSERT INTO BOM_station (BOM, station) VALUES ('$BOM', '$value');";
                $query=$conn->query($sql);
                
                $sql="SELECT a.*,b.description FROM BOM_station as a join station as b using (station) where a.BOM='$BOM' and b.station='$station' ";
                $query=$conn->query($sql);
                while ($rows=$query->fetch_assoc()){
                    $station_tmp=$rows['station'];
                    $stationOrder_tmp=$rows['stationOrder'];
                    $stationInfo_tmp=$rows['description'];
                    
                        }
                
                // update table ******************



               
            }
            else  $sql="UPDATE BOM_station SET station = '$value' WHERE (`BOM` = '$BOM') and (`station` = '$station');";

        }
        else   $sql="UPDATE BOM_station SET $column = '$value' WHERE (BOM = '$BOM') and (station = '$station');"; 


    

 
   $query=$conn->query($sql);
   echo $sql;





    
}


if (($_POST["update"])=="component"){

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $BOM =test_input($_POST["BOM"]);
    $component=test_input($_POST["component"]);
   
    if ($component!=null){
        if ($value==''){
            $sql="DELETE FROM BOM_component WHERE (BOM = '$BOM') and (component = '$component');";
            
        }
        else{

            $sql="UPDATE BOM_component  SET $column = '$value' WHERE ( BOM = '$BOM') and (component='$component') ;";
        
   
        }
    }
    else{
        $sql="INSERT INTO BOM_component (BOM, component) VALUES ('$BOM', '$value');";
        
        

   }
   $query=$conn->query($sql);
   echo $sql;
}





?>

