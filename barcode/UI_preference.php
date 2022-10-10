<script src="./js/jquery-3.6.0.js"></script>
<script>
      $(function(){
        $("#nav-placeholder").load("./nav.html");
      });
      
 </script>



<?php


include_once "conn.php";

$sql="SELECT * FROM preference ";
$query=$conn->query($sql);
while ($rows=$query->fetch_assoc()){
    $id[]=$rows['id'];
    $class[]=$rows['class'];
    $name[]=$rows['name'];
    $info[]=$rows['description'];
    $value[]=$rows['value'];
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
            Preference
            </h1>
            <div id="nav-placeholder">
            </div>

           


          </div>
            
  </div>
  <div class="main" >     











            <span class="widepane" id="result">

          
            <div class="info" style="overflow-y:scroll; height:400px;">
            
           <h2> Parameters   </h2>
              <table>
                <th class="s">Id</th>
              <th class="m">Class</th>
              <th class="m">Name</th>
              <th class="l">Description</th>
              <th class="s">Value</th>

                  <?php
                  if ($id!=null) foreach ($id as $key =>$number){
                    echo '<tr>
                          <td>'.$number.'</td>
                          <td>'.$class[$key].'</td>
                          <td>'.$name[$key].'</td>
                          <td>'.$info[$key].'</td>
                          <td><button id="o_edit" value="o_edit" name="o_edit">'.$value[$key].'</button></td> 
                          
                          </tr>';




                  }

               
                 


                  ?>
                  </table>
              </div>







            </span>

           
    </div>          

</body>
</html>

