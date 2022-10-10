<?php

include_once "../../conn.php";


$value=test_input($_POST["value"]);
$column=test_input($_POST["column"]);
$BOM =test_input($_POST["BOM"]);
$station=test_input($_POST["station"]);
$update=test_input($_POST["update"]);



if ($update=="BOM"){

    
  echo "$column";
  echo "$value";
 

    $sql="UPDATE BOM SET $column = '$value' WHERE (BOM = '$BOM') ;"; 

    $query=$conn->query($sql);


}

 if ($update=="delete"){
    
     
    $BOM=test_input($_POST["BOM"]);
    $sql="DELETE FROM BOM_component WHERE (BOM = '$BOM');";
    $query=$conn->query($sql);


    $sql="DELETE FROM BOM_station WHERE (BOM = '$BOM');";
    $query=$conn->query($sql);

    $sql="DELETE FROM BOM WHERE (BOM = '$BOM');";

    $query=$conn->query($sql);

     


    $sql="SELECT * FROM BOM  ";
    $query=$conn->query($sql);
    while ($rows=$query->fetch_assoc()){
        $BOM_array[]=$rows['BOM']; 
        $BOMinfo_array[]=$rows['description'];
        
            }






 }



    ?>




<div class="leftpane" id="w_result">



<table class="info" >

<th class="m">BOM</th>
<th class="l">Description</th>

    <?php

 

if (($update=="delete")&&($BOM_array!=null)) {
      
         

                $arraylength=count($BOM_array);
            
                $i=0;
                while  ($i< $arraylength){
                    
                    echo '<tr> 
                          
                    <td ><button type="button" name="WO" data-id="'.$BOM_array["$i"].'" class="btn_bom m" >'.$BOM_array[$i].'</button></td>
                            <td>' . $BOMinfo_array[$i].'</td> 
                           
                        </tr>';
                    $i++;
                }

                }
            
 
    ?>


</table>
 </div>