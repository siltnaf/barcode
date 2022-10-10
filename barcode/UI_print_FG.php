<script src="./js/jquery-3.6.0.js"></script>

<script>
  
  // script to load nav menu
$(function(){
        $("#nav-placeholder").load("nav.html");
      });


      $(document).ready(function() {
   
var input = document.getElementById("UDI");


// if enter is press, it trigger the print button
input.addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    document.getElementById("print").click();
  }
});

  
// if a print is clicked , it print to label printer
   $("#print").click(function(e) {
       var QRvalue=$("#UDI").val()
     
      
       input.value="";
      
       e.preventDefault();
     if (($('#UDI').val())!=null){

        $.ajax({type: "POST",
       url: "./backend/print/FG.php",
       data: { QRcode: QRvalue,
                
                },
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
                    Read UDI &ensp;<input type="text" id="UDI" value=""/></input>     
                    <input type="submit" id="print" value="Print" name="Print"/></input>
            
                </h2>
            </div>
          
      

        <span class="widepane" id="result">

            <div style="overflow-y:scroll; height:400px;" >

            </div>
        </span>
         
          
    </div>  
 

  </body>
</html>

