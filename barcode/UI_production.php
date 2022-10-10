<script src="./js/jquery-3.6.0.js"></script>

<script>


$(function(){
        $("#nav-placeholder").load("nav.html");
      });
     
      function delete_row(id)
            {
                console.log(id);
            $.ajax
            ({
              type:'post',
              url:'./backend/production/home.php',
              data:{
                id:id,
              },
              success:function(data)
                {
                  
             //   console.log(data);
                  
                $('#result').html(data);
                
                
                }

            });
            }





            $(document).ready(function(){
            
              var refreshtime=setInterval(refresh_fuction, 10000);
               
              
           function refresh_fuction(){
 
            $.ajax({
                url:"./backend/production/refresh.php",
                method:"POST",
                data:{},
                success:function(data)
                {
                  
              //  console.log(data);
                  
                $('#result').html(data);
                
                
                
                }
              });




            }
            
            var toggle=true;
       
            setInterval(function() {
                var d = new Date().toLocaleTimeString('en-US', { hour12: false, hour: 'numeric', minute: 'numeric' });
                var parts = d.split(":");
                $('#hours').text(parts[0]);
                $('#minutes').text(parts[1]);
                $("#colon").css({ visibility: toggle?"visible":"hidden"});
                toggle=!toggle;
              },1000);

           







            $('#delete_form').on('click', '.delete', function(){
                console.log("ok");
            });
            






            $('#insert_form').on('submit', function(event){
              event.preventDefault();
              var error = '';

              console.log("yes")
              $('.WO').each(function(){
              var count = 1;
            
              

              if($(this).val() == '')
              {
                error += "Enter Item WO ";
                return false;
              }
              count = count + 1;
              });
              
             
              
              
              var form_data = $(this).serialize();
              if(error == '')
              {
              $.ajax({
                url:"./backend/production/home.php",
                method:"POST",
                data:form_data,
                success:function(data)
                {
                  
                
                  
                $('#result').html(data);
                
                
                
                }
              });
              }
              else
              {
             alert (error);
              }
            });
            
            });



// graph










</script>

















<?php
//index.php
date_default_timezone_set('Asia/Hong_Kong');

$check_key=FALSE;
include_once "conn.php";



$sql="SELECT * FROM workorder;";
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
  
          $WO[]=$rows['WO']; 
        
         
            }





























?>









 
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  
  <title>barcode system</title>
  <link rel="stylesheet" type="text/css" href="./css/styles.css" >
  

  </head>




  <body> 
            



    <div class="header">

            <div class="toppane">
            <h1 style="color: #595770">
            HTI Barcode system <br>
            Daily Production
            </h1>
            <div id="nav-placeholder"></div>

            </div>
            
    </div>
  
  
  
  
  <div class="main" >     

    
                  <form method="post" id="insert_form">
                  <div class="table-repsonsive ">
             
                  </div>
                 
                  </form>
   
      <div class="widepane " >
      
              <div class="halfpane " id="result" >
           
                  <form  method="post"  style="overflow-y:scroll; height:400px;" id="result" >
                      <div  >
                      <table class="info" >



                            <th class="m">WO#</th>
                            <th class="l">Description</th>
                            <th class="m">Target Qty</th>
                            <th class="m">Output</th>
                            <th class="m">Manufacturing Date</th>
               
                          <?php

                          $sql="SELECT * FROM productionDetails as a
                          join workorder as b
                          using (WO)
                          join BOM
                          using (BOM)
                          where (endDate is null) or (startDate=CURDATE()) or (endDate=CURDATE())
                          order by a.startDate,a.recordDate desc ";
                          $query=$conn->query($sql);
                          while ($rows=$query->fetch_assoc()){
                              $id_array[]=$rows['production_id'];
                              $WO_array[]=$rows['WO'];                          
                              $WOinfo_array[]=$rows['description'];
                              $output_array[]=$rows['output'];
                              $qty_array[]=$rows['qty'];
                              $edate_array[]=$rows['endDate'];
                              $sdate_array[]=$rows['startDate'];
                              }



                          if ($WO_array !=null){

                          $arraylength=count($WO_array);

                          $i=0;
                          while  ($i< $arraylength){
                              echo '<tr> 
                                       <td>' . $WO_array[$i].'</td> 
                                      <td>' . $WOinfo_array[$i].'</td> 
                                      <td>' . $qty_array[$i].'</td> 
                                      <td>' . $output_array[$i].'</td>
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
   
              </div>
             <div class="halfpane white"  >
   
              <div style="text-align: center"><h2 > Time =  <span id="hours"></span><span id="colon">:</span><span id="minutes"></span></h2></div>     
              <div id="chart" style="width:700;height:350"></div>


              <!-- HTML -->
<?php
include "graph.php";
?>


                
          </div>


     </div>
    
 





 </body>


  

</html>

