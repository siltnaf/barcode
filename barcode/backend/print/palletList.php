

  <?php

include_once "../../conn.php";
 




if (isset($_POST["reset"])){


  $sql="DELETE FROM Pack_pallet";
  $query=$conn->query($sql);




}









  if (isset($_POST["QRcode"])){

          
          $carton=test_input($_POST["QRcode"]);
          $counter=test_input($_POST["counter"]);
         
         
          // create new carton id
          if ($counter==1){

            $sql="INSERT INTO pallet_id (date) VALUES ( NOW())";
            $query=$conn->query($sql);

          }

          // find the latest carton id
          $sql="SELECT id from pallet_id order by date desc limit 1";
          $query=$conn->query($sql);
          while ($rows=$query->fetch_assoc()){
            $pallet_id=$rows['id']; 
          }

          //check if QRcode in carton table

          $sql="SELECT * from UDI where carton='$carton'";
          $query=$conn->query($sql);
          if  (mysqli_num_rows($query)==0){
        
            $results = array(
                'status' => 'error',
                'msg' => 'carton not found in database',
                
             );





          }
          else{

            //check if QRcode is repeated

            $sql="SELECT * from Pack_pallet where carton='$carton'";
            $query=$conn->query($sql);
            if  (mysqli_num_rows($query)!=0){
          
              $results = array(
                  'status' => 'error',
                  'msg' => 'carton repeatedly scan',
                  
               );
  
            }
            else {

              // store the QRcode and pallet id to Pack_pallet table
              $sql="INSERT INTO Pack_pallet (carton, pallet_id) VALUES ('$carton', '$pallet_id')";
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
