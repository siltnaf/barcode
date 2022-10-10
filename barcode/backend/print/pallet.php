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
 


        // find the latest pallet id
        $sql="SELECT id from pallet_id order by date desc limit 1";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
        $pallet_id=$rows['id']; 
        }


        
          //find the prefex of pallet
          $sql="SELECT component from BOM_component where BOM='pallet'";
          $query=$conn->query($sql);
          if  (mysqli_num_rows($query)==0){ 
            $pallet="";

          }
          else
          {

            while ($rows=$query->fetch_assoc()){
              $pallet=$rows['component']; 
            }

          }
          $pallet=$pallet.$pallet_id;

       

          //create a carton list from Pack_pallet
          $sql="select carton from Pack_pallet";
          $query=$conn->query($sql); 
          if ($query->num_rows>0) while ($rows=$query->fetch_assoc()) { 
            $cartonlist[]=$rows["carton"];}
          
           if ($cartonlist!=null){
            //update UDI table of carton  
            foreach ($cartonlist as $key=>$value){

              $sql="update carton set pallet='$pallet' where carton='$value'";
              $query=$conn->query($sql); 

              }

           }
          
           

        
        


          //delete Pack_carton table
          
          $sql="DELETE FROM Pack_pallet";
          $query=$conn->query($sql);

        
          //read all UDI from pallet

         $sql=" SELECT QRcode,WO FROM carton as a 
                join UDI as b
                using (carton)
                join QRcode as c 
                using (QRcode)
                where a.pallet='$pallet'";
         $query=$conn->query($sql);
         while ($rows=$query->fetch_assoc()) { 
            $WO[]=$rows["WO"];
            $UDI[]=$rows["QRcode"];
           
           }



            //group according to $WO_UDI
         //  foreach ($WO as $key=>$value){

           // $WO_UDI[$value][]=$UDI[$key];
            
           //}

         
            foreach ($WO as $key=>$value)
            {
                  // find the PO,SKU,LOT information
                      $sql="SELECT PO,LOT,SKU from workorder where WO='$value'";
                      $query=$conn->query($sql);
                      while ($rows=$query->fetch_assoc()) { 
      
                        $pLabel[$value]['PO']=$rows["PO"];
                        $pLabel[$value]['LOT']=$rows["LOT"];
                        $pLabel[$value]['SKU']=$rows["SKU"];
                    
                      }
              
                        $pLabel[$value][]=$UDI[$key];
                       
                    }
                $WO=array_unique($WO);
           
            foreach($WO as $key=>$value)
            {
              $value='PP1677086';
                foreach($pLabel[$value] as $key2 =>$value2){
                  $sql="SELECT lot,SN from UDI where QRcode='$value2'";
                  $query=$conn->query($sql);
                  while ($rows=$query->fetch_assoc()) { 
  
                    $SN[]=$rows["lot"].$rows["SN"];     //replace the UDI with SN
                    
                  
                
                  }
                  
                }
              
                      
                $print_QRcode_prefix='LOT#:'.$pLabel[$value]['LOT'].' SKU:'.$pLabel[$value]['SKU'].' PO:'.$pLabel[$value]['PO'];
                $i=0;
                $j=0;
                while ($i<200)
                
                  $print_QRcode_SN[$j][$i]=' SN:'.$SN[$i];
                  $i++;
                  if ($i>=200) {$i=0;$j++;}
                }
   
                var_dump($print_QRcode_SN);
                 
                 
                  
                
            
             

                    //}

                    //find the SN information from UDI table





           


            

        die;
        
        
        


  
  
  

         




        
      


     

 


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
 <h1 style=" margin-top:150px; margin-left:100px">  Carton#:<?php echo ''.$carton?> </h1>

</span>

</span>
<span  class="halfpane" style="margin-top:50px; background-color: rgb(215, 217, 236)" >
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