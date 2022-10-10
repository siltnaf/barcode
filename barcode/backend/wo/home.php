<script src="js/jquery-3.6.0.js"></script>
<script>
        
        function activate(element){

                }



          function updateWO(element,column,WO){
         
          var value =element.innerText
          console.log(value+column+WO)
          $.ajax({
            url: "./backend/wo/wo.php",
            type: "post",
            data: {
                      value: value,
                      column: column,
                      WO: WO,
                      update: "WO"
            },
     
            success: function (result){
                console.log(result)
               
            }


          }); 
          }


          function updateWO_value(element,column,WO){
        
         var value =element.value
         console.log(value+column+WO)
       
         $.ajax({
           url: "./backend/wo/wo.php",
           type: "post",
           data: {
                     value: value,
                     column: column,
                     WO: WO,
                     update: "WO"
           },
    
           success: function (result){
               console.log(result)
              
           }


         }); 
         }


 











          $(document).ready(function()    {
   
  


                $("#w_delete").click(function() {
                    
                
                    $.ajax({type: "POST",
                    url: "./backend/wo/wo.php",




                    data: { update: "delete", WO: $("#WO").val()},
                    success:function(result) {
                        $('#result').html(result);
                    },
                   
                });

                });



             
 
 









          });





</script>











  <?php

//console.log(element.innerText+column+WO+station);

include_once "../../conn.php";
$editable="TRUE";


if ((isset($_POST["edit"]))&&(isset($_POST["WO"])!=0)){

 
   
    $WO=test_input($_POST["WO"]);
   
    $sql="SELECT WO FROM workorder WHERE WO= '$WO'";
    $query=$conn->query($sql);

    //if new WO , assign BOM and description to ""
    if (mysqli_num_rows($query)==0){    

                
              
                $editable="TRUE";
                
               
                
                $sql="INSERT INTO workorder (WO) VALUES ('$WO')";        
                $query=$conn->query($sql);

                $sql="INSERT INTO productionDetails (WO) VALUES ('$WO')";
                $query=$conn->query($sql);

               
        } else {


            //get all PO information 
                $editable="TRUE";
                $sql="SELECT * FROM workorder 
                        join BOM
                        using (BOM)
                        WHERE WO='$WO'";
                $query=$conn->query($sql);
                $rows=$query->fetch_assoc();
                $woinfo_new=$rows['description'];
                $fw_new=$rows['FW'];
                $hw_new=$rows['HW'];
                $sku_new=$rows['SKU'];
                $bom_new=$rows["BOM"];
                $po_new=$rows["PO"];
               
                $qty_new=$rows["qty"];

                }

            }


if (isset($_POST["WO"])) {
    

  //  $value=test_input($_POST["value"]);
  //  $column=test_input($_POST["column"]);
    $WO=test_input($_POST["WO"]);
   
  
    if (strlen($WO)>2){
      
        $sql="SELECT *  FROM workorder as a
        join productionDetails as b
        using (WO) 
        join BOM as c
        using (BOM)
        order by b.recordDate desc";
                 
               
        $query=$conn->query($sql);
        if ($query !=null)
        while ($rows=$query->fetch_assoc()){
            $wo_arr[]=$rows["WO"];
            $bom_arr[]=$rows["BOM"];
            $woinfo_arr[]=$rows["description"];
            $hw_arr[]=$rows["HW"];
            $fw_arr[]=$rows["FW"];
            $po_arr[]=$rows["PO"];
            $sku_arr[]=$rows["SKU"];
            $qty_arr[]=$rows["qty"];
            $lot_arr[]=$rows["LOT"];
            $sdate_arr[]=$rows["startDate"];
            $edate_arr[]=$rows["endDate"];
        }
        


        $sql="SELECT * FROM BOM";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
            $bom_list[]=$rows["BOM"];
            $bominfo_list[]=$rows["description"];
        }


 
                 
                

            }

            

            }   

 
 
            function show_list(array $bom){
    

                $arraylength=count($bom);
                
                $i=0;
                while  ($i< $arraylength){
                 echo '<option value="'.$bom[$i].'">'. $bom[$i].'</option>';        
                    $i++;
      
                
                }
      
              }






