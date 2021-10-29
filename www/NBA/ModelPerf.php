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

        <div class="BigGraphContainer">
            <div class="GraphContainer" id="GraphProbRealVsEstimated"></div>
            <div class="GraphContainer" id="GraphDiffPTSVsEstimatedProba"></div>
        </div>
            


    <script type="text/javascript" src="http://d3js.org/d3.v5.js"></script>
    <script type="text/javascript" src="../Graphs.js"></script>
    <!-- <script type="text/javascript" src="./app.js"></script> -->
    <script>

        var Data = <?php echo json_encode($ModelResults['DetailedResults'])?>

        let Identity = []
        Identity[0]= 0 //[0,1]
        Identity[1]= 1 //[1,1]


        //ProbaGoodAnswerVsEstimatedProba
        //====================================
        let D = []
        let V = []
        Data['ProbaGoodAnswerVsEstimatedProba']['Bins'].forEach( (d,i)=>{
            if(Data['ProbaGoodAnswerVsEstimatedProba']['CountDomProba'][i] == null)
                return 

            D.push([parseFloat(d)+0.05 , Data['ProbaGoodAnswerVsEstimatedProba']['CountDomProba'][i] , Math.sqrt(Data['ProbaGoodAnswerVsEstimatedProba']['CountDomGoodAnswer'][i])/Data['ProbaGoodAnswerVsEstimatedProba']['CountDomTot'][i] ])  // +0.05 to put at center of bin  ; last element = poissonian uncertainty 
            V.push([parseFloat(d)+0.05 , Data['ProbaGoodAnswerVsEstimatedProba']['CountVisProba'][i] , Math.sqrt(Data['ProbaGoodAnswerVsEstimatedProba']['CountVisGoodAnswer'][i])/Data['ProbaGoodAnswerVsEstimatedProba']['CountVisTot'][i] ])  // +0.05 to put at center of bin  ; last element = poissonian uncertainty 
        }) 


        let G = DrawGraph.CreateStaticFunctionGraph('GraphProbRealVsEstimated', "GraphProbRealVsEstimated")

        G.DataKeysAreX(false);

        let I = G.DrawDataSet(Identity,name='Identity',type="Line",params={'color': 'lightgrey', 'strokewidth':2})

        let ld = G.DrawDataSet(D,name='DataDom',type="Points",params={'color': 'blue', 'radius':5,'DrawErrors':true, 'strokewidth':2})
        let lv = G.DrawDataSet(V,name='DataVis',type="Points",params={'color': 'red', 'radius':5,'DrawErrors':true, 'strokewidth':2})




        //ProbaGoodAnswerVsEstimatedProba
        //====================================
        D = []
        V = []
        Data['DiffPTSVsEstimatedProba']['Bins'].forEach( (d,i)=>{
            if(Data['DiffPTSVsEstimatedProba']['CountDomProba'][i] == null)
                return 

                D.push([parseFloat(d)+0.05 , Data['DiffPTSVsEstimatedProba']['Mean_DomProba'][i] , Data['DiffPTSVsEstimatedProba']['Std_DomProba'][i] ])  // +0.05 to put at center of bin  
                V.push([parseFloat(d)+0.05 , Data['DiffPTSVsEstimatedProba']['Mean_VisProba'][i] , Data['DiffPTSVsEstimatedProba']['Std_VisProba'][i] ])  // +0.05 to put at center of bin  
        }) 


        let G2 = DrawGraph.CreateStaticFunctionGraph('GraphDiffPTSVsEstimatedProba', "GraphDiffPTSVsEstimatedProba")

        G2.DataKeysAreX(false);

        I = G2.DrawDataSet(Identity,name='Identity',type="Line",params={'color': 'rgba(255,0,0,0)', 'strokewidth':2})

        ld = G2.DrawDataSet(D,name='DataDom',type="Points",params={'color': 'blue', 'radius':5,'DrawErrors':true, 'strokewidth':2})
        lv = G2.DrawDataSet(V,name='DataVis',type="Points",params={'color': 'red', 'radius':5,'DrawErrors':true, 'strokewidth':2})

    </script>
    </body>
    </html>