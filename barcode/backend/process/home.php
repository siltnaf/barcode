<script src="js/jquery-3.6.0.js"></script>
<script>
        
        function activate(element){

                }



          function updateBOM(element,column,BOM){
          
          var value =element.innerText
          $.ajax({
            url: "./backend/process/bom.php",
            type: "post",
            data: {
                      value: value,
                      column: column,
                      BOM: BOM,
                      update: "BOM"
            },
     
            success: function (result){
                console.log(result); 
               
            }


          }); 
          }


          function updateQCI(element,column,BOM,station){
         
          var value =element.innerText;
          console.log(value+column+BOM+station);
          if (value=='') alert ('Empty cell');
           else {
            $.ajax({
            url: "./backend/process/station.php",
            type: "post",
            data: {
                      value: value,
                      column: column,
                      BOM: BOM,
                      station: station,
                      update: "station"
            },
            success: function (result){
              
    
            }

          });


           }
          
          

          }


          function updateComponent(element,column,BOM,component){
            
       var value =element.innerText
     //  console.log(value+column+BOM+component);
       $.ajax({
         url: "./backend/process/component.php",
         type: "post",
         data: {
                   value: value,
                   column: column,
                   BOM: BOM,
                   component: component,
                   update: "component"
         },
         success: function (result){
        
 
         }

       });
       

       }











          $(document).ready(function()    {
   
  
                    var div=document.getElementById("bom");
                   var BOM=div.textContent;

                $("#w_delete").click(function() {
                    
                    console.log("w");
                    $.ajax({type: "POST",
                    url: "./backend/process/bom.php",




                    data: { update: "delete", BOM: BOM},
                    success:function(result) {
                        $('#w_result').html(result);
                    },
                   
                });

                });



                $(document).on('click','.btn_delete',function() {

                    var div=document.getElementById("bom");
                   var BOM=div.textContent;
                    var id=$(this).data("id");
                    console.log(id);

                    $.ajax({type: "POST",
                    url: "./backend/process/station.php",

                    data: { update: "delete", station: id,BOM:BOM},
                    success:function(result) {
                       $("#q_result").html(result);
                        
                        
                    }
                });

                });



                $(document).on('click','.cbtn_delete',function() {
                    event.preventDefault();
                             
                    var id=$(this).data("id");
                    var div=document.getElementById("bom");
                   var BOM=div.textContent;

                    $.ajax({type: "POST",
                    url: "./backend/process/component.php",

                    data: { update: "delete", component: id,BOM:BOM},
                    success:function(result) {
                        $('#c_result').html(result);
                  
                        
                        
                    }
                    });

                    });


               

                    $('#insert_comp').on('click', function(event){
                        event.preventDefault();
                             
                   
                   var div=document.getElementById("bom");
                   var BOM=div.textContent;
                    
                   var id=$('#insert').val();
                   console.log(id);
                    $.ajax({
                        url:"./backend/process/component.php",
                        method:"POST",
                        data: {component:id, update:"add", BOM:BOM},
                        success:function(result)
                        {
                        
                            console.log(result);
                        
                        $('#c_result').html(result);
                        }
                    });
                });
    

                $('#insert_station').on('click', function(event){
                        event.preventDefault();
                    
                   
                  
                 
                   var id=$('#insert_s').val();
                   var div=document.getElementById("bom");
                   var BOM=div.textContent;
                   console.log(id+BOM);
              
                    $.ajax({
                        url:"./backend/process/station.php",
                        method:"POST",
                        data: {station:id, update:"add", BOM:BOM},
                        success:function(result)
                        {
                        
                     
                        $('#q_result').html(result);
                        }
                    });
                });
                 









          });





</script>











  <?php

//console.log(element.innerText+column+BOM+station);

include_once "../../conn.php";
$editable="TRUE";


