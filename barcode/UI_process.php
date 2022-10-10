<script src="./js/jquery-3.6.0.js"></script>
<script>

  //send BOM to backend_bom.php and return the BOM list to frontend
$(document).ready(function() {
   
    function ajax(){
        $.ajax({
            url: "./backend/process/home.php",
            type:"POST",
            data:{BOM: $("#BOM").val()},
            success:function(result){
         
                $('#result').html(result);
            }
        });
    }




            $(document).on('click','.btn_bom',function() {
                
            var div=document.getElementById("BOM");
          
                    
                                        
            var id=$(this).data("id");
            console.log(id);

            div.value=id;
            $.ajax({type: "POST",
            url: "./backend/process/home.php",

            data: { edit: "edit", BOM:id},
            success:function(result) {
            $("#result").html(result);
                
                
            }
            });

            });




     
// edit a new workorder ans send the BOM# to backend
    $("#edit").click(function() {
      
        var BOM=$("#BOM").val();

    if (BOM=='')  alert ("BOM is empty");
    else {


        $.ajax({type: "POST",
        url: "./backend/process/home.php",
        data: { edit: $("#edit").val(), BOM: BOM},
        success:function(result) {
          
            $('#result').html(result);
        },
        error:function(result) {
          alert('error');
        }
        });

        }
    });

   
    $("#BOM").keyup(function() {
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

$sql="SELECT * FROM BOM";
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
    $BOM_array[]=$rows['BOM']; 

    $BOMinfo_array[]=$rows['description'];
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
            Create BOM and workflow
            
            </h1>
   
            <div id="nav-placeholder">
            </div>

           


          </div>
            
  </div>
  <div class="main" >    





 <h2 class="center">


BOM#<input type="text" id="BOM"/>

<button id="edit" value="edit" name="edit">edit/new</button>



<p>



</h2>

<span class="widepane" id="result">

<div class="rightpane" style="overflow-y:scroll; height:400px;" >
                    
                    <p>
                    <table class="info"   >
                    
                    <th class="m">BOM</th>
                    <th class="l">Description</th>
               

                        <?php


                   
                                if ($BOM_array !=null){
 
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

 
</span>









 </div>

           
       

</body>
</html>












  







