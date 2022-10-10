<script src="./js/jquery-3.6.0.js"></script>
<script>


      $(function(){
        $("#nav-placeholder").load("nav.html");
      });
    


$(document).ready(function() {



// click operator button
      $(document).on('click','.btn_op',function() {

        var div=document.getElementById("operator");                            
        var id=$(this).data("id");
        div.value=id;
        console.log(id);


        $.ajax({type: "POST",
        url: "./backend/component/operator.php",

        data: { edit: "edit", operator:id},
        success:function(result) {
        $("#o_result").html(result);
            
            
        }
        });

        });

// click station button 
        $(document).on('click','.btn_st',function() {

          var div=document.getElementById("station");                                       
          var id=$(this).data("id");
          div.value=id;
          console.log(id);


          $.ajax({type: "POST",
          url: "./backend/component/station.php",

          data: { edit: "edit", station:id},
          success:function(result) {
          $("#s_result").html(result);
              
              
          }
          });

          });

//click component button

          $(document).on('click','.btn_co',function() {

          var div=document.getElementById("component");                                         
          var id=$(this).data("id");
          div.value=id;

          console.log(id);


          $.ajax({type: "POST",
          url: "./backend/component/component.php",

          data: { edit: "edit", component:id},
          success:function(result) {
          $("#c_result").html(result);
              
              
          }
          });

          });



   // station 
   
   
   $("#s_edit").click(function() {
      var station=$("#station").val();
      if (station=='')  alert ("station is empty");
    else {

      $.ajax({type: "POST",
       url: "./backend/component/station.php",
       data: { edit: $("#s_edit").val(), station: station },
       success:function(s_result) {
           $('#s_result').html(s_result);
       },
       error:function(s_result) {
         alert('error');
       }
      });

    }
      
  

   });

   $("#s_delete").click(function() {
   
     $.ajax({type: "POST",
     url: "./backend/component/station_update.php",
     data: { delete: $("#s_delete").val(), station: $("#station").val()},
     success:function(s_result) {
         $('#s_result').html(s_result);
     },
     error:function(s_result) {
       alert('error');
     }
 });

 });


  
   $("#station").keyup(function() {
    $.ajax({
           url: "./backend/component/station.php",
           type:"POST",
           data:{station: $("#station").val()},
           success:function(s_result){
               $('#s_result').html(s_result);
           }
       });

   });



//operator
   
   
   $("#o_edit").click(function() {
    var operator=$("#operator").val();

    if (operator=='')  alert ("operator is empty");
    else {

     $.ajax({type: "POST",
     url: "backend/component/operator.php",
     data: { edit: $("#o_edit").val(), operator:operator},
     success:function(o_result) {
         $('#o_result').html(o_result);
     },
     error:function(o_result) {
       alert('error');
     }
 });
}

 });

 $("#o_delete").click(function() {
 
   $.ajax({type: "POST",
   url: "backend/component/operator_update.php",
   data: { delete: $("#o_delete").val(), operator: $("#operator").val()},
   success:function(o_result) {
       $('#o_result').html(o_result);
   },
   error:function(o_result) {
     alert('error');
   }
});

});



 $("#operator").keyup(function() {
  $.ajax({
         url: "./backend/component/operator.php",
         type:"POST",
         data:{operator: $("#operator").val()},
         success:function(o_result){
             $('#o_result').html(o_result);
         }
     });

 });














// component
   
  
    $("#c_edit").click(function() {
      var component=$("#component").val();
      if (component=='')  alert ("component is empty");
    else {


        $.ajax({type: "POST",
        url: "./backend/component/component.php",
        data: { edit: $("#c_edit").val(), component: component},
        success:function(c_result) {
            $('#c_result').html(c_result);
        },
        error:function(c_result) {
          alert('error');
        }
    });
  }
    });

    $("#c_delete").click(function() {
    
      $.ajax({type: "POST",
      url: "./backend/component/component_update.php",
      data: { delete: $("#c_delete").val(), component: $("#component").val()},
      success:function(c_result) {
          $('#c_result').html(c_result);
      },
      error:function(c_result) {
        alert('error');
      }
  });

  });


   
    $("#component").keyup(function() {
        $.ajax({
            url: "./backend/component/component.php",
            type:"POST",
            data:{component: $("#component").val()},
            success:function(c_result){
                $('#c_result').html(c_result);
            }
        });

    });
});

