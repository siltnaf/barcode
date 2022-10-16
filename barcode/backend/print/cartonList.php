


  <?php

include_once "../../conn.php";
 




if (isset($_POST["reset"])){


  $sql="DELETE FROM Pack_carton";
  $query=$conn->query($sql);




}









  if (isset($_POST["QRcode"])){

          
          $QRcode=test_input($_POST["QRcode"]);
          $counter=test_input($_POST["counter"]);
         
         
          // create new carton id
          if ($counter==1){

            $sql="INSERT INTO carton_id (date) VALUES ( NOW())";
            $query=$conn->query($sql);

          }

          // find the latest carton id
          $sql="SELECT id from carton_id order by date desc limit 1";
          $query=$conn->query($sql);
          while ($rows=$query->fetch_assoc()){
            $carton_id=$rows['id']; 
          }

          //check if QRcode in UDI

          $sql="SELECT * from UDI where QRcode='$QRcode'";
          $query=$conn->query($sql);
          if  (mysqli_num_rows($query)==0){
        
            $results = array(
                'status' => 'error',
                'msg' => 'QRcode not found in database',
                
             );





          }
          else{

            //check if QRcode is repeated

            $sql="SELECT * from Pack_carton where QRcode='$QRcode'";
            $query=$conn->query($sql);
            if  (mysqli_num_rows($query)!=0){
          
              $results = array(
                  'status' => 'error',
                  'msg' => 'QRcode repeatedly scan',
                  
               );
  
            }
            else {

              // store the QRcode and carton id to Pack_carton table
              $sql="INSERT INTO Pack_carton (QRcode, carton_id) VALUES ('$QRcode', '$carton_id')";
              $query=$conn->query($sql);
            
            
              $results = array(
                  'status' => 'sucess',
                  'msg' => 'OK',
                  
              );


            }

      


        
           
    
          }
          
         echo json_encode($results);

  }

  //check if UDI exist in the UDI table
        
  

?>
