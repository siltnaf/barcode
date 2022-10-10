<?php
 $check_key=FALSE;
 include_once "conn.php";
$station = test_input($_POST["station"]);
 $BOM = test_input($_POST["BOM"]);
 $action=test_input($_POST["action"]);
 $operator=test_input($_POST["operator"]);
 $QR1=test_input($_POST["QR1"]);
 $QR2=test_input($_POST["QR2"]);
//update operator list

 $sql="INSERT INTO worker (operator) VALUES ('$operator')";
 $query=$conn->query($sql);

 $sql="SELECT QRcomponent from BOM_station where BOM='$BOM' and station='$station'";
 $query=$conn->query($sql);
 if (mysqli_num_rows($query)==0) die ('QRkey not found');
 else {
    while ($rows=$query->fetch_assoc()){
        //    $pid=$rows['production_id']; 
            $QRcomp=$rows['QRcomponent'];
              }


 } 

// QC station request is true then proceed

if (strpos('0'.$station, 'QC')!=false){
    
       
       
    
        $result=test_input($_POST["result"]);
      
     
        
        //echo json_encode($station);
       
//register operator to worker
// cancel qci result    




     
     


       

// from bOM and station find the production id on matching production date<= today

    $sql="SELECT a.production_id,a.WO FROM barcode.productionDetails as a
            join workorder as b
            using (WO)
            join BOM_station as c
            using (BOM)
            where  (a.endDate IS null) and (a.startDate <=CURDATE()) and (c.BOM='$BOM') and (c.station='$station') 
            order by a.production_id DESC";
 

  
    $query=$conn->query($sql);

    if (mysqli_num_rows($query)==0) die( "Incorrect scan code");

    while ($rows=$query->fetch_assoc()){
    //    $pid=$rows['production_id']; 
        $WO=$rows['WO'];
          }
    
         

// if action = CANCEL, then return 


          if ($action=="CANCEL"){

            $sql="SELECT station FROM workflow where (QRcode = '$QR1') and (station = '$station');";
            $query=$conn->query($sql);
            if (mysqli_num_rows($query)==0) die( "QRcode not in workflow");
             else{

                 
                 $sql="DELETE FROM workflow WHERE (QRcode = '$QR1') and (station = '$station') ;"; 
                 $query=$conn->query($sql);
                 echo "ok,".$WO;
                 return;  
             }
            
         }
         





 // check previous station

           

            $sql="SELECT station, 
            (select station FROM BOM_station as s1
            WHERE s1.stationOrder < s.stationOrder and s1.BOM=s.BOM
            ORDER BY stationOrder DESC LIMIT 1) as p_station
            FROM BOM_station as s
            WHERE  station='$station'  "; 

            $query=$conn->query($sql);
             while ($rows=$query->fetch_assoc()){
            $last_station=$rows['p_station'];

            }

            //check if QRcode match QRcomponent

            if (strpos('0'.$QR1, $QRcomp) == false)  die ("QRcode error");

            // if last station is beginning,assume result is pass and regiester Qrcode table
            
            if  ($last_station==null){

                $sql="SELECT * from QRcode where QRcode='$QR1'";
                $query=$conn->query($sql);

                if (mysqli_num_rows($query)==0) 
                 {

                    $sql="INSERT INTO QRcode (QRcode, WO, result) VALUES ('$QR1', '$WO', 'PASS');";
                    $query=$conn->query($sql);
                    $last_result="PASS";

                }
            
               

            }



            $sql="SELECT result FROM QRcode where QRcode='$QR1' ;";
            $query=$conn->query($sql);
            while ($rows=$query->fetch_assoc()){
                $last_result=$rows['result'];
              
               }
                

   

             // if this is a new component insert result 
    
            if ($last_result==null){


             
            $sql="INSERT INTO QRcode (QRcode, WO,result) VALUES ('$QR1','$WO', '$result');";
            $query=$conn->query($sql);

     

          
            
    
            }




          
    //if (($last_result=="FAIL")||($last_result==null)) die ("previous result is FAIL"); 


      /*   
    // check last result and control sequence
        if ($last_station!=null) {
        if (($last_result=="NA")||($last_result==null)) die("QCI sequence error");
   
  } */ 


  

                //insert to part table


            
            $sql="INSERT INTO material (part,component ) VALUES ('$QR1','$QRcomp');";
            $query=$conn->query($sql);   


            $sql="INSERT INTO assembly (QRcode,part, layer) VALUES ('$QR1','$QR1','1'); ";
            $query=$conn->query($sql);

        
   
         
            // update QRcoode table result
            $sql="UPDATE QRcode SET result = '$result' WHERE (QRcode = '$QR1') ;";
            $query=$conn->query($sql);

                


            //if same QCI station and previous result is fail, allow same station to update the result again
            $sql="SELECT result from workflow where QRcode='$QR1' and station='$station';";
            $query=$conn->query($sql);
            if (mysqli_num_rows($query)==0){


                //update workflow
                $sql="INSERT INTO workflow (QRcode, station, operator,  result ) VALUES ('$QR1','$station', '$operator', '$result' );";
                $query=$conn->query($sql);

            }
            else {
                $sql="UPDATE workflow SET result= '$result' where QRcode='$QR1' and station='$station';";
                $query=$conn->query($sql);

            }










      

     
     echo "ok,".$WO;







}


