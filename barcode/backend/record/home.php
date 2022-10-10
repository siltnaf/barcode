
<script src="js/jquery-3.6.0.js"></script>

<script>
function activate(element){

}




$(document).ready(function() {


  

   $("#right").click(function(e) {
    e.preventDefault();
    var element = document.getElementById('uid');
    var uid = element.getAttribute('uid');
    var WO=element.getAttribute('WO');
    var layer=element.getAttribute('layer');

        if (uid!=null){

        $.ajax({type: "POST",
        url: "./backend/record/child.php",
        data: { QRcode: uid ,WO: WO, layer: layer},
        success:function(result) {
         console.log(uid);
          $('#s_table').html(result);
        },
        error:function(result) {
        alert('error');
        }
        });
        } 

   
   
    });

  
  


  
});


$(document).on('click','.btn_view',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                   
                    var uid=$(this).data("id");
                    var WO=$(this).data("value");
                
                    $.ajax({type: "POST",
                    
                    url: "./backend/record/view.php",

                    data: { QRcode: uid,WO:WO},
                    success:function(result) {
                      console.log(result);
                        $('#s_table').html(result);
                  
                        
                        
                    }
                    });




                    });



 $(document).on('click','.btn_bpart',function(event) {
                    event.preventDefault();
                    
                    event.stopImmediatePropagation();
                   
                    var id=$(this).data("id");
                    var layer=$(this).data("layer");

             
                    $.ajax({type: "POST",
                    
                    url: "./backend/record/view.php",

                    data: { QRcode: id,  layer:layer },
                    success:function(result) {
                        $('#s_table').html(result);
                  
                        
                        
                    }
                    });




                    });

 

</script>





  <?php

include_once "../../conn.php";
$editable="TRUE";



