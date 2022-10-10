<script src="js/jquery-3.6.0.js"></script>

<script>
    
                 

                    $(document).on('click','.btn_right',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                    
                    var id=$(this).data("id");
                    var layer=$(this).data("layer");
                    var pid=$(this).data("pid");
                 
                   
                    $.ajax({type: "POST",
                     
                    url: "./backend/record/child.php",

                    data: { QRcode: id,layer:layer,pid:pid},
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
              

                    $(document).on('click','.btn_pview',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                   
                    var id=$(this).data("id");
                    var pid=$(this).data("pid");
                    var layer=$(this).data("layer");

                    $.ajax({type: "POST",
                    
                    url: "./backend/record/view.php",

                    data: { QRcode: id,layer:layer,pid:pid},
                    success:function(result) {
                        $('#s_table').html(result);
                  
                        
                        
                    }
                    });




                    });





</script>





  <?php

include_once "../../conn.php";
$editable="TRUE";


 








  $check_key=FALSE;

  $current_date="%".date("Y\-m\-d")."%";
  
  $QCI_station=0;
  $BND_station=0;
  $material=0;


  //search down the tree

  if (isset($_POST["QRcode"])){



             
        
          $QRcode=test_input($_POST["QRcode"]);
          $previous_pid=test_input($_POST["pid"]);
          $previous_layer=test_input($_POST["layer"]);

      

      
          $maxlevel=0;
          $current_layer=$previous_layer+1;
          
     

         
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

          if (($BND_station==0)&&($QCI_station==0)&&($material==0)) {alert ("QRcode not exist");die;} 
          
        
         

          $sql="SELECT layer from assembly where part='$QRcode'";  
          $query=$conn->query($sql);
          while ($rows=$query->fetch_assoc()) {
           
            $level[]= $rows["layer"];}
            $level=array_unique($level);
           
     
         
              //find two level up the tree
          
              if (max($level)<$current_layer){
                $sub_sql= "SELECT QRcode from  assembly where part='$QRcode' and part!=QRcode";
                $sql= "SELECT b.QRcode from ($sub_sql) as a  
                join assembly as b
                on (b.part=a.QRcode) and (b.QRcode !=a.QRcode)";

              }
              else {

                $sql= "SELECT QRcode from  assembly where part='$QRcode' and part!=QRcode";


              }
 


 
 
         
          $query=$conn->query($sql);
          if ($query->num_rows>0){

            while ($rows=$query->fetch_assoc()) {
           
                $top_QRcode= $rows["QRcode"];}
          
         
         
          
          
           // find one level down the tree

           $subsql="SELECT part from assembly where QRcode='$top_QRcode' and part!=QRcode";
           $sql= "SELECT b.QRcode from ($subsql) as a  
           join assembly as b
            on (b.QRcode=a.part)";
           $query=$conn->query($sql);
           if ($query->num_rows>0) {                     
               while ($rows=$query->fetch_assoc()) {

             $next_QRcode[]= $rows["QRcode"];}

             }
             $next_QRcode=array_unique($next_QRcode);
          
        

           
  
/*
             
              }
       
*/
              
            } else 
 // if two level up is empty then one level up is top , 

            {
             
              $sql= "SELECT QRcode,production_id from assembly where part='$QRcode' and part!=QRcode  ";  
              $query=$conn->query($sql);
              if ($query->num_rows>0) {
                while ($rows=$query->fetch_assoc()) {
             
              $next_QRcode[]= $rows["QRcode"];}
  
              $maxlevel=1;
                }


            }
          
          }
          
      
          
            
       
   
            foreach ($next_QRcode as $key=>$value){
               
              //find the subassembly layer
 
            

               $sql="SELECT a.production_id,b.result from assembly as a
                      join QRcode as b
                      using (QRcode)
                      where a.QRcode='$value' and a.layer='$current_layer'";
                $query=$conn->query($sql);
                if ($query->num_rows>0) while ($rows=$query->fetch_assoc()){
                 
                  $pid[$key][]=$rows["production_id"];
                  $result[$key]=$rows["result"];
                  }
                  
                 
  
                   if ($pid!=null){


                      $pid[$key]=array_unique($pid[$key]);

                      foreach($pid[$key] as $k=>$id_value){
                        $sql="SELECT a.startDate, b.BOM,b.PO,b.SKU, c.description,a.WO,a.endDate  
                        FROM productionDetails as a
                        join workorder as b
                        using (WO)
                        join BOM  as c
                        using (BOM)
                        where a.production_id='$id_value';";
                        $query=$conn->query($sql);
                        if ($query->num_rows>0) while ($rows=$query->fetch_assoc()) { 
                        $WO[$key][]=$rows["WO"];
                        $sdate[$key][]= $rows["startDate"];
                        $po[$key][]=$rows["PO"];
                        $sku[$key][]=$rows["SKU"];
                        $BOM[$key][]=$rows["BOM"];
                        $description[$key][]=$rows["description"];
                        $edate[$key][]=$rows["endDate"];
                        }
  





                   }
                  
                  
                  
                   

                    
                     

                    }

               

                    
                    }
                    
          

          
                    $sql="SELECT a.startDate, b.BOM, b.PO,b.SKU,c.description,a.WO,a.endDate  
                    FROM barcode.productionDetails as a
                    join barcode.workorder as b
                    using (WO)
                    join BOM as c
                    using (BOM)
                    where a.production_id='$pid[$key]';";
                    $query=$conn->query($sql);
                    if ($query->num_rows>0) while ($rows=$query->fetch_assoc()) { 
                    $WO[$key][]=$rows["WO"];
                    $sdate[$key][]= $rows["startDate"];
                    $BOM[$key][]=$rows["BOM"];
                    $po[$key][]=$rows["PO"];
                    $sku[$key][]=$rows["SKU"];
                    $description[$key][]=$rows["description"];
                    $edate[$key][]=$rows["endDate"];
                    }
                  
           


              

    


                

                
         
          /*

                    // already  one layer from top level
                if ($maxlayer==1)  {

                    
                    $top_level=1;
                    
                $sql="SELECT QRcode,production_id from assembly where part='$QRcode' and QRcode!='$QRcode'";


                


                  $query=$conn->query($sql);
                  if ($query->num_rows>0) {
                    while ($rows=$query->fetch_assoc()){
                        $next_QRcode[]=$rows["QRcode"];
                        $pid[]=$rows["production_id"];}

              
                        foreach ($next_QRcode as $key=>$value){

                          // find production id,WO,BOM,
                        
                            $sql ="SELECT result from QRcode where QRcode='$value'";
                            $query=$conn->query($sql);
                            if ($query->num_rows>0) while ($rows=$query->fetch_assoc()){
                              $result[$key]=$rows["result"];}
            
            
                              $sql="SELECT a.startDate,a.endDate, b.BOM, b.description,a.WO  
                              FROM barcode.productionDetails as a
                              join barcode.workorder as b
                              using (WO)
                              where a.production_id='$pid[$key]';";
                              $query=$conn->query($sql);
                              if ($query->num_rows>0) while ($rows=$query->fetch_assoc()) { 
                              $WO[$key]=$rows["WO"];
                              $sdate[$key]= $rows["startDate"];
                              $BOM[$key]=$rows["BOM"];
                              $description[$key]=$rows["description"];
                              $edate[$key]=$rows["endDate"];
                              
                              }

                            }
                         }


                   

                  }
  
                         
            
*/
            
         
         



               

 


// search without edit

  


  ?>



<html>
 



</br>

<div class="leftpane">
  <div class="info_s">
<h2 style="text-align: center">
Subassembly level <?php echo $current_layer ?> 
 
</h2>

</div>

 <?php


 
if ($next_QRcode!=null)
  
foreach ($next_QRcode as $i=>$value) {

 
  if ($WO!=null)  foreach ($WO[$i] as $j=>$WO_value){


// from WO value find the corresponding pid
$sql="SELECT production_id from productionDetails where WO='$WO_value'";
$query=$conn->query($sql);
if ($query->num_rows>0) while ($rows=$query->fetch_assoc()) { 
$next_id=$rows["production_id"];}



    echo ' <table class="info" >
          <tr ><th class="m">QRcode:</th>
          <td class="l" ><div id="uid" contenteditable=FALSE data-id="'.$value.'" >'.$value.'</div></td>
          <td class="m">';  
          if ($maxlevel==0) echo '<button type="submit" name="left" data-id="'.$value.'"  data-pid="'.$next_id.'"  data-layer="'.$current_layer.'"  class="xs btn btn-xs btn-warning btn_left" style="float:left"><</button>';
          echo '<button type="submit" name="right"  data-id="'.$value.'" data-pid="'.$next_id.'" data-layer="'.$current_layer.'" class="xs btn btn-xs btn-warning btn_right" style="float:right">></button>';
      echo '</td>
          <tr ><th>WO:</th>
          <td class="m">'. $WO_value.'</td> 
          
     
           
         </tr>
         <tr><th>BOM:</th>
         <td >'.$BOM[$i][$j].'</td>
         </tr>
         <tr><th>Description:</th>
         <td>'.$description[$i][$j].'</td>
         </tr>

         <tr><th>Manufacturing Date:</th>
         <td>'.$sdate[$i][$j].'</td>
         </tr>
         <tr><th>Completion Date:</th>
         <td>'.$edate[$i][$j].'</td>
         </tr>
         
         <tr><th>Result</th>
         <td>'.$result[$i].'</td>
         </tr></table> </br><p>';
   

  }

}


?>

</div>



<div class="rightall" id="s_table" >


</div>







</html>




  
  
