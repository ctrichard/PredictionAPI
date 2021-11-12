<?php


require_once 'MyTools.php';

$DateStr =  date("j F Y");         
$TodayDate =  date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
$TomorrowDate =  date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));

?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />

        <title>Prediction</title>
        <link rel="stylesheet" type="text/css" href="../root.css">
        <link rel="stylesheet" type="text/css" href="../perf.css">

    </head>
        
    <body>


        <!-- <div class="GraphContainer"> -->

            <!-- <graph-std data="ProbaGoodAnswerVsEstimatedProba-CountDomProba"> -->
            <!-- </graph-std> -->
        <!-- </div> -->
        <div class="Title">
            Paris en cours : <?php echo $DateStr ?>
        </div>
        <div class="SubTitle" id="accuracy">
        </div>




    <script type="text/javascript" src="http://d3js.org/d3.v5.js"></script>
    <script type="text/javascript" src="../Graphs.js"></script>
    <!-- <script type="text/javascript" src="./app.js"></script> -->
    <script>

        let Date = [<?php  echo '"'.$TodayDate.'","'.$TomorrowDate.'"' ?> ]
    
        async function LoadData(){
              const PredictionData = await fetch('../Prediction/Data/PredictionData.csv', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.text())

                // .then(response => response.json());
            
            //   CreateTeamSelectionOptions('A',NBAData)
            //   CreateTeamSelectionOptions('B',NBAData)
            

            d3.csv(PredictionData, function(data){

                console.log(Data)
                //code dealing with data here
                });

          }
  
        LoadData();
  



    </script>
    </body>
    </html>