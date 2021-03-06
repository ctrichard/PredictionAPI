<?php


require_once 'MyTools.php';




$ModelName = $_GET['ModelName'] ?? "TestForAPI" ;


try{

    // if(!IsValidModelName($ModelName))
        // throw new Exception('Bad model name : '.$ModelName);

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
        <div class="Title">
            Performances of Model <?php echo $ModelName ?>
        </div>
        <div class="SubTitle" id="accuracy">
        </div>
        <div class="SubTitle">
            Probability of Team wins versus Estimated Win Probability
        </div>
        <div class="BigGraphContainer">
            <div class="GraphContainer" id="GraphProbRealVsEstimated"></div>
        </div>
        <div class="SubTitle">
            Point Diff (mean/std) versus Estimated Win Probability
        </div>
        <div class="BigGraphContainer">

            <div class="GraphContainer" id="GraphDiffPTSVsEstimatedProba"></div>
        </div>

        <div class="SubTitle">
            Classement Diff (mean/std) versus Estimated Win Probability
        </div>
        <div class="BigGraphContainer">
            <div class="GraphContainer" id="GraphDiffClassement_VsEstimatedProba"></div>
        </div>




    <script type="text/javascript" src="http://d3js.org/d3.v5.js"></script>
    <script type="text/javascript" src="../Graphs.js"></script>
    <!-- <script type="text/javascript" src="./app.js"></script> -->
    <script>

        var Data = <?php echo json_encode($ModelResults['DetailedResults'])?>

        let el = d3.select('#accuracy').node()
        el.innerHTML = 'Dom Accuracy : '
        let acc = ComputeAccuracyAboveX(Data,Side='Dom',Proba=0.5)  //  [totalaccuracy , ngood , n ]
  
        // let goodanswer = Data['ProbaGoodAnswerVsEstimatedProba']['CountDomGoodAnswer'].slice((Data['ProbaGoodAnswerVsEstimatedProba']['CountDomGoodAnswer'].length-1)/2).reduce((partial_sum, a) => partial_sum + a, 0); //sum
        // let totalaccuracy = goodanswer /  Data['ProbaGoodAnswerVsEstimatedProba']['CountDomTot'].slice((Data['ProbaGoodAnswerVsEstimatedProba']['CountDomTot'].length-1)/2).reduce((partial_sum, a) => partial_sum + a, 0);
        el.innerHTML += acc[0].toFixed(2) + '('+acc[1]+'/'+acc[2]+')'
        el.innerHTML += '<br>' 
        el.innerHTML += 'Vis Accuracy : '
        // goodanswer = Data['ProbaGoodAnswerVsEstimatedProba']['CountVisGoodAnswer'].slice((Data['ProbaGoodAnswerVsEstimatedProba']['CountVisGoodAnswer'].length-1)/2).reduce((partial_sum, a) => partial_sum + a, 0);
        // totalaccuracy = goodanswer /  Data['ProbaGoodAnswerVsEstimatedProba']['CountVisTot'].slice((Data['ProbaGoodAnswerVsEstimatedProba']['CountVisTot'].length-1)/2).reduce((partial_sum, a) => partial_sum + a, 0);

        acc = ComputeAccuracyAboveX(Data,Side='Vis',Proba=0.5)
        el.innerHTML += acc[0].toFixed(2) + '('+acc[1]+'/'+acc[2]+')'
        el.innerHTML += '<br>' 


        el.innerHTML += '>70% : '
        el.innerHTML += '<br>' 
        el.innerHTML += 'Dom Accuracy : '
        acc = ComputeAccuracyAboveX(Data,Side='Dom',Proba=0.7)
        el.innerHTML += acc[0].toFixed(2) + '('+acc[1]+'/'+acc[2]+')'
        el.innerHTML += '<br>' 
        el.innerHTML += 'Vis Accuracy : '
        acc = ComputeAccuracyAboveX(Data,Side='Vis',Proba=0.7)
        el.innerHTML += acc[0].toFixed(2) + '('+acc[1]+'/'+acc[2]+')'
        el.innerHTML += '<br>' 
        el.innerHTML += '>80% : '
        el.innerHTML += '<br>' 
        el.innerHTML += 'Dom Accuracy : '
        acc = ComputeAccuracyAboveX(Data,Side='Dom',Proba=0.8)
        el.innerHTML += acc[0].toFixed(2) + '('+acc[1]+'/'+acc[2]+')'
        el.innerHTML += '<br>' 
        el.innerHTML += 'Vis Accuracy : '
        acc = ComputeAccuracyAboveX(Data,Side='Vis',Proba=0.8)
        el.innerHTML += acc[0].toFixed(2) + '('+acc[1]+'/'+acc[2]+')'
        el.innerHTML += '<br>' 

        let Identity = []
        Identity[0]= 0 //[0,1]
        Identity[1]= 1 //[1,1]
        let horizontal05 = []
        horizontal05[0]= 0.5 //[0,1]
        horizontal05[1]= 0.5 //[1,1]
        let horizontal075 = []
        horizontal075[0]= 0.75 //[0,1]
        horizontal075[1]= 0.75 //[1,1]
        //ProbaGoodAnswerVsEstimatedProba
        //====================================
        let D = []
        let V = []
        Data['ProbaGoodAnswerVsEstimatedProba']['Bins'].forEach( (d,i)=>{
            if(Data['ProbaGoodAnswerVsEstimatedProba']['CountDomProba'][i] == null)
                return 

            D.push([parseFloat(d)+0.025 , Data['ProbaGoodAnswerVsEstimatedProba']['CountDomProba'][i] , Math.sqrt(Data['ProbaGoodAnswerVsEstimatedProba']['CountDomGoodAnswer'][i])/Data['ProbaGoodAnswerVsEstimatedProba']['CountDomTot'][i] ])  // +0.05 to put at center of bin  ; last element = poissonian uncertainty 
            V.push([parseFloat(d)+0.025 , Data['ProbaGoodAnswerVsEstimatedProba']['CountVisProba'][i] , Math.sqrt(Data['ProbaGoodAnswerVsEstimatedProba']['CountVisGoodAnswer'][i])/Data['ProbaGoodAnswerVsEstimatedProba']['CountVisTot'][i] ])  // +0.05 to put at center of bin  ; last element = poissonian uncertainty 
        }) 


        let G = DrawGraph.CreateStaticFunctionGraph('GraphProbRealVsEstimated', "GraphProbRealVsEstimated")

        G.DataKeysAreX(false);

        let I = G.DrawDataSet(Identity,name='Identity',type="Line",params={'color': 'lightgrey', 'strokewidth':2})
        G.DrawDataSet(horizontal05,name='horizontal05',type="Line",params={'color': 'lightgrey', 'strokewidth':1.5})
        G.DrawDataSet(horizontal075,name='horizontal075',type="Line",params={'color': 'lightgrey', 'strokewidth':1.5})

        let ld = G.DrawDataSet(D,name='DataDom',type="Points",params={'color': 'blue', 'radius':5,'DrawErrors':true, 'strokewidth':2})
        let lv = G.DrawDataSet(V,name='DataVis',type="Points",params={'color': 'red', 'radius':5,'DrawErrors':true, 'strokewidth':2})




        //ProbaGoodAnswerVsEstimatedProba
        //====================================
        D = []
        V = []
        Identity[0]= -20 //[0,1]
        Identity[1]= 20 //[1,1]
        Data['DiffPTSVsEstimatedProba']['Bins'].forEach( (d,i)=>{
            if(Data['DiffPTSVsEstimatedProba']['Mean_DomProba'][i] == null)
                return 

                D.push([parseFloat(d)+0.05 , Data['DiffPTSVsEstimatedProba']['Mean_DomProba'][i] , Data['DiffPTSVsEstimatedProba']['Std_DomProba'][i] ])  // +0.05 to put at center of bin  
                V.push([parseFloat(d)+0.05 , Data['DiffPTSVsEstimatedProba']['Mean_VisProba'][i] , Data['DiffPTSVsEstimatedProba']['Std_VisProba'][i] ])  // +0.05 to put at center of bin  
        }) 


        let G2 = DrawGraph.CreateStaticFunctionGraph('GraphDiffPTSVsEstimatedProba', "GraphDiffPTSVsEstimatedProba")
        G2.FixAxis('Y',-20,20);

        G2.DataKeysAreX(false);

        I = G2.DrawDataSet(Identity,name='Identity',type="Line",params={'color': 'rgba(255,0,0,0)', 'strokewidth':2})

        ld = G2.DrawDataSet(D,name='DataDom',type="Points",params={'color': 'blue', 'radius':5,'DrawErrors':true, 'strokewidth':2})
        lv = G2.DrawDataSet(V,name='DataVis',type="Points",params={'color': 'red', 'radius':5,'DrawErrors':true, 'strokewidth':2})



        //DiffClassement_VsEstimatedProba
        //====================================
        D = []
        V = []
        Identity[0]= -20 //[0,1]
        Identity[1]= 20 //[1,1]
        Data['DiffClassement_VsEstimatedProba']['Bins'].forEach( (d,i)=>{
            if(Data['DiffClassement_VsEstimatedProba']['Mean_DomProba'][i] == null)
                return 

                D.push([parseFloat(d)+0.05 , Data['DiffClassement_VsEstimatedProba']['Mean_DomProba'][i] , Data['DiffClassement_VsEstimatedProba']['Std_DomProba'][i] ])  // +0.05 to put at center of bin  
                V.push([parseFloat(d)+0.05 , Data['DiffClassement_VsEstimatedProba']['Mean_VisProba'][i] , Data['DiffClassement_VsEstimatedProba']['Std_VisProba'][i] ])  // +0.05 to put at center of bin  
        }) 


        let G3 = DrawGraph.CreateStaticFunctionGraph('GraphDiffClassement_VsEstimatedProba', "GraphDiffClassement_VsEstimatedProba")
        G3.FixAxis('Y',-20,20);

        G3.DataKeysAreX(false);

        I = G3.DrawDataSet(Identity,name='Identity',type="Line",params={'color': 'rgba(255,0,0,0)', 'strokewidth':2})

        ld = G3.DrawDataSet(D,name='DataDom',type="Points",params={'color': 'blue', 'radius':5,'DrawErrors':true, 'strokewidth':2})
        lv = G3.DrawDataSet(V,name='DataVis',type="Points",params={'color': 'red', 'radius':5,'DrawErrors':true, 'strokewidth':2})


        function ComputeAccuracyAboveX(Data,Side='Dom',Proba=0.5){

            let acc = 0
            let ganswer = 0
            let tanswer =0
            Data['ProbaGoodAnswerVsEstimatedProba']['Bins'].forEach((d,i)=>{

                if(d<Proba){
                    return
                }

                if(d>=1){
                    return
                }

                if(Data['ProbaGoodAnswerVsEstimatedProba']['Count'+Side+'Tot'][i]>0){
                    tanswer += Data['ProbaGoodAnswerVsEstimatedProba']['Count'+Side+'Tot'][i]
                    ganswer += Data['ProbaGoodAnswerVsEstimatedProba']['Count'+Side+'GoodAnswer'][i]
                }

            })

            acc  =  ganswer /tanswer
            
            console.log("Acc=",acc,Side,tanswer,ganswer)
            return [acc, ganswer, tanswer]



        }


        


    </script>
    </body>
    </html>