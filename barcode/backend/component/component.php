


<script src="js/jquery-3.6.0.js"></script>

<script>
function activate(element){

}



function updateComponent(element,column,component){

var status=document.getElementById("checkbox").checked;
console.log(element.innerText+column+component);

var value =element.innerText
 $.ajax({
   url: "./backend/component/component_update.php",
   type: "post",
   data: {
            value: value,
            status: status,
            column: "description",
            component: component,
            update: "component"
   },
   success: function (php_result){
      console.log(php_result);
   }
      



 })
}



</script>





  <?php

include_once "../../conn.php";
$editable="TRUE";

function component_input($column,$component,$editable) {
  $result = '<div contenteditable="'.$editable.'" onBlur="updateComponent(this,'."'".$column."','".$component."'".')" onClick="activate(this)">';
  
  echo $result;
  return $result;
}



  $check_key=FALSE;


  


  if ((isset($_POST["edit"]))&&(isset($_POST["component"])!=0)){


    $component=test_input($_POST["component"]);

    $sql="SELECT component FROM component WHERE component= '$component'";
    $query=$conn->query($sql);

    //if new WO , assign BOM and description to ""
    if (mysqli_num_rows($query)==0){    
      $editable="TRUE";
      $componentInfo_new="";
      $sql="INSERT INTO component (component, description) VALUES ('$component',  '$componentInfo_new')";
      $query=$conn->query($sql);

    }

    // if existing WO, find their BOM and description database
    else{
      $editable="TRUE";
      $sql="SELECT * FROM component WHERE component='$component'";
      $query=$conn->query($sql);
      $rows=$query->fetch_assoc();
      $componentFull_new=$rows['full'];
      $componentInfo_new=$rows['description'];


     



    }
   
    

  }
  // if no edit is clicked, data is not editable
  else{
  

    $editable="FALSE";

  }
  







// search without edit

  if ((isset($_POST["component"]))&&(isset($_POST["edit"])==0)) {
    
      $component=$_POST["component"];  
      if (strlen($component)>2){

     
         $component=test_input($_POST["component"]);
         $component="%$component%";
      




        $sql="SELECT * FROM component  ";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
         
            $component_array[]=$rows['component'];
      
            $componentInfo_array[]=$rows['description'];
              }
      


    


      }
    }


  ?>



<html>
  <head>
  <meta charset="utf-8">
  <title>barcode system</title>
  </head>





<div class="info" style="overflow-y:scroll; height:400px;">
<table>

    <?php


    if ($component_array !=null){

    $arraylength=count($component_array);
    echo  '<th class="l">Component</th>
    <th class="l">Description</th>';
    
    $i=0;
    while  ($i< $arraylength){
        echo '<tr> 
        <td ><button type="button"  data-id="'.$component_array["$i"].'" class="btn_co l" >'.$component_array[$i].'</button></td>';
       

        echo '<td >' . $componentInfo_array[$i].'</td> 
              </tr>';
        $i++;
    }

    }
    else if (isset($_POST['edit'])) {
      echo  '<th class="l">Component</th>
      <th class="s">Full</th>
      <th class="l">Description</th><tr>
      
      <td><div>'. $component.'</div></td>'; 
      if ($componentFull_new==1)
              echo '<td><input id="checkbox" type="checkbox" value="checkbox" checked="checked"></td>';
              else  echo '<td><input id="checkbox" type="checkbox" value="checkbox"></td>';
      echo '<td >' .component_input("description",$component,$editable). $componentInfo_new.'</div></td> 
    </tr>';

    }
 
    ?>


</table>我是放了水但一点烟也没
</div>




</html>
