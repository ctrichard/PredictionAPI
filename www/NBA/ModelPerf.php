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


             <div class="GraphContainer" id="GraphProbRealVsEstimated"></div>


    <script type="text/javascript" src="http://d3js.org/d3.v5.js"></script>
    <script type="text/javascript" src="../Graphs.js"></script>
    <!-- <script type="text/javascript" src="./app.js"></script> -->
    <script>

        var Data = <?php echo json_encode($ModelResults['DetailedResults'])?>

        let Identity = {}
        Identity[0]=0
        Identity[1]=1

        let D = []
        Data['ProbaGoodAnswerVsEstimatedProba']['Bins'].forEach( (d,i)=>{
            if(Data['ProbaGoodAnswerVsEstimatedProba']['CountDomProba'][i] == null)
                return 
            D[parseFloat(d)+0.05] = Data['ProbaGoodAnswerVsEstimatedProba']['CountDomProba'][i]
        })

        console.log(D)
        // await LoadFunctionStaticFunctionResults("Population","OverHousingEffectOnBirth","","0","5","0.1");

        G = DrawGraph.DrawStaticFunctionGraph('GraphProbRealVsEstimated',"GraphProbRealVsEstimated",D,parseInt(d3.select('#GraphProbRealVsEstimated').style('width'))/1);

        G.DrawOtherLine(Identity)



    </script>
    </body>
    </html>