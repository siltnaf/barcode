 

<script src="./js/jquery-3.6.0.js"></script>

<script>
  
  // script to load nav menu
$(function(){
        $("#nav-placeholder").load("nav.html");
      });


$(document).ready(function() {
 

 var input = document.getElementById("UDI");



 $("#reset").click(function() {
              input.value="";
              input.focus();
              $.ajax({
                        url:"./backend/print/cartonList.php",
                        method:"POST",
                        data: {reset:"reset"},
                        success:function(result)
                        {
                          $('#result').html(result);

                          counter_value=0;
                          document.getElementById('counter').innerHTML=counter_value;
          

                         }
                        });
                          

 });



 $("#print").click(function() {
              input.value="";
              input.focus();
              $.ajax({
                        url:"./backend/print/carton.php",
                        method:"POST",
                        data: {print:"print"},
                        success:function(result)
                        {
                          
                          $('#result').html(result);
                          counter_value=0;
                          document.getElementById('counter').innerHTML=counter_value;
          

                         }
                        });
                          

 });





// if enter is press, it trigger the print button
input.addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    var QRvalue=$("#UDI").val();
    input.value="";
    var counter_value=document.getElementById('counter').innerHTML;
    var max_value=$("#UDI_pcs").val();
    console.log(max_value);
    if (counter_value<(max_value-1)) {
         
          $.ajax({
                        url:"./backend/print/cartonList.php",
                        method:"POST",
                        data: {QRcode:QRvalue, counter:counter_value},
                        success:function(result)
                        {
                          result=JSON.parse(result);
                         if (result.status=='error'){

                          alert (result.msg);
                         
                         } else{
                          console.log(counter_value);
                          counter_value++;
                         document.getElementById('counter').innerHTML=counter_value;
          

                         }
                          
                        
                      
                        }
                    });

        }
       else {
      
        
        $.ajax({
                        url:"./backend/print/carton.php",
                        method:"POST",
                        data: {QRcode:QRvalue},
                        success:function(result)
                        {

                          $('#result').html(result);
                         
                         
                        }
                      });
                       /*   result=JSON.parse(result);
                         if (result.status=='error'){

                          alert (result.msg);
                         
                         } else{

                         alert(result.msg);

                          
                            */
                         


                        
                         counter_value=0;
                        document.getElementById('counter').innerHTML=counter_value;
                          
                
                        }
                  

        

       }
   
  
  
    
  });
});


  
  









</script>

<?php
include_once "conn.php";



$sql="DELETE FROM Pack_carton";
$query=$conn->query($sql);






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
            <h1 style="color: #595770">
            HTI Barcode system <br>
            Search Record
            </h1>
            <div id="nav-placeholder">
            </div>

           


          </div>
            
  </div>
  <div class="main" >     




            

            <div class="container">
            <h2 class="center">
       
            Read UDI &ensp;<input type="text" id="UDI" value=""/> <br>

            Print label per<input type="text"   id="UDI_pcs" value="<?php echo $counter_max ?>"  style="width:50px" class="center"/> input / &ensp;
               
            <input type="submit" id="print" value="Print" name="Print"/>
            <input type="submit" id="reset" value="Reset" name="RESET"/>
              
              
              
         
            
            
             
            
            
            </h2>
        </div>
          
      

        <span class="leftpane" >

        <div style="overflow-y:scroll; height:400px;" >
      
        <div id="counter"  class="center" style="font-size:200px">
     0
      
        </div>
      


        </div>
        </span>
         
        <span class="rightall" id="result">

<div style="overflow-y:scroll; height:400px;" >

 


</div>
</span>
          
        </div>  
 

</body>
</html>



            
 
             
           