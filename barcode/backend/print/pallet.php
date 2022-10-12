<script src="js/jquery-3.6.0.js"></script>

<script src="js/QRcode.js"></script>

<script>
function activate(element){

}


 




 
var qrcode = new QRCode("QRimage", {
 
    width: 600,
    height: 600,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});



function makeCode () {    
  var element = document.getElementById('text1');
  var QRtext = element.getAttribute('value');
  console.log(QRtext);
  if (!QRtext) {
    alert("Input a text");
    return;
  }
  
  qrcode.makeCode(QRtext);
}

makeCode();

$("#text1").
  on("blur", function () {
    makeCode();
  }).
  on("keydown", function (e) {
    if (e.keyCode == 13) {
      makeCode();
    }
  });

 



  function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
        }


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
           
          //  foreach($WO as $key=>$value)
           // {
              $value='PP1677086';                        //assume one value
                foreach($pLabel[$value] as $key2 =>$value2){
                  $sql="SELECT lot,SN from UDI where QRcode='$value2'";
                  $query=$conn->query($sql);
                  while ($rows=$query->fetch_assoc()) { 
  
                    $SN[]=$rows["lot"].$rows["SN"];     //replace the UDI with SN
                    
                  
                
                  }
                }

               
                  for ($i=0;$i<300;$i++){              //create dummy data
                    $SN[$i]="10000".$i;
  
                  }

      
                  $print_QRcode_prefix='LOT#:'.$pLabel[$value]['LOT'].' SKU:'.$pLabel[$value]['SKU'].' PO:'.$pLabel[$value]['PO'];
              
                  //group SN into 30 unit per group
  
                  $group_size=100;
                  $loop=count($SN);
                  
                  $i=0;
                  $j=0;
                  $k=0;
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
            
                 



                 
                
                 

                






                
             // }  

               
                

                
                 
                 
                  
                
            
             




           


            

        
        
        


  
  
  

         




        
      


     

 




          


 

  //check if UDI exist in the UDI table
  


?>



<span class="widepane">


 
  
 <div id="printableArea">
  <span id="text" value= "<?php echo $print_QRcode[0] ?>" style="width:90%" class="single_record" >

  <div id="QRimage"></div>
   <h1 style=" margin-top:300px; margin-left:100px">  Pallet#: <?php echo $pallet ?> </h1>
   <input type="button" onclick="printDiv('printableArea')" value="print" /></input>
  </span>

  <span id="text1" value= "<?php echo $print_QRcode[1] ?>" style="width:90%" class="single_record" >

<div id="QRimage"></div>
 <h1 style=" margin-top:300px; margin-left:100px">  Pallet#: <?php echo $pallet ?> </h1>
 <input type="button" onclick="printDiv('printableArea')" value="print" /></input>
</span>

</div>
                



