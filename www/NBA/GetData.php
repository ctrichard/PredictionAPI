<?php



$ModelListNames = ['TrainWo_','TrainUpTo','TrainLast3Years_','TrainLast5Years_'];
$ModelList = [];

function DefineModelist(){

    foreach($ModelListNames as $ModelBaseName){

        foreach(range(2004,2022) as $Year){
         
            $GlOBALS['ModelList'].append($ModelBaseName.$Year);
            
        }
    }

}


DefineModelist();


if($_GET['Data']=='PredictionData'){

    echo file_get_contents('../../Prediction/Data/PredictionData.csv');
    die();
}

if($_GET['Data']=='PredictionDataWithAllModels'){

    $PredictionList = [];

    echo $GLOBALS['ModelList'];
    
    foreach ($GlOBALS['ModelList'] as $ModelName){

        $PredictionList[$ModelName] = file_get_contents('../../Prediction/Data/PredictionData_'.$ModelName.'.csv');

    } 

    echo json_encode($PredictionList);
    die();
}

?>