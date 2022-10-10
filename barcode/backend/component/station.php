

<script src="js/jquery-3.6.0.js"></script>

<script>
function activate(element){

}



function updateStation(element,column,station){

var value =element.innerText




  $.ajax({
   url: "./backend/component/station_update.php",
   type: "post",
   data: {
            value: value,
            column: column,
            station: station,
            update: "station"
   },
   success: function (php_result){
      console.log(php_result);
   }




 });






}



</script>





  <?php

include_once "../../conn.php";
$editable="TRUE";

function station_input($column,$station,$editable) {
  $result = '<div contenteditable="'.$editable.'" onBlur="updateStation(this,'."'".$column."','".$station."'".')" onClick="activate(this)">';
  return $result;
}




  $check_key=FALSE;

  $current_date="%".date("Y\-m\-d")."%";
  


  if ((isset($_POST["edit"]))&&(isset($_POST["station"])!=0)){

    

    $station=test_input($_POST["station"]);

    $sql="SELECT station FROM station WHERE station= '$station';";
    $query=$conn->query($sql);

    //if new WO , assign BOM and description to ""
    if (mysqli_num_rows($query)==0){  
      
      $editable="TRUE";
      $stationInfo_new="";
      $version_new="";
      $sql="INSERT INTO station (station, version, description) VALUES ('$station', '$version_new', '$stationInfo_new')";
      $query=$conn->query($sql);

    }

    // if existing WO, find their BOM and description database
    else{
      $editable="TRUE";
      $sql="SELECT * FROM station WHERE station='$station'";
      $query=$conn->query($sql);
      $rows=$query->fetch_assoc();
      $version_new=$rows['version'];
      $stationInfo_new=$rows['description'];


      



    }
   
    

  }
  // if no edit is clicked, data is not editable
  else{
  

    $editable="FALSE";

  }
  







// search without edit

  if ((isset($_POST["station"]))&&(isset($_POST["edit"])==0)) {
    
      $station=$_POST["station"];  
      if (strlen($station)>2){

     
         $station=test_input($_POST["station"]);
         $station="%$station%";
      




        $sql="SELECT * FROM station  ";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
            $version_array[]=$rows['version']; 
            $station_array[]=$rows['station'];
            $stationInfo_array[]=$rows['description'];
              }
      


    


      }
    }


  ?>



<html>
  <head>
  <meta charset="utf-8">
  <title>barcode system</title>
  </head>





<div class="info"  style="overflow-y:scroll; height:400px;">
<table  >
<th class="l">Station</th>
<th class="xs">Rev.</th>
<th class="l">Description</th>

    <?php


    if ($station_array !=null){

    $arraylength=count($station_array);

    $i=0;
    while  ($i< $arraylength){
        echo '<tr> 
                <td ><button type="button" name="WO" data-id="'.$station_array["$i"].'" class="btn_st m" >'.$station_array[$i].'</button></td>
                <td >' . $version_array[$i].'</td> 
                <td >' .$stationInfo_array[$i].'</td> 
              </tr>';
        $i++;
    }

    }
    else if (isset($_POST['edit'])) {

      echo '<tr> 
      <td>' .station_input("station",$station,$editable). $station.'</div></td> 
      <td>' .station_input("version",$station,$editable). $version_new.'</div></td> 
      <td>' .station_input("description",$station,$editable). $stationInfo_new.'</div></td> 
    </tr>';

    }
 
    ?>


</table>
</div>




</html>
