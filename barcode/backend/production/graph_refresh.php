<?php

include_once "conn.php";
$current_date=date("Y\-m\-d");

//$current_date="2022-03-22";

$sql="SELECT completed FROM productionDetails where  productionDate='$current_date'";

$query=$conn->query($sql);
  if  ($query->num_rows>0) 
    while ($rows=$query->fetch_assoc()){
        
        $graph_completed[]=$rows['completed'];
        }


        echo json_encode($graph_completed);






?>