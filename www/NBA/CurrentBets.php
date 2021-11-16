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

        // var AllModelAllBetPredictions={}

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

            const AllModelAllBetPredictions = SortModelPredictions(AllModels)

            console.log(AllModels)
            console.log(AllModelAllBetPredictions)

            DrawGraphs(AllModelAllBetPredictions)

            

          }

  
        LoadData();
  


        function DrawBet(BetData){

            d3.select('#BigContainer')

            let div = document.createElement("div");
            div.className  = "BetContainer";
            // div.innerHTML = BetData['Match'];
            document.getElementById("BigContainer").appendChild(div);

            let divrestitle = document.createElement("div");
            divrestitle.className  = "BetTitleContainer";
            divrestitle.innerHTML = WriteBetTitle(BetData)
            divrestitle.innerHTML +='<br>'
            divrestitle.innerHTML +=BetData['BetValue']+' Win<br>'
            
            div.appendChild(divrestitle);

            let divres = document.createElement("div");
            divres.className  = "BetResultsContainer";
            div.appendChild(divres);
            
            // div with logos -----------
            // let logoAcontainer = document.createElement("div");
            // logoAcontainer.className  = "LogoAContainer";
            // logoAcontainer.id  = "LogoAContainer_"+BetData['UUID'];
            // divres.appendChild(logoAcontainer);
            // let logoBcontainer = document.createElement("div");
            // logoBcontainer.className  = "LogoBContainer";
            // logoBcontainer.id  = "LogoBContainer_"+BetData['UUID'];
            // divres.appendChild(logoBcontainer);
            // let imgdom= document.createElement('img');
            // imgdom.id = 'LogoA_'+BetData['UUID'];
            // logoAcontainer.appendChild(imgdom)
            // let imgvis= document.createElement('img');
            // imgvis.id = 'LogoB_'+BetData['UUID'];
            // logoBcontainer.appendChild(imgvis)
            // selectLogo('LogoA_'+BetData['UUID'],BetData['Dom'])
            // selectLogo('LogoB_'+BetData['UUID'],BetData['Vis'])


            let divgraph = document.createElement("div");
            divgraph.className  = "BetGraphContainer";
            divgraph.id = "GraphContainer_"+BetData['UUID'];
            div.appendChild(divgraph);


            let divextrainfo = document.createElement("div");
            // divextrainfo.className  = "BetInfoContainer";

            let button = document.createElement("div");
            let prediction = parseFloat(BetData['Prediction'])
            button.innerHTML = prediction.toFixed(2)*100+'%'
            if(prediction>0.70)
                button.className +="Good ";
            else if(prediction>50)
                button.className +="Warning ";
            else if(prediction<30)
                button.className  += "Bad ";

            button.className  = "BetInfoButton ";
            divextrainfo.appendChild(button);



            button = document.createElement("div");
            button.className  = "BetInfoButton ";
            button.innerHTML = 'Odd : '+BetData['BetOdds']+'</br>'
            divextrainfo.appendChild(button);


            button = document.createElement("div");
            button.className  = "BetInfoButton ";

            let rentability = (parseFloat(BetData['BetOdds'])*parseFloat(BetData['Prediction']) -1 )*100
            button.innerHTML += (rentability.toFixed(0))+'% </br>'

            if(rentability>25)
                button.className +="Good ";
            else if(rentability>0)
                button.className +="Warning ";
            else
                button.className  += "Bad ";
            divextrainfo.appendChild(button);

            
            divres.appendChild(divextrainfo);



            // const svg = d3.select("#GraphContainer_"+BetData['UUID'])
            //               .append("svg")
            //                 .attr("width", "100%") //width + margin.left + margin.right)
            //                 .attr("height","100%") //height + margin.top + margin.bottom)
            //                 .attr("id",'graph_predictions_'+BetData['UUID'])
            //               .append("g")
            //                 // .attr("transform", `translate(${width/2},${height/2+100})`); // Add 100 on Y translation, cause upper bars are longer




        }

        function SortModelPredictions(ModelPredictions){
            let AllModelAllBetPredictions ={}

            Object.entries(ModelPredictions).forEach(entry => {
                const [ModelName, ModelPreds] = entry;
                ModelPreds.forEach(el=>{
                    if(el[0]=='UUID')
                     return   //== premiere ligne du .cvs

                    if(AllModelAllBetPredictions[el[0]]==undefined)
                         AllModelAllBetPredictions[el[0]] ={}

                    AllModelAllBetPredictions[el[0]][ModelName] = {'Prediction' : el[2], 'Date':el[1]} 

                })
            });

            return AllModelAllBetPredictions
        }

        function DrawGraphs(AllModelAllBetPredictions){

            Object.entries(AllModelAllBetPredictions).forEach(entry => {
                const [BetName, ModelPreds] = entry;
                let G = DrawGraph.CreateStaticFunctionGraph("GraphContainer_"+BetName, "Graph_BarPlot")

                G.DataKeysAreX(false);

                let data = []
                console.log('data:')
                Object.entries(ModelPreds).forEach(el => {
                    
                    let d = parseFloat(el['1']['Prediction'])
                    console.log(d)
                    if(d<0){
                        d=0
                    }
                    data.push(d) 
                    
                })

                console.log('drawing '+'data_'+BetName)
                console.log(data)
                // let I = G.DrawDataSet(Identity,name='Identity',type="Line",params={'color': 'lightgrey', 'strokewidth':2})
                G.DrawDataSet(data,name='data_'+BetName,type="Histo",params={'XValueAtCenter': false, 'color': 'lightgrey', 'strokewidth':1.5, 'classname' : 'BarPlot'})

                // let ld = G.DrawDataSet(D,name='DataDom',type="Points",params={'color': 'blue', 'radius':5,'DrawErrors':true, 'strokewidth':2})
                // let lv = G.DrawDataSet(V,name='DataVis',type="Points",params={'color': 'red', 'radius':5,'DrawErrors':true, 'strokewidth':2})



            });


        }

        function WriteBetTitle(BetData){

            html = BetData['Dom']+' - '+BetData['Vis']
            return html
        }

    </script>
    </body>
    </html>