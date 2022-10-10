
<script src="js/jquery-3.6.0.js"></script>

<script>

function activate(element){

}

 
$(document).on('click','.btn_part',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                   
                    var id=$(this).data("id");
                    var pid=$(this).data("pid");
                    var layer=$(this).data("layer");

                    console.log(id);
                    $.ajax({type: "POST",
                    
                    url: "./backend/record/view.php",

                    data: { QRcode: id, pid:pid, layer:layer},
                    success:function(result) {
                        $('#s_table').html(result);
                  
                        
                        
                    }
                    });

                    });





</script>





  <?php

include_once "../../conn.php";
$editable="TRUE";


 

function query_down($last_sql)
{
  
 $sql=" SELECT b.part,b.layer from ($last_sql  ) as a
  join assembly as b
 on a.part=b.QRcode and a.part!=b.part";


  return $sql;
}








  $check_key=FALSE;

 
  
  $QCI_station=0;
  $BND_station=0;
  $material=0;
  
  
  //search down the tree

  if (isset($_POST["QRcode"])){

          
        
          $QRcode=test_input($_POST["QRcode"]);
          $previous_layer=test_input($_POST["layer"]);

     
       
          
          // check assembly if assembly exist
          
           $current_layer=$previous_layer-1;
        
          
            $BND_station=1;
          }   
          

     

         


          // check if component register in QCI_station 

          $sql="SELECT * FROM workflow where QRcode='$QRcode';";
          $query=$conn->query($sql);    
          if ($query->num_rows>0) $QCI_station=1;
          
      

  
 
  



 

  if ($BND_station==1){

  
    //find the WO from pid
    
  //  $sql="SELECT WO from QRcode where QRcode='$QRcode'";
   // $query=$conn->query($sql);
    
   // while ($rows=$query->fetch_assoc()) { 
     //   $WO=$rows["WO"];}
 

      

    
/*
  //find the production date / BOM, description 
    $sql="SELECT a.BOM,b.description from workorder as a 
           join BOM  as b
           using (BOM)
          where a.WO='$WO';";
    $query=$conn->query($sql);
    while ($rows=$query->fetch_assoc()) { 
    $BOM=$rows["BOM"];
    $description=$rows["description"];

    }
  
 */
 

// find all component associate with the top QRcode
  $part[0]=$QRcode;
  $sql="SELECT part FROM assembly where (QRcode='$QRcode') and (layer='$current_layer') ";
  $query=$conn->query($sql);
      if ($query->num_rows>0){   
      while ($rows=$query->fetch_assoc()) 
            $part[]=$rows['part'];

           

       
      }


  
    

     




  } else {

    $part[0]=$QRcode;
  }


  // find the BOM
            $sql="SELECT * FROM QRcode as a 
                  join workorder  as b
                  using (WO)
                  where a.QRcode='$QRcode'";
              $query=$conn->query($sql);
              while ($rows=$query->fetch_assoc()){
                $BOM=$rows['BOM'];
              }


             

            //find QCI from BOM
            $sql="SELECT a.station,b.description FROM BOM_station as a
                  join station as b
                  using (station) 
                  where a.BOM='$BOM'
                  order by a.stationOrder asc;";

            $query=$conn->query($sql);
            while ($rows=$query->fetch_assoc()){
                if (strpos('0'.$rows['station'],"QC")!=false){

                  $station_array[]=$rows['station'];
                  $stationInfo_array[]=$rows['description'];
                }    //only save QCI station and ignore BND station


            }
            
          
            
            foreach($part as $key=>$value)
            {

              $sql="SELECT * FROM workflow 
                      where QRcode='$QRcode'";
              $query=$conn->query($sql);
              while ($rows=$query->fetch_assoc()){

              $wip_station[]= $rows['station'];
              $wip_operator[]=$rows['operator'];
              $wip_result[]=$rows['result'];
              
              }
                //only save QCI station and ignore BND station */


            }
 






  

 


 

   

    
    $sql="SELECT a.component,b.description FROM BOM_component as a 
            join component as b 
            using (component)   
            where a.BOM='$BOM' ";
    $query=$conn->query($sql);
    while ($rows=$query->fetch_assoc()){
        $componentInfo_array[]=$rows['description'];
        $component_array[]=$rows['component'];
            }     


            

   






 
 




  





// search without edit

  


  ?>
<div>
<span class="halfpane info_w" >
 
<div >
<h2 style="text-align: center">
<div>
QRcode(

<?php
echo $QRcode.")";

?>
</div>
Component Inside
</h2>
     
<table class="center"  >





<th class="xm">Component code</th>
<th class="l">Description</th>
<th class="xl">Scanned components</th>

<?php









     
  
    
         
           
           $comp_arr=$component_array;
           $compinfo_arr=$componentInfo_array;
           
          
         
          
     
          
         if ($comp_arr!=null){
          $part=array_unique($part);
          
          $comp_arr = array_unique($comp_arr);
          $compinfo_arr = array_unique($compinfo_arr);
         
            $i=0;
            while ($i< count($comp_arr)){
    
          
            if ($part!=null)foreach ($part as $key=>$value) if  (substr_count($value, $comp_arr[$i])==1) { 

              echo '<tr> 
                
              <td>' . $comp_arr[$i].'</td> 
             
              <td>' .$compinfo_arr[$i].'</td><td>';



              echo   '<button type="button"  name="CO" data-id="'.$value.'"  data-layer="'.$current_layer.'" data-pid="'.$pid.'"    class="btn_co xl btn_part" >'. $value.'</br></button>';

            
             
               
             }
             echo '</td>';
             echo '</tr>';
     
             $i++;
     
            }
 


         }
         

  
     ?>


</table>

</div>
</span>
         


















 </div>