<script src="./js/jquery-3.6.0.js"></script>
<script>
  
  // script to load nav menu
$(function(){
        $("#nav-placeholder").load("nav.html");
      });


      $(document).ready(function() {
   
    

  
// if a record search is clicked , shows its full history
   $("#search").click(function(e) {
       var QRvalue=$("#barcode").val()
       e.preventDefault();
     if (($('#barcode').val())!=null){

        $.ajax({type: "POST",
       url: "./backend/record/home.php",
       data: { QRcode: QRvalue},
       success:function(result) {
      
     $('#result').html(result);
       },
       error:function(result) {
         alert('error');
       }
      });
     } 
   });





});






</script>


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
              Barcode &ensp;<input type="text" id="barcode"/>
              
            <input type="submit" id="search" value="search" name="search"/>
            
            
          
         
            
            
            <span id="UDI"></span>
            
            
</h2>
        </div>
          


            <span class="widepane" id="result"></span>


         
          
</div>        
 

</body>
</html>