?>








           
<div style="overflow-y:scroll; height:400px;" > 

          
                   
                    <table class="info"  >
                 
                    <th class="m">WO</th>
                    <th class="l">Description</th>
                    <th class="m">BOM</th>
                    <th class="s">FW</th>
                    <th class="s">HW</th>
                    <th class="m">PO</th>
                    <th class="m">SKU</th>
                    <th class="s">Qty</th>
                    <th class="m">LOT</th>
                    <th class="xm">Manufacturing Date</th>
                    <th class="xm">Completion Date</th>
                    
                        <?php


                    function WO_input($column,$WO,$editable) {
                  
                        $result = '<div contenteditable="'.$editable.'" onBlur="updateWO(this,'."'".$column."','".$WO."'".')" onClick="activate(this)">';
                        
                        return $result;
                    }


                    if (isset($_POST['edit'])) {
                                                
                                                 
                                                echo
                                                '<th></th>
                                                <tr><td >'. $WO. '</td> 
                                                <td>' .wo_input("description",$WO,"False").$woinfo_new.'</div></td> ';?>
                                                <td> <select type="text"  onBlur="updateWO_value(this,'BOM','<?php echo $WO ?>')"   class="form-control BOM"  select=true> <option value=""><?php echo $bom_new ?></option> <?php show_list($bom_list);?></select></td>
                                                <?php echo
                                              
                                                '<td>' .wo_input("FW",$WO,$editable).$fw_new.'</div></td> 
                                                <td>' .wo_input("HW",$WO,$editable).$hw_new.'</div></td> 
                                                <td>' .wo_input("PO",$WO,$editable).$po_new.'</div></td> 
                                                <td>' .wo_input("SKU",$WO,$editable).$sku_new.'</div></td>
                                                <td>' .wo_input("qty",$WO,$editable).$qty_new.'</div></td>
                                                <td>' .wo_input("LOT",$WO,$editable).$lot_new.'</div></td>'?>
                                                <td><input type="date" name="startDate[]" class="form-control Prod.Date"  onBlur="updateWO_value(this,'startDate','<?php echo $WO ?>')" value="<?php echo date('Y-m-d') ; ?>" ></td>
                                                <td><div><?php echo $edate_new ?></div></td>
                                                <td><button id="w_delete" value="w_delete" name="w_delete">x</button></td>
                                              <?php



                    } elseif  ($wo_arr !=null){

                                   $arraylength=count($wo_arr);
                                   $i=0;
                                   
                                   while ($i<$arraylength){
                                        echo
                                        '<tr>
                                        <td ><button type="button" name="WO" data-id="'.$wo_arr["$i"].'" class="btn_wo m" >'.$wo_arr[$i].'</button></td> 
                                         <td >' . $woinfo_arr[$i].'</td>
                                         <td ><button type="button" name="WO" data-id="'.$bom_arr["$i"].'" class="btn_bom m" >'.$bom_arr[$i].'</button></td>
                                       
                                         <td>' . $fw_arr[$i].'</td>
                                         <td>' . $hw_arr[$i].'</td>
                                         <td>' . $po_arr[$i].'</td>
                                         <td>' . $sku_arr[$i].'</td>
                                         <td>' . $qty_arr[$i].'</td> 
                                         <td>' . $lot_arr[$i].'</td> 
                                         <td>' . $sdate_arr[$i].'</td> 
                                         <td>' . $edate_arr[$i].'</td> 
                                         </tr>'; 

                                    $i++;
                                   }
                                                 
                                               

                                    }
                             
                    
                        ?>


                    </table>
  

                     </div>

 
                     

  

                

 
 