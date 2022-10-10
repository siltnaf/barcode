<?php
 $check_key=FALSE;
include_once "../../conn.php";




$value=test_input($_POST["value"]);
$column=test_input($_POST["column"]);
$BOM =test_input($_POST["BOM"]);
$station=test_input($_POST["station"]);
$update=test_input($_POST["update"]);





switch ($update){

    case "station":
        if ($column=='station'){


           

            if ($value!="") {

             $sql="INSERT INTO BOM_station (BOM, station) VALUES ('$BOM', '$value');";
             $query=$conn->query($sql);

             $sql="SELECT a.*,b.description FROM BOM_station as a join station as b using (station) where a.BOM='$BOM'  order by a.stationOrder asc   ";
             $query=$conn->query($sql);
             while ($rows=$query->fetch_assoc()){
                 $station_array[]=$rows['station'];
                 $stationOrder_array[]=$rows['stationOrder'];
                 $stationInfo_array[]=$rows['description'];
                 $stationQR_array[]=$rows['QRcomponent'];
                 $stationCount_array[]=$rows['componentCount'];
                 }
         
                

            }

         }
         else {
             
            
             $sql="UPDATE BOM_station SET $column = '$value' WHERE (BOM = '$BOM') and (station = '$station');";
             $query=$conn->query($sql);

         }



        break;

        case "delete":

            $sql="DELETE FROM BOM_station WHERE (BOM = '$BOM') and (station='$station');";

            $query=$conn->query($sql);
        
         
            $sql="SELECT a.*,b.description FROM BOM_station as a join station as b using (station) where a.BOM='$BOM'  order by a.stationOrder asc   ";
            $query=$conn->query($sql);
            while ($rows=$query->fetch_assoc()){
                $station_array[]=$rows['station'];
                $stationOrder_array[]=$rows['stationOrder'];
                $stationInfo_array[]=$rows['description'];
                $stationQR_array[]=$rows['QRcomponent'];
                $stationCount_array[]=$rows['componentCount'];
                }



       break;

       case "add":
              
                $editable="FALSE";
                $sql="INSERT INTO BOM_station (BOM, station) VALUES ('$BOM', '$station');";
                $query=$conn->query($sql);
                $sql="SELECT a.*,b.description FROM BOM_station as a join station as b using (station) where a.BOM='$BOM'  order by a.stationOrder asc   ";
                $query=$conn->query($sql);
                while ($rows=$query->fetch_assoc()){
                    $station_array[]=$rows['station'];
                    $stationOrder_array[]=$rows['stationOrder'];
                    $stationInfo_array[]=$rows['description'];
                    $stationQR_array[]=$rows['QRcomponent'];
                    $stationCount_array[]=$rows['componentCount'];
                    }

        break;



}


 
 if ($station_array!=null){
    $max_layer=0;
    $accum_layer=[];
    foreach ($station_array as $value){
     
        if (strpos('0'.$value, 'BND') == true) $max_layer++;       
    }


    // check for SMT case where  BOM=QRcomponent
    foreach ($stationQR_array as $value){
        if ($value!=null){

            $sql="SELECT a.maxlayer FROM BOM as a
            join BOM_station as b
            using (BOM) 
            where QRcomponent='$value' and (BOM ='$value')";

            $query=$conn->query($sql);
            if (mysqli_num_rows($query)!=0){

            while ($rows=$query->fetch_assoc()){
                $accum_layer[]=$rows['maxlayer'];
                }

        }


        }
   
    }

       

    if ($accum_layer!=null) $accum_maxlayer=max($accum_layer); else $accum_maxlayer=0;
 
    $max_layer+=$accum_maxlayer;
   
    
   


    if ($max_layer==0) $max_layer=1;    //if no binding station and only QCI station, maxlayer set to 1 
    $sql="UPDATE BOM SET maxlayer = '$max_layer' WHERE (BOM = '$BOM')";
    $query=$conn->query($sql);
    

 }
 


   
    


function QCI_input($column,$BOM,$station,$editable) {
                        
    $result = '<div style="color:grey" contenteditable="'.$editable.'"  onClick="activate(this)">';
    echo $result;
    return $result;
}

  



?>


        <div class="middlepane">


        <table class="info" id="q_result"  >
        <th class="m">QCI station</th>
        <th class="s">Order</th>
        <th class="l">Description</th>
        <th class="m">Key QRcode</th>
        <th class="s">Scan no.</th>
        <th class="xs"></th>
        

    <?php
    
    if ($station_array!=null){
        $arraylength=count($station_array);
        $i=0;
        while  ($i< $arraylength){
            echo
            '<tr id=update_info> 
                
            <td >' .QCI_input("station",$BOM,$station_array[$i],$editable). $station_array[$i].'</div></td> 
            <td class="s">' .QCI_input("stationOrder",$BOM,$station_array[$i],$editable). $stationOrder_array[$i].'</div></td> 
            <td>' .QCI_input("description",$BOM,$station_array[$i],"FALSE").$stationInfo_array[$i].'</div></td> 
            <td>' .QCI_input("QRcomponent",$BOM,$station_array[$i],$editable).$stationQR_array[$i].'</div></td>
            <td>' .QCI_input("componentCount",$BOM,$station_array[$i],$editable).$stationCount_array[$i].'</div></td> 
            <td class="xs"><button type="button" name="q_delete" data-id="'.$station_array["$i"].'" class="btn btn-xs btn-danger btn_delete">-</button></td> 
                    
    
                </tr>';
            $i++;
        }


    }

   


    

    ?>


</table>

</div>



 

      