else 

// if this is BND or SMT station

if  ((strpos('0'.$station, 'BND')!=false))

{
  

    if ($action=="CANCEL"){

       
             echo "ok,".$WO;
             return;  
         }
        
 
    



// check if the production qty match the target qty, if yes stop the WIP 

    $sql="SELECT a.WO,c.componentCount FROM productionDetails as a
            join workorder as b
            using (WO)
            join BOM_station as c
            using (BOM)
            where  (a.output<b.qty)and (a.startDate<'CURDATE()') and (c.station='$station') and (BOM='$BOM')";

// find the config for the station 

    $query=$conn->query($sql);

    if (mysqli_num_rows($query)==0) die( "No production for this station");

    while ($rows=$query->fetch_assoc()){
        $QRcount=$rows['componentCount'];
     //   $pid=$rows['production_id']; 
        $WO=$rows['WO'];       
        
    }

   

   //store part into $part array and create join a text list


// join the part list and find if the key QRcode existing in the full_list

    
        $QR_parent=-1;
        $i=0;
  //      $full_list="";
        while  ($i< $QRcount){
            
            $QR[$i]= test_input($_POST["QR".($i+1)]);
         //   echo $part[$i];
            if (substr_count($QR[$i], $QRcomp)==1) $QR_parent=$i;   // record the position of key QRcode

    //            $full_list=$full_list.substr($part[$i],0,11);
            
            //print( $QR[$i]);
            $i++;
        }
    
            if ($QR_parent==-1) {
                
            
                die ("no Tracking QRcode");
            }



          // count the part array and if there is a array value is "" then report missing part
    
          foreach ($QR as $value) {
            //echo($value);
            if ($value=="") die ('missing part');

          }
         
      
          $value=$QR;

          // find if parts are repeated 
          $value=array_unique($value);
          if (count($value)<count($QR)) die ("repeated parts");
             

 

         // create part list, component name has 16 character with ',' as delimiter
         foreach ($QR as $value){ 
            if    ( (strpos($value,',',0)!=null) &&(strpos($value,',',0)<17))
            {
                $temp=substr($value,0,strpos($value,',',0));

            
                $QR_prefix[]=$temp;
            
            }
            else $QR_prefix[]=substr($value,0,16);
        
        }
     
      //  echo "QRlist";
     //   print_r($QRList);
      //  print_r($partList);

        //  get the list of component from WO 

        $sql="SELECT component,qty FROM BOM_component where  BOM='$BOM';";
        $query=$conn->query($sql);
        while ($rows=$query->fetch_assoc()){
            $temp=$rows['component'];
            if  (strpos($temp,',LOT',0)!=0){

                $lotcomponent_prefix[]=substr($temp,0,strpos($temp,',LOT',0));
                $lqty[]=$rows['qty'];
            }else
            {
                $component_prefix[]=$temp;
                $qty[]=$rows['qty'];

            }

            
        }

 

   
        if ($component_prefix==null) die('BOM error');
            
            
        
        //flatten component list according to qty
        $i=0;$j=0; $k=0;
        while ($i<count($component_prefix)){
        
        for ($j=0;$j<$qty[$i];$j++){
        
                $comp_prefix[$k]=$component_prefix[$i];
                $k++;
                
            }
            
        $i++; 
        }

        $i=0;$j=0; 

        if (!empty($lotcomponent_prefix)){


            while ($i<count($lotcomponent_prefix)){
        
                for ($j=0;$j<$lqty[$i];$j++){
                
                        $comp_prefix[$k]=$lotcomponent_prefix[$i];
                        $k++;
                        
                    }
                    
                $i++; 
                }


        }

      



  
   
       
        //compare the configuration in BOM and actual scanned part

        if (array_intersect($QR_prefix, $comp_prefix) != $QR_prefix) {
            die (" number of components not match workorder");
      }     
          
    
        
        //check if the component already exist material table and not in QRtable
        $i=0;
        
        for ($i=0; $i< $QRcount;$i++){
            if ($i!=$QR_parent){   //exclude the key scancode items
              
                $sql="SELECT * FROM material as a
                        LEFT JOIN QRcode as b 
                        on a.part = b.QRcode
                        WHERE (b.QRcode IS NULL) and (a.part='$QR[$i]')";

                $query=$conn->query($sql);
               
                if  ($query->num_rows>0){ die ("part used in another assembly"); }  



            }

        }

    
            // check if this is new in QRcode table then store the key QRcode parts into QRcode table
            $sql="SELECT * from QRcode where QRcode='$QR[$QR_parent]'";
            $query=$conn->query($sql); 


             if (mysqli_num_rows($query)==0) {

                $sql="INSERT INTO QRcode (QRcode,WO, result) VALUES ('$QR[$QR_parent]', '$WO','PASS')";
                $query=$conn->query($sql);  

                $layer_base=0;
              
               


             }

             // find the previous max layer
             
               

                $WO_old=[];
                $QRcode_old=[];
              
                foreach ($QR as $key=>$value){
                    $sql="SELECT * from QRcode WHERE (QRcode = '$value')";
                    $query=$conn->query($sql);  
                    while ($rows=$query->fetch_assoc()){
                        $WO_old[]=$rows['WO'];
                        $QRcode_old[]=$rows['QRcode'];

                    }
             
                
                }
           
                foreach ($WO_old as $key=>$value){
                    if ($value!=$WO){
                        //save the history of change in QRcode_history
                        $sql="INSERT INTO QRcode_history (QRcode,WO) VALUES ('$QRcode_old[$key]', '$value')";
                        $query=$conn->query($sql);  
    
                           //update the QRocde with new WO 
    
                        $sql="UPDATE QRcode SET WO = '$WO' WHERE (QRcode = '$QRcode_old[$key]')";
                        $query=$conn->query($sql);   
    
                       
    
                    }



                }
             
                
                
               
            


                    foreach($QR as $value){
                        $sql="SELECT layer from assembly  where  (QRcode='$value') ";
                        $query=$conn->query($sql);  
                        while ($rows=$query->fetch_assoc()){
                            $layer[]=$rows['layer'];                   
                        }

                    }
            
                //echo $layer;
                if ($layer!=null) $layer_base=max($layer);
                else $layer_base=0;

              
             
             


           



            //save the part to material table
         


            $compCount=count($component_prefix);
                
            $j=0;
            while ($j<$compCount){

            foreach ($QR_prefix as $key=>$value){
                if ($value== $component_prefix[$j]){
                    $temp=$QR[$key];    
                    $temp=substr($temp,(strlen($QR_prefix[$key])+1));
                    $manufacture=substr($temp,0,strpos($temp,',',0));
                    $temp=substr($temp,strpos($temp,',',0)+1);
                    $lot=substr($temp,0,strpos($temp,',',0));
                    $sn=substr($temp,strpos($temp,',',0)+1);
                    
                    
                $sql="INSERT INTO material (part, component,manufacture,lot,sn) VALUES ('$QR[$key]', '$component_prefix[$j]','$manufacture','$lot','$sn');";
                $query=$conn->query($sql);
                }

            }
                

            $j++;
            }

        

            //save the lot to materialLot table
           

            if (!empty($lotcomponent_prefix)){

                $compCount=count($lotcomponent_prefix);
                            
                $j=0;
                while ($j<$compCount){
    
                foreach ($QR_prefix as $key=>$value){
                    if ($value== $lotcomponent_prefix[$j]){
                    $material_part=$QR[$key];    
                    $material_info=substr($material_part,(strlen($QR_prefix[$key])+1));
                    $material_manufacturer=substr($material_info,0,strpos($material_info,','));
                    $material_lot=substr($material_info,(strpos($material_info,',')+1));
                   

                   $sql="INSERT INTO materialLot (QRcode, part,component,manufacturer, lot) VALUES ('$QR[$QR_parent]','$material_part', '$lotcomponent_prefix[$j]','$material_manufacturer','$material_lot');";
                    
                    $query=$conn->query($sql);
                    }
    
                }
                    
    
                $j++;
                }

            }
          
           



         

  
    //increment layer by 1 
         
          

            $layer_top=$layer_base+1;
          

            if (count($component_prefix)==1){

                $sql="INSERT INTO assembly (QRcode, part,layer) VALUES ('$QR[$QR_parent]', '$QR[$QR_parent]','$layer_top');";
              
    
                $query=$conn->query($sql);

            }
            else{

                foreach ($QR as $k=> $value){
                    if ($k!=$QR_parent){
                    $sql="INSERT INTO assembly (QRcode, part,layer) VALUES ('$QR[$QR_parent]', '$value','$layer_top');";
              
                     
                    $query=$conn->query($sql);
                  
                } 
                
         
               
    
            }


            }

           

       

       

     
 



   


echo "ok,".$WO; 







} else 
if ((strpos('0'.$station, 'PAK')==true) && ($BOM=='carton')){

   
    // both QR1 and QR2 are same Qrcomp then clear the counter and update QR1 with QR2
    if (strpos('0'.$QR1, $QRcomp)==1) {
            if (strpos('0'.$QR2, $QRcomp)==1){
                echo "ok,"."0";
                return;
            }


        $sql="INSERT INTO carton ( carton) VALUES ( '$QR1')";
        $query=$conn->query($sql);

        $sql="SELECT carton from UDI where QRcode='$QR2'" ;
        $query=$conn->query($sql);


        // always use the updated carton box number attached to the UDI code 
        if  (mysqli_num_rows($query)==0) {

            // check if UDI exist in QRcode table
            $sql="SELECT QRcode from QRcode where QRCode='$QR2'";
            $query=$conn->query($sql);
            if  (mysqli_num_rows($query)==0){
                die ("UDI not found in QRcode");



            }else 
            {

                $sql="INSERT INTO UDI (QRcode, carton) VALUES ('$QR2', '$QR1')";
                $query=$conn->query($sql);

            }

            

        } else {
            $sql="UPDATE UDI SET carton = '$QR1' WHERE (QRcode = '$QR2')";
            $query=$conn->query($sql);


        }



    
       
   //check if the QRcode is UDI, if yes, store it in UDI table
            $UDI=$QR2;
            decode_UDI($UDI);
           
           

                    
                
            

        }
       

        echo "ok,".$BOM;
        return;

    }
  
    else if ((strpos('0'.$station, 'PAL')==true) && ($BOM=='pallet')){

        
        if (strpos('0'.$QR1, $QRcomp)==1) {
            if (strpos('0'.$QR2, $QRcomp)==1){
                echo "ok,"."0";
                return;
            }

            $sql="UPDATE carton SET pallet = '$QR1' WHERE (Carton = '$QR2')";
            $query=$conn->query($sql);
    
          



            }


            echo "ok,".$BOM;
            return;

    
    }
    
    
    else{

        echo "incorrect format";
        return;

    }



    




















        ?>