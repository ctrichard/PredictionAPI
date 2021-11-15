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

        <div class="BigContainer" id='BigContainer'>
        </div> 




    <!-- <script type="text/javascript" src="http://d3js.org/d3.v5.js"></script> -->
    <script src="https://d3js.org/d3.v6.js"></script>

    <script type="text/javascript" src="../Graphs.js"></script>
    <script type="text/javascript" src="./app.js"></script>
    <script>

        let Date = [<?php  echo '"'.$TodayDate.'","'.$TomorrowDate.'"' ?> ]
    
        async function LoadData(){

            d3.csv('GetData.php?Data=PredictionData', function(data){

                console.log(data)
                DrawBet(data)
            });


            const AllModels = await fetch('GetData.php?Data=PredictionDataWithAllModels', {
            headers: {
                'Accept': 'application/json'
            }
            })
            .then(response => response.json());

            

          }

  
        LoadData();
  

        function DrawBet(BetData){

            d3.select('#BigContainer')

            let div = document.createElement("div");
            div.className  = "BetContainer";
            // div.innerHTML = BetData['Match'];
            document.getElementById("BigContainer").appendChild(div);

            let divres = document.createElement("div");
            divres.className  = "BetResultsContainer";
            div.appendChild(divres);

            let divgraph = document.createElement("div");
            divgraph.className  = "BetGraphContainer";
            divgraph.id = "GraphContainer_"+BetData['UUID'];
            div.appendChild(divgraph);

     
            
            let logoAcontainer = document.createElement("div");
            logoAcontainer.className  = "LogoAContainer";
            logoAcontainer.id  = "LogoAContainer_"+BetData['UUID'];
            divres.appendChild(logoAcontainer);
            let logoBcontainer = document.createElement("div");
            logoBcontainer.className  = "LogoBContainer";
            logoBcontainer.id  = "LogoBContainer_"+BetData['UUID'];
            divres.appendChild(logoBcontainer);

            let imgdom= document.createElement('img');
            imgdom.id = 'LogoA_'+BetData['UUID'];
            logoAcontainer.appendChild(imgdom)
            let imgvis= document.createElement('img');
            imgvis.id = 'LogoB_'+BetData['UUID'];
            logoBcontainer.appendChild(imgvis)

            selectLogo('LogoA_'+BetData['UUID'],BetData['Dom'])
            selectLogo('LogoB_'+BetData['UUID'],BetData['Vis'])



            let divextrainfo = document.createElement("div");
            divextrainfo.className  = "BetInfoContainer";
            divextrainfo.innerHTML = BetData['BetValue']+' Win : '+(parseFloat(BetData['Prediction']).toFixed(2))+'</br>'
            divextrainfo.innerHTML += 'Odd : '+BetData['BetOdds']+'</br>'

            let rentability = (parseFloat(BetData['BetOdds'])*parseFloat(BetData['Prediction']) -1 )*100
            divextrainfo.innerHTML += 'R : '+(rentability.toFixed(0))+'% </br>'
            
            div.appendChild(divextrainfo);



            const svg = d3.select("#GraphContainer_"+BetData['UUID']")
                          .append("svg")
                            .attr("width", "100%") //width + margin.left + margin.right)
                            .attr("height","100%") //height + margin.top + margin.bottom)
                          .append("g")
                            // .attr("transform", `translate(${width/2},${height/2+100})`); // Add 100 on Y translation, cause upper bars are longer




        }


    </script>
    </body>
    </html>