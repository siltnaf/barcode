

<script src="js/jquery-3.6.0.js"></script>

<?php
 $check_key=FALSE;
include_once "../../conn.php";


if(isset($_POST["WO"]))
{

    $WO=test_input($_POST["WO"][0]);
    $sdate=test_input($_POST["startDate"][0]);
   
 
  $sql="SELECT WO,startDate FROM productionDetails where WO= '$WO' ;";
  $query=$conn->query($sql);
  if  (mysqli_num_rows($query)==null) {
    $sql = "INSERT INTO productionDetails (WO,startDate)  VALUES ('$WO','$sdate'); ";
    $query=$conn->query($sql);
     
    }
   else {
    echo '<script>alert ("repeated WO")</script>' ;

   }
  
 
  

 

}


if(isset($_POST["id"]))
{
    
 $id=test_input(($_POST["id"]));
 
  $sql = "DELETE FROM productionDetails WHERE (production_id = '$id');";
  $query=$conn->query($sql);
  




}



$sql="SELECT * FROM productionDetails as a 
      join workorder as b
      using (WO)
      join BOM
      using (BOM)
      where (endDate is null) or (startDate=CURDATE()) or (endDate=CURDATE())
      order by a.startDate desc ";
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
    $id_array[]=$rows['production_id'];
    $WO_array[]=$rows['WO'];
    $WOinfo_array[]=$rows['description'];
    $lot_array[]=$rows['lot'];
    $output_array[]=$rows['output']; 
    $qty_array[]=$rows['qty'];
    $edate_array[]=$rows['endDate'];
    $sdate_array[]=$rows['startDate'];
    }


















?>



    
    <form  method="post"  style="overflow-y:scroll; height:400px;" id="result" >
        <div  >
        <table class="info" >


        <th class="m">WO#</th>
        <th class="l">Description</th>
        <th class="m">Target Qty</th>
        <th class="m">Completed Qty</th>
        <th class="m">Manufacturing Date</th>
        <th class="xs"></th>

             
             
    
            <?php




            if ($WO_array !=null){

            $arraylength=count($WO_array);

            $i=0;
            while  ($i< $arraylength){
                echo '<tr> 
                        <td>' . $WO_array[$i].'</div></td> 
                        <td>' . $WOinfo_array[$i].'</div></td> 
                        <td >' . $qty_array[$i].'</div></td> 
                        <td>' . $output_array[$i].'</div></td> 
                        <td>' . $sdate_array[$i].'</div></td> 
                        <td class="xs"><input type="button" class="delete_button" id="delete_button" value="-" onclick="delete_row('.$id_array[$i].')">
                        </td>
                        </tr>';
                $i++;
            }

            }
        
        
            ?>


        </table>
          </div> 
    </form>


















<?php

?>

   