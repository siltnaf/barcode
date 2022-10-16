 

<script src="./js/jquery-3.6.0.js"></script>

<script>
  
  // script to load nav menu
$(function(){
        $("#nav-placeholder").load("nav.html");
      });


$(document).ready(function() {
 

 var input = document.getElementById("carton");



 $("#reset").click(function() {
              input.value="";
              input.focus();
              $.ajax({
                        url:"./backend/print/palletList.php",
                        method:"POST",
                        data: {reset:"reset"},
                        success:function(result)
                        {
                          $('#result').html(result);

                          counter_value=1;
                          document.getElementById('counter').innerHTML=counter_value;
          

                         }
                        });
                          

 });



 $("#print").click(function() {
              input.value="";
              input.focus();
              $.ajax({
                        url:"./backend/print/pallet.php",
                        method:"POST",
                        data: {print:"print"},
                        success:function(result)
                        {
                          
                          $('#result').html(result);
                          counter_value=1;
                          document.getElementById('counter').innerHTML=counter_value;
          

                         }
                        });
                          

 });





// if enter is press, it trigger the print button
input.addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    var QRvalue=$("#carton").val();
   
    var counter_value=document.getElementById('counter').innerHTML;
   
   
         
          $.ajax({
                        url:"./backend/print/palletList.php",
                        method:"POST",
                        data: {QRcode:QRvalue, counter:counter_value},
                        success:function(result)
                        {
                          result=JSON.parse(result);
                         if (result.status=='error'){

                          alert (result.msg);
                         
                         } else{

                          counter_value++;
                         document.getElementById('counter').innerHTML=counter_value;
          

                         }
                          
                        
                      
                        }
                    });

      
      
        
     
                       /*   result=JSON.parse(result);
                         if (result.status=='error'){

                          alert (result.msg);
                         
                         } else{

                         alert(result.msg);

                          
                            */
                         


                       
                          
                
                        }
                  

        

       });
   
  
  
    
  });



  
  









</script>

<?php
include_once "conn.php";



$sql="DELETE FROM Pack_pallet";
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
       
            Read carton &ensp;<input type="text" id="carton" value=""/> 
     
            <input type="button" id="print" value="Print" name="Print" />
            <input type="submit" id="reset" value="Reset" name="RESET"/>
              
              
              
         
            
            
             
            
            
            </h2>
        </div>
          
      

        <span class="leftpane" >

        <div style="overflow-y:scroll; height:400px;" >
      
        <div id="counter"  class="center" style="font-size:200px">
     1
      
        </div>
      


        </div>
        </span>
        
        <span class="rightall" id="result">

 
        </span>
      
          
        </div>  
 

</body>
</html>



            
 
             
           