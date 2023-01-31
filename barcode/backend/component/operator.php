


<script src="js/jquery-3.6.0.js"></script>

<script>
function activate(element){

}



function updateOperator(element,column,operator){
console.log(element.innerText+column+operator);
var value =element.innerText
 $.ajax({
   url: "./backend/component/operator_update.php",
   type: "post",
   data: {
            value: value,
            column: column,
            operator: operator,
            update: "operator"
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

function operator_input($column,$operator,$editable) {
  $result = '<div contenteditable="'.$editable.'" onBlur="updateOperator(this,'."'".$column."','".$operator."'".')" onClick="activate(this)">';
  echo $result;
  return $result;
}




  $check_key=FALSE;

  $current_date="%".date("Y\-m\-d")."%";
  


  if ((isset($_POST["edit"]))&&(isset($_POST["operator"])!=0)){

 
    $operator=test_input($_POST["operator"]);

    $sql="SELECT operator FROM worker WHERE operator= '$operator'";
    $query=$conn->query($sql);

    //if new WO , assign BOM and description to ""
    if (mysqli_num_rows($query)==0){    
      $editable="TRUE";
      $operatorInfo_new="";
      $sql="INSERT INTO worker (operator, description) VALUES ('$operator',  '$operatorInfo_new')";
      $query=$conn->query($sql);

    }

    // if existing WO, find their BOM and description database
    else{
      $editable="TRUE";
      $sql="SELECT * FROM worker WHERE operator='$operator'";
      $query=$conn->query($sql);
      $rows=$query->fetch_assoc();
      $operatorInfo_new=$rows['description'];


      



    }
   
    

  }
  // if no edit is clicked, data is not editable
  else{
  

    $editable="FALSE";

  }
  







// search without edit

  if ((isset($_POST["operator"]))&&(isset($_POST["edit"])==0)) {
    
      $operator=$_POST["operator"];  
      if (strlen($operator)>2){

     
         $operator=test_input($_POST["operator"]);
         $operator="%$operator%";
      




        $sql="SELECT * FROM worker ";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
         
            $operator_array[]=$rows['operator'];
            $operatorInfo_array[]=$rows['description'];
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
<th class="m">Operator</th>
<th class="l">Description</th>

    <?php


    if ($operator_array !=null){

    $arraylength=count($operator_array);

    $i=0;
    while  ($i< $arraylength){
        echo '<tr> 
                <td ><button type="button" name="WO" data-id="'.$operator_array["$i"].'" class="btn_op m" >'.$operator_array[$i].'</button></td>
         
                <td>'. $operatorInfo_array[$i].'</td> 
              </tr>';
        $i++;
    }

    }
    else if (isset($_POST['edit'])) {

      echo '<tr> 
      <td>'. $operator.'</div></td>
     
      <td>' .operator_input("description",$operator,$editable). $operatorInfo_new.'</div></td> 
    </tr>';

    }
 
    ?>


</table>
</div>




</html>
