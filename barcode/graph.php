<script src="./js/jquery-3.6.0.js"></script>
<script src="./js/d3.v5.min.js"></script>
 <link href="./css/c3.min.css" rel="stylesheet">
<script src="./js/c3.min.js"></script>
<script src="./js/qrcode.js"></script>



 

<?php


include_once "conn.php";

$current_date=date("Y\-m\-d");

//$current_date="2022-03-22";

$sql="SELECT  WO FROM productionDetails where  (endDate is null) or (startDate=CURDATE()) or (endDate=CURDATE())";

$query=$conn->query($sql);
  if  ($query->num_rows!=0) 
    while ($rows=$query->fetch_assoc()){
        $graph_WO[]=$rows['WO']; 
       
        }

       

?>




<script>  

   
   var graph_WO= <?php echo json_encode($graph_WO); ?>;
   if (graph_WO!=null) graph_WO.unshift('x')
   
  var result=[]

   var rdata= []

   
   var gdata=[]

   for (i=0;i<graph_WO.length;i++){
    rdata=[graph_WO[i]]
    result[i]=0
    gdata.push(rdata);
  
   }
  

   console.log(gdata);

 
   // each  minites on data    therefore 1000 *60 =60000; 14 hrs
   for(j=0;j<gdata.length;j++)
        for(i=1;i<(60*15);i++) {
       
      if (j==0) gdata[j].push(60000*i+8*3600000); else gdata[j].push(null);
      };
 

    var chart = c3.generate({
        
        bindto: '#chart',
        padding: {
            left: 50,
            right: 50
        },
        point: {
            show: true
        },


        data: {
            type: "line",
            x: 'x',
            //        xFormat: '%Y%m%d', // 'xFormat' can be used as custom format of 'x'
            columns: gdata
                
           
            ,
            connectNull: true
        },
        axis: {
            x: {
            //    fit: true,
                localtime: false,
                type: 'timeseries',
                tick: {
                    fit:true,
                    count:16,
                    format: "%H:%M",
                    values: [32400000,43200000,54000000,64800000,75600000]
                },
                label: {
                    text: 'Time',
                    position: 'outer-center',
                }

            },
            y: {
                label: {
                    text: 'Daily Output',
                    position: 'outer-middle',
                }
            }

        }
    });
 





 function refresh_graph(){
  


  
   $.ajax({
      url: './backend/production/graph_refresh.php',
   
      success: function(data) {
          result=data;
      }     
  });


 // result=['70','80','90']
console.log(result);

  var t=new Date()
        var h=t.getHours()
        var m=t.getMinutes()
        var currentTime=m*60000+h*3600000 

        console.log(result[0]);
for ( var key in gdata[0]){
               if (gdata[0][key]==currentTime) 
               {

                   for (j=1;j<gdata.length;j++){
                    
                    gdata[j][key]=result[j-1];

                   } 
                
              //  console.log(new Date(gdata[0][key]).toISOString().slice(11, -1)); 
         


           
        }
    }
        chart.flow({
            columns: gdata,
        });




 }

    setInterval(refresh_graph, 10000);






</script>

