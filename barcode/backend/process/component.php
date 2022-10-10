<?php
 $check_key=FALSE;
include_once "../../conn.php";


$value=test_input($_POST["value"]);
$column=test_input($_POST["column"]);
$BOM =test_input($_POST["BOM"]);
$component=test_input($_POST["component"]);
$update=test_input($_POST["update"]);

$editable=false;

switch ($update) {
    case "component":


        if ($column=="component"){

            if ($value!=""){
    
            
    
               $sql="INSERT INTO BOM_component (BOM, component) VALUES ('$BOM', '$value');";
    
            }
        
           
        
            }
        else{
            $sql="UPDATE BOM_component  SET $column = '$value' WHERE ( BOM = '$BOM') and (component='$component') ;";
    
           echo $sql;
            
        
        }
        $query=$conn->query($sql);



        break;

    case "delete":

  
        $sql="DELETE FROM BOM_component WHERE (BOM = '$BOM') and (component='$component');";

        $query=$conn->query($sql);
    
    
        
        $sql="SELECT a.component,b.description,a.qty FROM BOM_component as a join component as b using (component) where a.BOM='$BOM'   ";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
            $componentInfo_array[]=$rows['description'];
            $qty_array[]=$rows['qty'];
            $component_array[]=$rows['component'];
                }     



            break;

    case "add":

            $editable="FALSE";
            $sql="INSERT INTO BOM_component (BOM, component) VALUES ('$BOM', '$component');";
            $query=$conn->query($sql);
            $sql="SELECT a.component,b.description,a.qty FROM BOM_component as a join component as b using (component) where a.BOM='$BOM'   ";
            $query=$conn->query($sql);
           if ($query!=null){
            while ($rows=$query->fetch_assoc()){
                $componentInfo_array[]=$rows['description'];
                $qty_array[]=$rows['qty'];
                $component_array[]=$rows['component'];
                    }     



           }


            break;

    }


    function comp_list(array $component_array){
    

        $arraylength=count($component_array);
     
        $i=0;
        while  ($i< $arraylength){
         echo '<option value="'.$component_array[$i].'">'. $component_array[$i].'</option>';        
            $i++;

        
        }

      }





?>



<div class="rightpane">

<table class="info" id="c_result" >






<?php



function component_input($column,$BOM,$component,$editable) {
    
    $result = ' <div style="color:grey" contenteditable="'.$editable.'"  onClick="activate(this)">';
echo $result;
    return $result;
}



    if (($update=="delete")||($update=="add"))
    {

        ?>
        

   
    <th>Component</th>
    <th class="s">Qty</th>
    <th>Description</th>
    
    <th class="xs"></th>


<?php
    if ($component_array!=null){
    $arraylength=count($component_array);
    $i=0;
            while  ($i<$arraylength) {
                echo '<tr> 
                    
                        <td>' .component_input("component",$BOM,$component_array[$i],$editable). $component_array[$i] . '</div>'.'</td> 
                        <td class="xs">' .component_input("qty",$BOM,$component_array[$i],$editable). $qty_array[$i] . '</div>'.'</td> 
                        <td>' .component_input("description",$BOM,$component_array[$i],"FALSE").$componentInfo_array[$i] .'</div>'. '</td> 
                        <td class="xs"><button type="button" name="c_delete" data-id="'.$component_array["$i"].'" class="btn btn-xs btn-warning cbtn_delete" >-</button></td> 
                    </tr>';
                $i++;
            }
}

}

//  

    ?>



</table>


</div>