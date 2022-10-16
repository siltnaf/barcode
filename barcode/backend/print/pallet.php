<script src="js/jquery-3.6.0.js"></script>

<script src="js/QRcode.min.js"></script>

<script src="js/barcode.min.js"></script>

<script>

function printDiv(){
  
 var printContents = document.getElementById("printableArea").innerHTML;
 var originalContents = document.body.innerHTML;



 document.body.innerHTML = printContents;

 window.print();

 document.body.innerHTML = originalContents;



}
 



function makeCode () {  

// All inputs that contain the value
$qrs = $('.qr_value');

// Create a new instance of the QRCode for each input
$qrs.each(function(index, item){
    
    // We cant hace same id multiple times, so, we need to create dynamic ids,
    // thats why we are concatenating the index to the id string
    let containerQr =  "qrcode_"+index;

    // Create QR
    let qrcode = new QRCode(containerQr, {
        text: item.value, // value to read on qr
        width: 220,
        height: 220,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H,
    });
});

}

makeCode();
JsBarcode(".barcode").init();

$(document).ready(function() {

  printDiv()
})

//makeCode().then();
  




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

            if ($WO==null) die;

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
             
              //$value='PP1677086';                        //assume one value
                foreach($pLabel[$value] as $key2 =>$value2){
                  $sql="SELECT lot,SN from UDI where QRcode='$value2'";
                  $query=$conn->query($sql);
                  while ($rows=$query->fetch_assoc()) { 
  
                    $SN[]=$rows["lot"].$rows["SN"];     //replace the UDI with SN
                    
                  
                
                  }
                }

               
                  for ($i=0;$i<200;$i++){              //create dummy data
                    $SN[$i]="10000".$i;
  
                  }

      
                  $print_QRcode_prefix='LOT#:'.$pLabel[$value]['LOT'].' SKU:'.$pLabel[$value]['SKU'].' PO:'.$pLabel[$value]['PO'];
              
                  //group SN into 30 unit per group
  
                  $group_size=50;
                  $loop=count($SN);
                  
                  $i=0;
                  $j=0;
                  if ($print_QRcode==null) $k=0; else $k=count($print_QRcode);
                  $SN_2D = array(array());
                  $SN_group = array();
                 while ($i<$loop){
                     
                      if ($j>$group_size){
                        $print_QRcode[$k]=$print_QRcode_prefix.' '.$SN_group[$k];
                        $j=0;
                        $k++;
                      }
                      $SN_group[$k]=$SN_group[$k].'SN:'.(string)$SN[$i].' ';
                      $SN_2D[$k][$j]=$SN[$i];
                      
                  
                      $j++;
                      $i++;

                      


                  }
                $print_QRcode[$k]=$print_QRcode_prefix.' '.$SN_group[$k];
              
            
                }



          //delete Pack_carton table
          
          $sql="DELETE FROM Pack_pallet";
          $query=$conn->query($sql);


  
  


?>



<span class="widepane" >
  
 <div id="printableArea" style="overflow-y:scroll; height:400px">

 <?php  
 foreach($print_QRcode as $key => $value){
  if (($key%6)==0)  {
    echo '<div style="page-break-before: always;"></div>';
   
  
  
  
  }
  if (($key%2)==0){
    echo '<div   class="halfpane" style= "margin-left:50px" >';
  } else {
    echo '<div   class="halfpaneright" style= "margin-left:50px" >';
  }
 
  $SKU_pos=strpos($value,'SKU:');
  $PO_pos=strpos($value,'PO:');
  $SN_pos=strpos($value,'SN:');
  $LOT=substr($value,0,$SKU_pos);
  $SKU=substr($value,$SKU_pos,$PO_pos-$SKU_pos);
  $PO=substr($value,$PO_pos,$SN_pos-$PO_pos);

echo '

'.$LOT.'<br>'.$SKU.'<br>'.$PO.'
<input type="hidden" class="qr_value" value="'.$value.'">
    <div id="qrcode_'.$key.'"></div> <br>
    
</div>';




}

if ((count($print_QRcode)%2)==0){
  echo '<div   class="halfpane" style="margin-top:20px" >';
} else {
  echo '<div   class="halfpaneright"  style="margin-top:20px" >';
}
 
 ?>
 
 <svg class="barcode" 
  jsbarcode-format="code128"
  jsbarcode-value="<?php echo $pallet ?>"
  jsbarcode-textmargin="0"
  jsbarcode-fontoptions="bold">
</svg>
</div>
</span>       