</script>


<?php

include_once "conn.php";

$sql="SELECT * FROM worker ";
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
 
    $operator_array[]=$rows['operator'];
    $operatorInfo_array[]=$rows['description'];
}


$sql="SELECT * FROM station  ";
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
    $version_array[]=$rows['version']; 
    $station_array[]=$rows['station'];
    $stationInfo_array[]=$rows['description'];
      }


$sql="SELECT * FROM component  ";
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
  
    $component_array[]=$rows['component'];
    $componentInfo_array[]=$rows['description'];
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
            <h1 style="color: #595770">
            HTI Barcode system <br>
            Component Setup
            </h1>
            <div id="nav-placeholder">
            </div>

           


          </div>
            
  </div>
  <div class="main" >     





    </br>
             <div class="tripane1" >
                </br>
               <div class="info_s">
                  <h2  style="text-align: center">
                 <th >Operator List</th> 
               
                  </br>
                  <input type="text" id="operator"/>
                  <button id="o_edit" value="o_edit" name="o_edit">edit/new</button>
                  <button id="o_delete" value="o_delete" name="o_delete">-</button>
                  </h2>
                  <span id="o_result" >
                  <div class="info"  style="overflow-y:scroll; height:400px;">
                  <table>
                      <th class="m">Operator</th>
                      <th class="l">Description</th>

                      <?php


    
                        if ($operator_array!=null){
                        $arraylength=count($operator_array);

                        $i=0;
                        while  ($i< $arraylength){
                            echo '<tr> 
                                     <td ><button type="button" name="WO" data-id="'.$operator_array["$i"].'" class="btn_op m" >'.$operator_array[$i].'</button></td>
                            
                                    <td>' .  $operatorInfo_array[$i].'</div></td> 
                                  </tr>';
                            $i++;
                        }
                      }

                        ?>
                      </table>
                      </div>


                      </span>

                
                </div>
                 
            
            
            </div>






            <div class="tripane2" >
              </br>
                <div class="info_s">
                  <h2 style="text-align: center" >
                      <th  >Station Database</th>
                        </br>
                      <input type="text" id="station"/>
                      <button id="s_edit" value="s_edit" name="s_edit">edit/new</button>
                      <button id="s_delete" value="s_delete" name="s_delete">-</button>
                    </h2>
                    <span  id="s_result" >
                                        
                        <div class="info"  style="overflow-y:scroll; height:400px;">
                        <table  >
                        <th class="l">Station</th>
                        <th class="xs">Rev.</th>
                        <th class="l">Description</th>
                        <?php
            
 
                          if ($station_array!=null){
                            $arraylength=count($station_array);

                            $i=0;
                            while  ($i< $arraylength){
                                echo '<tr> 
                                <td ><button type="button" name="WO" data-id="'.$station_array["$i"].'" class="btn_st m" >'.$station_array[$i].'</button></td>
                                        <td >' .  $version_array[$i].'</div></td> 
                                        <td >' .  $stationInfo_array[$i].'</div></td> 
                                      </tr>';
                                $i++;
                            }


                          }
                          
                            
                            ?>
                            </table>
        
          
                        </div>

                        </span>
                   
                   </div>
                </div>

            <div class="tripane1">
          </br>
            <div class="info_s">
                  <h2 style="text-align: center" >
            <th >Component Database</th>
            </br>

            <input type="text" id="component"/>
            <button id="c_edit" value="c_edit" name="c_edit">edit/new</button>

            <button id="c_delete" value="c_delete" name="c_delete">-</button>
           
            
           

            </h2>
            <span id="c_result">



            <div class="info" style="overflow-y:scroll; height:400px;">
              <table>
              <th class="l">Component</th>
              <th class="l">Description</th>

                  <?php


                 if ($component_array!=null){
                  $arraylength=count($component_array);

                  $i=0;
                  while  ($i< $arraylength){
                      echo '<tr> 
                      <td ><button type="button" name="WO" data-id="'.$component_array["$i"].'" class="btn_co l" >'.$component_array[$i].'</button></td>
                      
                              <td >' . $componentInfo_array[$i].'</div></td> 
                            </tr>';
                      $i++;
                  }


                 }

                 


                  ?>
                  </table>
              </div>

              </span>

           </div>
        </div>

</div>
</body>
</html>















            
 
</body>
</html>









