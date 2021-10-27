<?php


require_once 'MyTools.php';




$ModelName = $_GET['ModelName'] ?? "TestForAPI" ;


try{

    if(!IsValidModelName($ModelName))
        throw new Exception('Bad model name : '.$ModelName);

    $ModelResults = GetModelResults($ModelName);
    // print_r($ModelResults);

}
catch(Exception $a){
    echo 'ERROR'.PHP_EOL;
    print_r($a->getMessage());
}




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


        <div class="   basicDiv  base-container ">
             <div class="graph-pop" id="graph_pop3"></div>
            </div>


    <script type="text/javascript" src="http://d3js.org/d3.v5.js" defer></script>
    <script type="text/javascript" src="../Graphs.js"></script>
    <!-- <script type="text/javascript" src="./app.js"></script> -->
    <script>

        var Data = <?php echo json_encode($ModelResults['DetailedResults'])?>


        let D = []
        Data['ProbaGoodAnswerVsEstimatedProba']['Bins'].forEach( (d,i)=>{
            D[d] = Data['ProbaGoodAnswerVsEstimatedProba']['CountDomProba'][i]
        })

        console.log(D)
        // await LoadFunctionStaticFunctionResults("Population","OverHousingEffectOnBirth","","0","5","0.1");

        // DrawGraph.DrawStaticFunctionGraph('graph_pop3',"OverHousingEffectOnBirth",OverHousingEffectOnBirth,parseInt(d3.select('#graph_pop3').style('width'))/ngraph);




    </script>
    </body>
    </html>