function query_up($check_sql,$check_QRcode)
{
  $sql= "SELECT b.QRcode from ($check_sql) as a  
          join assembly as b
          on (b.part='$check_QRcode' and b.QRcode!='$check_QRcode') ";

  return $sql;
}



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

  if (isset($_POST["QRcode"])){

          
          $QRcode=test_input($_POST["QRcode"]);

          

          // check if QRcode is used in workflow table, 
          $sql="SELECT * FROM workflow where QRcode='$QRcode';";
        
          $query=$conn->query($sql);
          
         // if there is record, set the QCI_station flag to 1
    
          if (mysqli_num_rows($query)!=null) {
            while ($rows=$query->fetch_assoc()) {
       
            $QCI_station=1;
          }
        }
   

        // check if QRcode exist in assembly table
          $sql="SELECT * FROM assembly where QRcode='$QRcode';";
      
          $query=$conn->query($sql);
 
        // if there is record, set BND_station flag to 1
          if (mysqli_num_rows($query)!=null) {
            while ($rows=$query->fetch_assoc()) {
    
            $BND_station=1;
          }

          }

            $sql="SELECT * FROM material where part='$QRcode'";

            $query=$conn->query($sql);
        
            if (mysqli_num_rows($query)!=null) {
            while ($rows=$query->fetch_assoc()) {

            $material=1;
            }
            }
 

      

            if ($material==1){

              $childcode=$QRcode;
          
          
          
              $last_sql="SELECT QRcode from assembly where part='$childcode' and QRcode!='$childcode'";
              $query=$conn->query($last_sql);
              if ($query!=null){
                          while ($rows=$query->fetch_assoc()) 
                              $parentcode=$rows['QRcode'];
                          }
                    
              // if $childcode is empty QRCode is top of the parent
      
              
              $end=0;
              $skip_search=0;
          
              if ($parentcode==null) {
                $skip_search=1;
                $end=1;
                $Topcode=$childcode;
           
              } else $Topcode=$parentcode;
                          
    
           
             $childcode=$parentcode;

              
              $n=0;
            while  ($end==0)  {
                     
                      $sql=query_up($last_sql,$childcode);
                      $query=$conn->query($sql);
                     
                      if ($query!=null) {
                        $row_cnt=$query->num_rows;
                          if ($row_cnt>0){
                            while ($rows=$query->fetch_assoc()) {
                              $next_QRcode=$rows['QRcode'];}
                            if ((empty($next_QRcode)!=1)||($next_QRcode==null)){ 
                               
                                $childcode=$next_QRcode;
                                $last_sql=$sql; 
                               $n++;
                              }else {$end=1;}
                            } else {$end=1;}
                          }else {$end=1;}
                         
                         
                        }
          
                     
                   
          
          //  use Topcode to find its production id
              if ($skip_search==0) $Topcode=$childcode; 
          

          

          //find all component associate with Topcode
          $last_sql="SELECT * from assembly where QRcode='$Topcode'  ";
          $query=$conn->query($sql);
          if ($query!=null){
            while ($rows=$query->fetch_assoc()) {

              $all_part[]=$rows['QRcode'];
              $all_part[]=$rows['part'];
             
            }
   

            $end=0;
            while  ($end==0)  {
           
              $sql=query_down($last_sql);   
              $query=$conn->query($sql);
                if (mysqli_num_rows($query)!=0){   
                while ($rows=$query->fetch_assoc()) {
                  $all_part[]=$rows['QRcode'];
                  $all_part[]=$rows['part'];
                }
                      if ($layer>1){
                        $last_sql=$sql;
                      } else {$end=1;}
                } else {
                  $end=1;}
                }
  
          



          }


            $all_part=array_unique($all_part);
            


          
              //find production WO from the $all_part
              
              $sql="SELECT WO from QRcode where QRcode='$Topcode'";
              $query=$conn->query($sql);
              if ($query!=null) while ($rows=$query->fetch_assoc())
              {
              $WO=$rows['WO'];
           
              }

              foreach($all_part as $key=>$value){
                $sql="SELECT WO from QRcode_history where QRcode='$value'";
                $query=$conn->query($sql);
                if ($query!=null) while ($rows=$query->fetch_assoc())
                {
                $other_WO[]=$rows['WO'];
             
                }
      


              }
             if ($other_WO!=null) $other_WO=array_unique($other_WO);
          
          
            
               if ($Topcode !=null) {$BND_station=1;}
        
           
           
              }
          
          






  if ($BND_station==1){

  

      //find the production date / BOM, description 


    $sql="SELECT * from workorder 
          join productionDetails 
          using (WO)
          join BOM
          using (BOM)
          where WO='$WO'";
          
    $query=$conn->query($sql);
    while ($rows=$query->fetch_assoc()) { 
    
    $sdate= $rows["startDate"];
    $BOM=$rows["BOM"];
    $po=$rows["PO"];
    $sku=$rows["SKU"];
    $description=$rows["description"];
    $edate=$rows["endDate"];
    $hw=$rows["HW"];
    $fw=$rows["FW"];
    $maxlayer=$rows["maxlayer"];
    if ($edate==null) $edate="In progress";
    }
  

    // find result rom QRcode table
 
  $sql="SELECT result from QRcode where QRcode='$QRcode'";
  $query=$conn->query($sql);
  while ($rows=$query->fetch_assoc()) { $result=$rows["result"];}


      

      // find all component associate with the current QRcode

      $part[0]=$QRcode;

      $sql="SELECT part FROM assembly where QRcode='$QRcode' ";
      $query=$conn->query($sql);
      if (mysqli_num_rows($query)!=0){   
        while ($rows=$query->fetch_assoc()) {
              $part[]=$rows['part'];
        }
      }   

      $sql="SELECT part FROM materialLot where QRcode='$QRcode' ";
      $query=$conn->query($sql);
      if (mysqli_num_rows($query)!=0){   
        while ($rows=$query->fetch_assoc()) {
              $part[]=$rows['part'];
        }
      }   

 





  }
  else {

    $part[0]=$QRcode;
  }

      





          

                if ($all_part!=null)foreach($all_part as $key=>$value)
                {   

                $sql="SELECT * FROM workflow as a
                      join station as b
                      using (station)
                      where QRcode='$value'";
                $query=$conn->query($sql);
                while ($rows=$query->fetch_assoc()){

                $wip_station[]= $rows['station'];
                $wip_info[]=$rows['description'];
                $wip_operator[]=$rows['operator'];
                $wip_result[]=$rows['result'];
                 

                }
              
            
                //only save QCI station and ignore BND station */


                }

           

                
                if ($wip_station!=null){
                  $wip_stationx=array_unique($wip_station);
                  asort($wip_stationx);
                  foreach ($wip_stationx as $key=>$value){
                      if ($value==$wip_station[$key]){
                        $wip_operatorx[$key]=$wip_operator[$key];
                        $wip_infox[$key]=$wip_info[$key];
                        $wip_resultx[$key]=$wip_result[$key];
                      }

                  }
                   

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





// search without edit

  


  ?>



<html>
 



</br>

<div class="leftpane">
  <div class="info_s">
<h2 style="text-align: center">

  
Finish Goods Information <?php echo "(layer ".$maxlayer.")" ?>
</h2>

</div>
<p>
 <?php

if ($WO!=null){
 
    
    echo ' <table class="info" >
          <tr ><th class="m">UDI</th>
          <td class="xl" ><div id="uid" contenteditable=FALSE layer="'.$maxlayer.'" WO="'.$WO.'" uid="'.$Topcode.'" >'.$Topcode.'';  
          
          if ($maxlayer>1) echo '<input class="m" type=button name="right" id="right" value="show details"   style="float:right"> ';
     echo 
     
          '</div>
          </td>
          <tr >
         
          </tr>
          <tr><th>WO:</th><td>'.$WO;
          
          if ($other_WO!=null) foreach ($other_WO as $key=>$value){
            echo ' / '.$value;

          }

          echo 
          '</td><tr><th>BOM:</th><td>'.$BOM.'</td>
          <tr><th>Description:</th><td>'.$description.'</td>
 
          <tr><th>PO:</th><td>'.$po.'</td>
          <tr><th>SKU:</th>
          <td>'.$sku.'</td>
          </tr>
          <tr><th>H/W vesion:</th>
          <td>'.$hw.'</td>
          </tr>
          <tr><th>F/W version:</th>
          <td>'.$fw.'</td>
          </tr>

          <tr><th>Manufacturing Date:</th>
          <td>'.$sdate.'</td>
          </tr>
          <tr><th>Completion Date:</th>
          <td>'.$edate.'</td>
         
          
          </tr></table> </br><p>';
   



}

 
?>

 

</div>



<div class="rightall" >
<div>
<span class="halfpane info_w"  id="s_table">
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


           
          if ($component_array!=null){

           $comp_arr=array_unique($component_array);

           foreach($comp_arr as $key=>$value){
            if ($value==$component_array[$key]) {$compinfo_arr[$key]=$componentInfo_array[$key];}


           }

          }
          
     
         
          if (($comp_arr!=null)&&($BND_station==1)){
            $part=array_unique($part);

          
            $i=0;
            while ($i< count($comp_arr)){
    
             
            if ($part!=null)foreach ($part as $key=>$value) if  (substr_count($value, $comp_arr[$i])==1) { 

              echo '<tr> 
                
              <td>' . $comp_arr[$i].'</td> 
             
              <td>' .$compinfo_arr[$i].'</td><td>';

              $current_layer=$maxlayer-1;
        

              echo   '<button type="button"  name="CO" data-id="'.$value.'"  data-layer="'.$current_layer.'"   class="btn_co xl btn_bpart" >'. $value.'</br></button>';
             
               
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
         

     
<span  class="halfpane info_w" >
<div>

<h2  style="text-align: center" >
      Test station
    </h2>
    
    <table class="center"  >
   


<th class="m">QCIstation</th>
<th class="l" >Description</th>
<th class="m">Operator</th>
<th class="m">Result</th>
<th class="m">Report</th>




    <?php
     
       
    
        if ($wip_station!=null) foreach($wip_stationx as $key=>$value){

          echo '<tr> 
             
          <td>' . $value.'</td> 
         
          <td>' .$wip_infox[$key].'</td>
          
          
          <td><span>' .$wip_operatorx[$key].'</br>
          </span></td>
            
            <td><span>' .$wip_resultx[$key].'</br>
            </span></td>
            
            <td><span><button id="test_data" value="test_data" name="test_data">view</button></br>
            </span></td></tr>';





        }
       
  
         
        

  
       
  
        
      
    
    
  
    
   
   
 
    ?>

</table>
</div>
    </span>


















 </div>

</div>







</html>




