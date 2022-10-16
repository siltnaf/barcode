<script src="js/jquery-3.6.0.js"></script>

<script src="js/QRcode.js"></script>

<script>
function activate(element){

}


 




var qrcode = new QRCode("QRimage");




function makeCode () {    
  var element = document.getElementById('text');
  var QRtext = element.getAttribute('value');
  console.log(QRtext);
  if (!QRtext) {
    alert("Input a text");
    return;
  }
  
  qrcode.makeCode(QRtext);
}

makeCode();

$("#text").
  on("blur", function () {
    makeCode();
  }).
  on("keydown", function (e) {
    if (e.keyCode == 13) {
      makeCode();
    }
  });





</script>





  <?php

include_once "../../conn.php";
 
$QRcode="";
$print="";
 


 // find the latest carton id
 $sql="SELECT id from carton_id order by date desc limit 1";
 $query=$conn->query($sql);
 while ($rows=$query->fetch_assoc()){
   $carton_id=$rows['id']; 
 }



 
if     (isset($_POST["QRcode"]))  {

       

      //check if QRcode in UDI
    
      $QRcode=test_input($_POST["QRcode"]);

      $sql="SELECT * from UDI where QRcode='$QRcode'";
      $query=$conn->query($sql);
      if  (mysqli_num_rows($query)==false){
    
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
                  echo json_encode($results); 


                } else {
                  
                          
                  // store the QRcode and carton id to Pack_carton table
                  $sql="INSERT INTO Pack_carton (QRcode, carton_id) VALUES ('$QRcode', '$carton_id')";
                  $query=$conn->query($sql);

                  
                  
                
                 
              }
            }

   
          }
        
          //find the prefex of carton 
          $sql="SELECT component from BOM_component where BOM='carton'";
          $query=$conn->query($sql);
          if  (mysqli_num_rows($query)==0){ 
            $carton="";

          }
          else
          {

            while ($rows=$query->fetch_assoc()){
              $carton=$rows['component']; 
            }

          }
          $carton=$carton.$carton_id;

       
          //transfer carton id to carton table      
          $sql="INSERT INTO carton (carton) VALUES ('$carton')";
          $query=$conn->query($sql);

          //create a QR list from Pack_carton
          $sql="select QRcode from Pack_carton";
          $query=$conn->query($sql); 
          if ($query->num_rows>0) while ($rows=$query->fetch_assoc()) { 
            $QRlist[]=$rows["QRcode"];}
          
           if ($QRlist!=null){
            //update UDI table of carton  
            foreach ($QRlist as $key=>$value){

              $sql="update UDI set carton='$carton' where QRcode='$value'";
              $query=$conn->query($sql); 

              }

           }
          
           

        
        


          //delete Pack_carton table
          
          $sql="DELETE FROM Pack_carton";
          $query=$conn->query($sql);


          //read wo for the same carton id
          $sql="SELECT QRcode,c.SKU,c.LOT,c.PO,a.lot,a.SN  from UDI as a
                join QRcode as b
                using (QRcode)
                join workorder as c
                using (WO)
                where a.carton='$carton'";

          $query=$conn->query($sql);
          while ($rows=$query->fetch_assoc()) { 
            
            $SN_lot[]=$rows["lot"];
            $SN_SN[]=$rows["SN"];
            $PO=$rows["PO"];
            $SKU=$rows["SKU"];
            $LOT=$rows["LOT"];
          
          }


          
          foreach ($SN_lot as $key=>$value){
            $SN[$key]=$value.$SN_SN[$key];
           }
        

          $print_QRcode='LOT#:'.$LOT.' SKU:'.$SKU.' PO:'.$PO;
          foreach($SN as $key=>$value){
            $print_QRcode=$print_QRcode. 'SN:'.$value;
          }
   

  
  
  

         




        
      


     

 


//search printer IP
$searchthis = "printIp=";
  

$handle = @fopen("../../settng.ini", "r");
if ($handle)
{
    while (!feof($handle))
    {
        $buffer = fgets($handle);
        if(strpos($buffer, $searchthis) !== FALSE)
            $matches = $buffer;
    }
    fclose($handle);
}

$Ip_addr=substr($matches, strlen($searchthis),(strlen($matches)-strlen($searchthis)));
 
$print_info='LOT#:'.$LOT.' SKU:'.$SKU.' PO:'.$PO;

$print_SN="";

foreach ($SN as $key=>$value){

$print_SN=$print_SN.' SN:'.$value;


}

$print_QRcode=$print_info.$print_SN;

$data = ' 
^XA
 ^FT50,350^BQN,2,7
 ^FH\^FDLA,'.$print_QRcode.'^FS
 ^FT325,130^A0N,38,33^FH\^CI28^FDLOT#: ^FS^CI27
 ^FT325,170^A0N,32,38^FH\^CI28^FDSKU: ^FS^CI27
 ^FT325,210^A0N,32,38^FH\^CI28^FDPO: ^FS^CI27

 ^FT419,130^A0N,38,38^FH\^CI28^FD'.$LOT.'^FS^CI27
 ^FT419,170^A0N,32,33^FH\^CI28^FD'.$SKU.'^FS^CI27
 ^FT419,210^A0N,32,33^FH\^CI28^FD'.$PO.'^FS^CI27';
 
 $lengh=count($SN);
 $data_SN="";
 foreach($SN as $key=>$value){
$data_SN=$data_SN.'
^FT325,'.(string)(250+($key)*40).'^A0N,34,38^FH\^CI28^FDSN: ^FS^CI27
^FT419,'.(string)(250+($key)*40).'^A0N,35,35^FH\^CI28^FD'.$value.'^FS^CI27';

 }

 $data_carton= '^BY2,3,68^FT700,346^BAB,,Y,N,N
 ^FD'.$carton.'^FS';
 
 $data=$data.$data_SN.$data_carton.'^XZ';
 
 //echo $data;

//print to label printer
/*


if(($connection = fsockopen($Ip_addr,9100,$errno,$errstr))===false){ 
    echo 'Connection Failed' . $errno . $errstr; 
} 



    

#send request 
$fput = fputs($connection, $data, strlen($data)); 

#close the connection 
fclose($connection); 


  */    





          


 

  //check if UDI exist in the UDI table
  ?>




<span class="halfpane">


<span id="text" value="<?php echo $print_QRcode ?>" >

<div id="QRimage"></div>
 <h1 style=" margin-top:10px; margin-left:100px">  Carton#:<?php echo ''.$carton?> </h1>

</span>

</span>
<span  class="halfpane" style="margin-top:0px; background-color: rgb(215, 217, 236)" >
<span  class="info" style=" height:300px;width:300px">
<div class="QRinfo">

   <h2 >  LOT#:<?php echo ''.$LOT?> </h2>
   <h2 > SKU:<?php echo '    '.$SKU?> </h2>
   <h2 > PO:<?php echo '    '.$PO?> </h2>
   <?php 
        foreach ($SN as $key=>$value){
          echo '<h2> SN: '.$value.'</h2>';

        } 
        ?>
   <h2 style="color: rgb(200, 200, 200)"> <?php echo $UDI?></h2>

</div>
</span>
</span>