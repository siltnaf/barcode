<script src="js/jquery-3.6.0.js"></script>

<script>
    
                 

                    $(document).on('click','.btn_right',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                    
                    var id=$(this).data("id");
                    var layer=$(this).data("layer");
                    var pid=$(this).data("pid");
                  
                    console.log(layer)
                    $.ajax({type: "POST",
                      
                    url: "./backend/record/child.php",

                    data: { QRcode: id, layer:layer,pid:pid
                    },
                    success:function(result) {
                     
                        $('#result').html(result);
                  
                        
                        
                    }
                    });




                    });


                    $(document).on('click','.btn_left',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                    var id=$(this).data("id");
                    var layer=$(this).data("layer");
                    var pid=$(this).data("pid");
                 
                    console.log(id)

                    $.ajax({type: "POST",
                    
                    url: "./backend/record/parent.php",

                    data: { QRcode: id,layer:layer,pid:pid},
                    success:function(result) {
                      
                        $('#result').html(result);
                  
                        
                        
                    }
                    });




                    });
            


            /*        $(document).on('click','.btn_cview',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                   
                    var id=$(this).data("id");
                    var layer=$(this).data("layer");
                    var pid=$(this).data("pid");
                  
                    $.ajax({type: "POST",
                    
                    url: "./backend/record/view.php",

                    data: { QRcode: id,layer:layer,pid:pid},
                    success:function(result) {
                     
                        $('#s_table').html(result);
                  
                        
                        
                    }
                    });




                    });

                      */



</script>





  <?php

include_once "../../conn.php";
$editable="TRUE";


 


function query_down($last_sql)
{
  
 $sql=" SELECT b.* from ($last_sql  ) as a
  join assembly as b
 on a.part=b.QRcode ";


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
          
      

      //    $level=test_input($_POST["layer"]);
  
         
        
       // check assembly if assembly exist
       
         
            $BND_station=1;
             
          // check if component register in QCI_station 

          $sql="SELECT * FROM workflow where QRcode='$QRcode';";
          $query=$conn->query($sql);    
          if ($query->num_rows>0) $QCI_station=1;

         //check if the component exist in material table   
          $sql="SELECT * from material where part='$QRcode';";
          $query=$conn->query($sql);
          if ($query->num_rows>0) $material=1;

         
          
         //find one level below the QRcode in the assembly level

         
          
          if ($BND_station==1){

          $last_sql="SELECT * from assembly where QRcode='$QRcode'  ";

          $query=$conn->query($last_sql);
          
          if (mysqli_num_rows($query)!=0){
            
            
            // find result
            while ($rows=$query->fetch_assoc()) {

            $key=$rows['QRcode'];
            $layer=$rows['layer'];
            $part[$key][$layer][]= $rows['part'];
            
           
           
          }

          $sql="SELECT part FROM materialLot where QRcode='$key' ";
          $query=$conn->query($sql);
          if (mysqli_num_rows($query)!=0){   
            while ($rows=$query->fetch_assoc()) {
                  $part[$key][$layer][]=$rows['part'];
            }
          }   
         
   
   

        $end=0;
          while  ($end==0)  {
         
            $sql=query_down($last_sql);
            
            $query=$conn->query($sql);
              if (mysqli_num_rows($query)!=0){   
              while ($rows=$query->fetch_assoc()) {
                $key=$rows['QRcode'];
                $layer=$rows['layer'];
                $part[$key][$layer][]= $rows['part'];
              }

              $sql="SELECT part FROM materialLot where QRcode='$key' ";
              $query=$conn->query($sql);
              if (mysqli_num_rows($query)!=0){   
                while ($rows=$query->fetch_assoc()) {
                      $part[$key][$layer][]=$rows['part'];
                }
              }   


                    if ($layer>1){
                      

                      $last_sql=$sql;
                    } else {$end=1;}
          
                   
                   
          
              } else {
                
              
                $end=1;}
              }


             
    
            // insert QRcode to part 

            foreach ($part as $key=>$info){
             
              foreach ($info as $level=>$value){

                $part[$key][$level][]=$key;
               

               
              }


            }

            //flatten 3D array to 2D
            foreach ($part as $key=>$info){
             
              foreach ($info as $level=>$value){

               
               foreach ($value as $x){

                  $partx[$level][] =$x;
                  
                }

                $partx[$level]=array_unique($partx[$level]);
              }


            }


        





                    // check the subassembly layer
                }






               
                


                   // find the BOM              
                   $sql="SELECT * FROM barcode.QRcode
                   join workorder
                   using (WO) 
                   join BOM
                   using (BOM)
                   where QRcode='$QRcode'";
                       
                   $query=$conn->query($sql);
                   while ($rows=$query->fetch_assoc()) { 
 
                
                   $BOM=$rows["BOM"];
                  
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
             

                   // find the BOM              
                  $sql="SELECT * FROM barcode.QRcode
                  join workorder
                  using (WO) 
                  join BOM
                  using (BOM)
                  where QRcode='$QRcode'";
                      
                  $query=$conn->query($sql);
                  while ($rows=$query->fetch_assoc()) { 

               
                  $BOM=$rows["BOM"];
                 
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
            
            
                // remove the ",Lot" character from the component list                       
                if ($component_array!=null) foreach ($component_array as $key=>$value)
                {
                if (strpos($value, ',')!=0){
                    $component_array[$key]=substr($value,0,(strpos($value,',')));


                };

                }






                         
            } 

            
         
         



  }





// search without edit

  


  ?>



<html>
 






<?php

for ($j=$previous_layer; $j>0;$j--){
?>



  <div class="info_s">

Layer <?php echo $j ?> 


</div>


<table class="center"  >

<th class="xm">Component code</th>
<th class="l">Description</th>
<th class="l">Scanned components</th>

<?php
      
      
          
        
          
       
           $comp_arr=array_unique($component_array);

           foreach($comp_arr as $key=>$value){
            if ($value==$component_array[$key]) {$compinfo_arr[$key]=$componentInfo_array[$key];}


           }

          
         
         if ($comp_arr!=null){
       //   $partx=array_unique($partx);

         
         
            $i=0;
            while ($i< count($comp_arr)){
    
            
            if ($partx!=null)foreach ($partx as $level=>$value) foreach ($value as $value) 
            
            if  ((substr_count($value, $comp_arr[$i])==1)and ($level==$j)){ 

              echo '<tr> 
                
              <td>' . $comp_arr[$i].'</td> 
             
              <td>' .$compinfo_arr[$i].'</td><td>';
             
 
            
 
             // if $value find in QRcode table, then use buttton , otherwise no select button is enable
             $sql="SELECT * from QRcode where QRcode='$value'";
             $query=$conn->query($sql);
             if ($query->num_rows!=0) {   

              echo   '<button type="button"  name="CO" data-id="'.$value.'"  data-layer="'.$j.'" class="btn_co xl btn_part" >'. $value.'</br></button>';

             }else {

              echo $value;

             }
             
             
             
             
               
             }
             echo '</td>';
             echo '</tr>';
     
             $i++;
     
            }
 


         }
         

  
     ?>


</table>
















<?php
}
?>














</html>




  
  
