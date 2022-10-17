
<script src="js/jquery-3.6.0.js"></script>

<script src="js/QRcode.js"></script>

<script>
function activate(element){

}


 




var qrcode = new QRCode("QRimage",{


    width: 200,
    height: 200,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H,



});




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
 










  if (isset($_POST["QRcode"])){

          
          $QRcode=test_input($_POST["QRcode"]);
          $UDI=$QRcode;
   

  }

  //check if UDI exist in the UDI table
        
  $sql="SELECT QRcode from QRcode where QRCode='$QRcode'";
  $query=$conn->query($sql);
  if  (mysqli_num_rows($query)==0){
      die ("UDI not found in QRcode");



  }else 
  {
// check if QRcode alread existing in UDI table
      $sql="SELECT QRcode from UDI where QRCode='$QRcode'";
      $query=$conn->query($sql);
      if  (mysqli_num_rows($query)==0){
        
            $sql="INSERT INTO UDI (QRcode) VALUES ('$QRcode')";
            $query=$conn->query($sql);

            decode_UDI($QRcode);

           
        


      }

      $sql="SELECT b.LOT,b.SKU,b.SN_suffix,b.SN_prefix from QRcode as a
      join workorder as b
      using (WO)
      where a.QRcode='$QRcode'";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
          $LOT=$rows['LOT']; 
          $SKU=$rows['SKU'];
          $SN_pre= $rows['SN_prefix'];
          $SN_suf= $rows['SN_suffix'];
          
              }
          
        $sql="SELECT lot,SN from UDI where QRcode='$QRcode'";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
          $SN_lot=$rows['lot']; 
          $SN_SN=$rows['SN'];
          
              }
 


          $SN=$SN_pre.$SN_lot.$SN_SN.$SN_suf;
        

        }





 


//search printer IP
  $searchthis = "printIp=";
  
  $handle = @fopen("../../settng.ini", "r");
  //$handle = @fopen("../../settng.ini", "r");
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
   
  echo $IP_addr;
  $print_QRcode='LOT#:'.$LOT.' SKU:'.$SKU.' SN:'.$SN;

  $data = ' 
  ^XA
   ^FT100,293^BQN,2,7
   ^FH\^FDLA,:'.$print_QRcode.'^FS
   ^FT325,138^A0N,38,33^FH\^CI28^FDLOT#: ^FS^CI27
   ^FT325,195^A0N,32,38^FH\^CI28^FDSKU: ^FS^CI27
   ^FT412,138^A0N,38,38^FH\^CI28^FD'.$LOT.'^FS^CI27
   ^FT419,195^A0N,32,33^FH\^CI28^FD'.$SKU.'^FS^CI27
   ^FT325,250^A0N,34,38^FH\^CI28^FDSN: ^FS^CI27
   ^FT415,251^A0N,35,35^FH\^CI28^FD'.$SN.'^FS^CI27

   ^XZ';

 

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
       

?>



<span class="leftpane">


<span id="text" value="<?php echo $print_QRcode ?>" >

<div id="QRimage"></div>


</span>

</span>
<span  class="info" style=" height:200px;width:400px">
<div class="QRinfo">

   <h1>  LOT#:<?php echo ''.$LOT?> </h1>
   <h1> SKU:<?php echo '    '.$SKU?> </h1>
   <h1> SN:<?php echo '      '.$SN?> </h1>
   <h2 style="color: rgb(200, 200, 200)"> <?php echo $UDI?></h2>

</div>

</span>