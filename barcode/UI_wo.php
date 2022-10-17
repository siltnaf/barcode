<script src="./js/jquery-3.6.0.js"></script>
<script>

  //send WO to backend/wo.php and return the WO list to frontend
$(document).ready(function() {
   
    function ajax(){
        $.ajax({
            url: "./backend/wo/home.php",
            type:"POST",
            data:{WO: $("#WO").val()},
            success:function(result){
         
                $('#result').html(result);
            }
        });
    }


//   wo button click and jump to wo/home.php


            $(document).on('click','.btn_wo',function() {
                
              
            var div=document.getElementById("WO");
                             
            var id=$(this).data("id");

            div.value=id;
            console.log(id);

            $.ajax({type: "POST",
            url: "./backend/wo/home.php",

            data: { edit: "edit", WO:id},
            success:function(result) {
            $("#result").html(result);
                
                
            }
            });

            });

//  click bom jump to bom/home.php

            $(document).on('click','.btn_bom',function() {

            window.location.replace("./UI_process.php");   
            var div=document.getElementById("bom");
           
                             
            var id=$(this).data("id");
             
            div.value=id;    
            $.ajax({type: "POST",
            url: "./backend/process/home.php",

            data: { edit: "edit", BOM:id},
            success:function(result) {
                   
            $("#result").html(result);
                
                
            }
            });

            });



     
// edit a new WO ans send the WO# to backend
    $("#edit").click(function() {
       
        var WO=$("#WO").val();

        if (WO=='')  alert ("WO is empty");
            else {

                $.ajax({type: "POST",
               url: "./backend/wo/home.php",
                data: { edit: $("#edit").val(), WO: WO},
                success:function(result) {
                
                    $('#result').html(result);
                },
                error:function(result) {
                alert('error');
        }
    });


            }

  

    });

   
    $("#WO").keyup(function() {
        ajax();

    });
});

// load the navigation bar


      $(function(){
        $("#nav-placeholder").load("nav.html");
      });
   


</script>
<?php
include_once "conn.php";
$editable="TRUE";

                 
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
    $sn_pre[]=$rows["SN_prefix"];
    $sn_suf[]=$rows["SN_suffix"];
    $lot_arr[]=$rows["LOT"];
    $sdate_arr[]=$rows["startDate"];
    $edate_arr[]=$rows["endDate"];
}









?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  
  <title>barcode system</title>
  <link rel="stylesheet" type="text/css" href="./css/styles.css" />
  </head>


  <body> 
    <div class="header">

            <div class="toppane">
          
            <h1 style="color: #595770 " >
            HTI Barcode system <br>
            Enter WO to the system
            
            </h1>
   
            <div id="nav-placeholder">
            </div>

           


          </div>
            
  </div>
  <div class="main" >    





 <h2 class="center">


WO#<input type="text" id="WO"/>

<button id="edit" value="edit" name="edit">edit/new</button>



<p>



</h2>


<span class="widepane" id="result">

<div style="overflow-y:scroll; height:400px;" >
             <table class="info"  >


                 

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

 

                   if ($wo_arr!=null){

                   $arraylength=count($wo_arr);

                   $i=0;
                   while  ($i< $arraylength){
                       echo '<tr> 

                               <td ><button type="button" name="WO" data-id="'.$wo_arr["$i"].'" class="btn_wo m" >'.$wo_arr[$i].'</button></td> 
                  
                               
                               <td>' . $woinfo_arr[$i].'</td> 
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

                               </td>
                               </tr>';
                       $i++;
                   }

                   }
               
               
                   ?>


               </table>
               </div>
                </span>
                



           
       

</body>
</html>












  







