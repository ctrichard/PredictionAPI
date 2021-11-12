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
        <link rel="stylesheet" type="text/css" href="../CurrentBets.css">

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

        <div class="BetContainer" id='BigContainer'>
        </div> 




    <script type="text/javascript" src="http://d3js.org/d3.v5.js"></script>
    <script type="text/javascript" src="../Graphs.js"></script>
    <!-- <script type="text/javascript" src="./app.js"></script> -->
    <script>

        let Date = [<?php  echo '"'.$TodayDate.'","'.$TomorrowDate.'"' ?> ]
    
        async function LoadData(){

            d3.csv('GetData.php?Data=PredictionData', function(data){

                console.log(data)
                DrawBet(data)
            });

          }
  
        LoadData();
  

        function DrawBet(BetData){

            d3.select('#BigContainer')

            var div = document.createElement("div");
            div.style.width = "100%";
            div.style.height = "100px";
            div.style.background = "red";
            div.style.color = "white";
            div.innerHTML = BetData['Match'];

            document.getElementById("BigContainer").appendChild(div);


        }


    </script>
    </body>
    </html>