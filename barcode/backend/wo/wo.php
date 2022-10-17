
<?php

include_once "../../conn.php";


$value=test_input($_POST["value"]);
$column=test_input($_POST["column"]);
$WO =test_input($_POST["WO"]);
 
$update=test_input($_POST["update"]);



if ($update=="WO"){
  
   if ($column=='startDate'){

    $sql="UPDATE productionDetails SET $column = '$value' WHERE   (WO = '$WO') ;"; 
   }else {
    $sql="UPDATE workorder SET $column = '$value' WHERE (WO = '$WO') ;"; 

   }

   

    $query=$conn->query($sql);

    echo $sql;
}

 if ($update=="delete"){
    
     
    $WO=test_input($_POST["WO"]);


    $sql="DELETE FROM QRcode WHERE (WO = '$WO');";
    $query=$conn->query($sql);

    $sql="DELETE FROM productionDetails WHERE (WO = '$WO');";
    $query=$conn->query($sql);
    $sql="DELETE FROM workorder WHERE (WO = '$WO');";

    $query=$conn->query($sql);
 }

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
            $sn_pre[]=$rows["SN_prefix"];
            $sn_suf[]=$rows["SN_suffix"];
            $po_arr[]=$rows["PO"];
            $sku_arr[]=$rows["SKU"];
            $qty_arr[]=$rows["qty"];
            $lot_arr[]=$rows["LOT"];
            $sdate_arr[]=$rows["startDate"];
            $edate_arr[]=$rows["endDate"];


       





 }



    ?>





<div style="overflow-y:scroll; height:400px;" >

<table class="info" >
                     <th class="m">WO</th>
                    <th class="l">Description</th>
                    <th class="m">BOM</th>
                    <th class="s">FW</th>
                    <th class="s">HW</th>
                    <th class="s">_SN</th>
                    <th class="s">SN_</th>
                    <th class="m">PO</th>
                    <th class="m">SKU</th>
                    <th class="s">Qty</th>
                    <th class="m">LOT</th>
                    <th class="xm">Manufacturing Date</th>
                    <th class="xm">Completion Date</th>
    <?php

 

if (($update=="delete")&&($wo_arr!=null)) {
      
         

               
            
                $arraylength=count($wo_arr);
                $i=0;
                
                while ($i<$arraylength){
                     echo '<tr>
                     <td ><button type="button" name="WO" data-id="'.$wo_arr["$i"].'" class="btn_wo m" >'.$wo_arr[$i].'</button></td> 
                      <td >' . $woinfo_arr[$i].'</td>
                      <td ><button type="button" name="WO" data-id="'.$bom_arr["$i"].'" class="btn_bom m" >'.$bom_arr[$i].'</button></td>
                    
                               <td>' . $fw_arr[$i].'</td>
                               <td>' . $hw_arr[$i].'</td>
                               <td>' . $sn_pre[$i].'</td>
                               <td>' . $sn_suf[$i].'</td>
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