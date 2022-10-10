

<script src="js/jquery-3.6.0.js"></script>


<?php

include_once "../../conn.php";




 
// find the max layer in each pid

$sql="SELECT a.WO,c.maxlayer,a.endDate  FROM productionDetails as a
      join workorder as b
      using (WO)
      join BOM  as c
      using (BOM)
      where  (a.endDate is null) or (a.startDate=CURDATE()) or (a.endDate=CURDATE())";


$query=$conn->query($sql);


if (mysqli_num_rows($query)!=0) 
  {
      while ($rows=$query->fetch_assoc()) { 
      $WO[]=$rows["WO"];
      $maxlevel[]=$rows["maxlayer"];
      $endDate[]=$rows["endDate"];
      } 




 

//  find the complete qty in pid from assembly table 


 //only count those in production

        

        foreach( $endDate as $key=>$value){
          if ($value==null){


            $sql="SELECT a.WO  FROM QRcode as a
            join assembly as b
            using (QRcode) 
            where a.WO='$WO[$key]' and layer='$maxlevel[$key]' ";

             $query=$conn->query($sql);
            
        
            if (mysqli_num_rows($query)!=null) 
            {
                while ($rows=$query->fetch_assoc()) { 
                $QRcode[$key][]=$rows["WO"];
               
                

                }
            
              $output=count($QRcode[$key]);
                
              
              } else $output=0;


                }



    


   
    $sql="UPDATE productionDetails SET output = '$output' WHERE (WO = '$WO[$key]') ";
    $query=$conn->query($sql);
       

          }









}



// if the output >=qty  record the end date





$sql="SELECT * FROM productionDetails as a 
    join workorder as b
    using (WO)
    join BOM
    using (BOM)
    where (endDate is null) or (startDate=CURDATE()) or (endDate=CURDATE())
    order by a.startDate,a.recordDate desc";
    
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
                $id_array[]=$rows['production_id'];
                $WO_array[]=$rows['WO']; 
                $WOinfo_array[]=$rows['description'];
                
                $output_array[]=$rows['output'];
                $qty_array[]=$rows['qty'];
                $sdate_array[]=$rows['startDate'];
                $edate_array[]=$rows['endDate'];
                }
 

// if the output >=qty  record the end date
foreach($qty_array as $key=>$value)
{
if ($output_array[$key]>=$value)
{
  $sql="UPDATE productionDetails SET endDate=CURDATE() where production_id='$id_array[$key]' ";
  $query=$conn->query($sql);

}

}



$sql="SELECT production_id,output FROM productionDetails where (startDate<'CURDATE()') and (endDate is null)";

$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){ if ($maxlevel!=null)
    foreach ($maxlevel as $id=>$level){
      if ($id ==$rows['production_id']){
        $graph_id=$rows['production_id'];
        
        $graph_output[]=$rows['output'];

      }
     } 
    
    }







?>



    
    <form  method="post"  style="overflow-y:scroll; height:400px;" >
        <div  >
        <table class="info" >


              <th class="m">WO#</th>
              <th class="l">Description</th>
              <th class="m">Target Qty</th>
              <th class="m">Output</th>
              <th class="m">Manufacturing Date</th>
            
            <?php

            


            if ($WO_array !=null){

            $arraylength=count($WO_array);

            $i=0;
            while  ($i< $arraylength){
                echo '<tr> 
                        <td>' . $WO_array[$i].'</div></td> 
                        <td>' . $WOinfo_array[$i].'</div></td> 
                        <td >' . $qty_array[$i].'</div></td> 
                        <td >' . $output_array[$i].'</div></td> 
                        <td>' . $sdate_array[$i].'</div></td> 
                       
                        </td>
                        </tr>';
                $i++;
            }

            }
        
        
            ?>


        </table>
          </div> 
    </form>
      
     
















 

 