if ((isset($_POST["edit"]))&&(isset($_POST["BOM"])!=0)){

   
   
    $BOM=test_input($_POST["BOM"]);

    $sql="SELECT BOM FROM BOM WHERE BOM= '$BOM'";
    $query=$conn->query($sql);

    //if new BOM , assign BOM and description to ""
    if (mysqli_num_rows($query)==0){    
                $editable="TRUE";
               
                $BOMinfo_new="";
               
                $sql="INSERT INTO BOM (BOM, description) VALUES ('$BOM', '$BOMinfo_new')";
                $query=$conn->query($sql);
        } else {

                $editable="TRUE";
                $sql="SELECT * FROM BOM WHERE BOM='$BOM'";
                $query=$conn->query($sql);
                $rows=$query->fetch_assoc();
                $BOMinfo_new=$rows['description'];
                
                
                $sql="SELECT a.*,b.description FROM BOM_station as a join station as b using (station) where a.BOM='$BOM'  order by a.stationOrder asc   ";
                $query=$conn->query($sql);
                while ($rows=$query->fetch_assoc()){
                    $station_array[]=$rows['station'];
                    $stationOrder_array[]=$rows['stationOrder'];
                    $stationInfo_array[]=$rows['description'];
                    $stationQR_array[]=$rows['QRcomponent'];
                    $stationCount_array[]=$rows['componentCount'];
                    }
        
        
                $sql="SELECT a.component,b.description,a.qty FROM BOM_component as a join component as b using (component) where a.BOM='$BOM'   ";
                $query=$conn->query($sql);
                if ($query !=null){
                    while ($rows=$query->fetch_assoc()){
                        $componentInfo_array[]=$rows['description'];
                        $qty_array[]=$rows['qty'];
                        $component_array[]=$rows['component'];
                            }     

                }
                









                    }
        
            

                    $sql="SELECT * FROM component  ";
                    $query=$conn->query($sql);
                    while ($rows=$query->fetch_assoc()){
                        $comp_arr[]=$rows['component'];
                        $compInfo_arr[]=$rows['description'];

                    }
                    

                    $sql="SELECT * FROM station   ";
                    $query=$conn->query($sql);
                    while ($rows=$query->fetch_assoc()){
                        $station_arr[]=$rows['station'];

                        $stationInfo_arr[]=$rows['description'];

                    }
                    






            }


if (isset($_POST["BOM"])) {
    

    $value=test_input($_POST["value"]);
    $column=test_input($_POST["column"]);
    $BOM=test_input($_POST["BOM"]);
  
    if (strlen($BOM)>2){
        

        $sql="SELECT * FROM BOM  ";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
            $BOM_array[]=$rows['BOM']; 
        
            $BOMinfo_array[]=$rows['description'];
          
                }


            }



            }   



            function comp_list(array $comp_arr){
    

                $arraylength=count($comp_arr);
             
                $i=0;
                while  ($i< $arraylength){
                 echo '<option value="'.$comp_arr[$i].'">'. $comp_arr[$i].'</option>';        
                    $i++;
      
                
                }
      
              }

              function station_list(array $station_arr){
    

                $arraylength=count($station_arr);
             
                $i=0;
                while  ($i< $arraylength){
                 echo '<option value="'.$station_arr[$i].'">'. $station_arr[$i].'</option>';        
                    $i++;
      
                
                }
      
              }







?>








<body class="container">



            <div class="rightpane" style="overflow-y:scroll; height:400px;" >
                    
                    <p>
                    <table class="info" id="w_result" >
                    
                    <th class="m">BOM</th>
                    <th class="l">Description</th>
               

                        <?php


                    function BOM_input($column,$BOM,$editable) {
                  
                        $result = '<div contenteditable="'.$editable.'" onBlur="updateBOM(this,'."'".$column."','".$BOM."'".')" onClick="activate(this)">';
                        
                        return $result;
                    }


                    if (isset($_POST['edit'])) {
                        

                        echo ' <th class="xs"></th><tr> 
                        <td><div id="bom">' . $BOM.'</div></td> 
                      
                        <td >' .BOM_input("description",$BOM,$editable). $BOMinfo_new.'</div></td> 
                    
                        <td class="xs" ><button id="w_delete" value="w_delete" name="w_delete">x</button></td>
                    </tr>';




                    } elseif  ($BOM_array !=null){

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

 
                    <div class="middlepane" style="overflow-y:scroll; height:400px;" >

                    
                    
                    
                        <?php
                    
                    
                    
                        function QCI_input($column,$BOM,$station,$editable) {
                            
                        
                            $result = '<div contenteditable="'.$editable.'" onBlur="updateQCI(this,'."'".$column."','".$BOM."','".$station."'".')" onClick="activate(this)">';
                            echo $result;
                            return $result;
                        }
                        
                    
                    
                    
                        if (isset($_POST['edit']))
                        {

                            ?>
                            <div class="info2"><select  id="insert_s" type="text" name="station_arr[]" class="form-control station_arr" /><option value="">==station==</option><?php echo station_list($station_arr); ?></select>
                            <button type="button" id="insert_station"  value="add" name="add" class="btn btn-success btn-number">+</button>
                           </div>
                            <table class="info" id="q_result"  >
                            <th class="m">station</th>
                            <th class="s">Order</th>
                            <th class="l">Description</th>
                            <th class="m">Key QRcode</th>
                            <th class="s">Scan no.</th>
                            <th class="xs"></th>

                        <?php

                        if ($station_array!=null){

                            $arraylength=count($station_array);
                    
                        
                            $editable=false;
                            $i=0;
                            while   ($i<$arraylength){
                                echo
                                '<tr id=update_info> 
                                    
                                        <td>' .QCI_input("station",$BOM,$station_array[$i],$editable). $station_array[$i].'</div></td> 
                                        <td class="s">' .QCI_input("stationOrder",$BOM,$station_array[$i],$editable). $stationOrder_array[$i].'</div></td> 
                                        <td>' .QCI_input("description",$BOM,$station_array[$i],"FALSE").$stationInfo_array[$i].'</div></td> 
                                        <td>' .QCI_input("QRcomponent",$BOM,$station_array[$i],$editable).$stationQR_array[$i].'</div></td>
                                        <td>' .QCI_input("componentCount",$BOM,$station_array[$i],$editable).$stationCount_array[$i].'</div></td> 
                                        <td class="xs"><button type="button" name="q_delete" data-id="'.$station_array["$i"].'" class="btn btn-xs btn-danger btn_delete">x</button></td> 
                                        
                        
                                    </tr>';
                                $i++;
                            }

                            


                        }

                      
                    }
                    
                        
                    
                        ?>
                    
                    
                    </table>

                </div>


 


                    <div class="leftpane" style="overflow-y:scroll; height:400px;" >

                        <table class="info" id="c_result" >





                        
                        <?php



                        function component_input($column,$BOM,$component,$editable) {
                         
                            $result = ' <div contenteditable="'.$editable.'" onBlur="updateComponent(this,'."'".$column."','".$BOM."','".$component."'".')" onClick="activate(this)">';
                        echo $result;
                            return $result;
                        }
                        


                            if(isset($_POST['edit']))
                            {

                                ?>
                                

                            <div ><select id="insert" type="text" name="comp_arr[]" class="form-control comp_arr" /><option value="">==component==</option><?php echo comp_list($comp_arr); ?></select>
                            <button type="button" id="insert_comp"  value="add" name="add" class="btn btn-success btn-number">+</button>
                            </div>
                           
                            <th>Component</th>
                            <th class="s">Qty</th>
                            <th>Description</th>
                            <th class="xs"></th>
                        

                        <?php

                            if ($component_array!=null){

                                $editable=false;   
                            $arraylength=count($component_array);
                            $i=0;
                                    while  ($i< $arraylength) {
                                        echo '<tr> 
                                            
                                                <td>' .component_input("component",$BOM,$component_array[$i],$editable). $component_array[$i] . '</div>'.'</td> 
                                                <td class="xs">' .component_input("qty",$BOM,$component_array[$i],$editable). $qty_array[$i] . '</div>'.'</td> 
                                                <td>' .component_input("description",$BOM,$component_array[$i],"FALSE").$componentInfo_array[$i] .'</div>'. '</td> 
                                                <td class="xs"><button type="button" name="c_delete" data-id="'.$component_array["$i"].'" class="btn btn-xs btn-warning cbtn_delete" >x</button></td> 
                                            </tr>';
                                        $i++;
                                    }
                        }

                    }
                        
                      //  

                            ?>

                        
                        
                        </table>

              
                    </div>

                    <body>


 
 