<?php



$ModelListNames = ['TrainWo_','TrainUpTo','TrainLast3Years_','TrainLast5Years_'];
$ModelList = [];

function DefineModelList(){

    echo 'Define !';
    print_r($ModelListNames);
    print_r($GlOBALS['ModelListNames']);
    foreach($GlOBALS['ModelListNames'] as $ModelBaseName){

        echo $ModelBaseName;
        foreach(range(2004,2022) as $Year){
         
            array_push($GlOBALS['ModelList'],$ModelBaseName.$Year);
            echo $ModelBaseName.$Year;

        }
    }

}


DefineModelList();


if($_GET['Data']=='PredictionData'){

    echo file_get_contents('../../Prediction/Data/PredictionData.csv');
    die();
}

if($_GET['Data']=='PredictionDataWithAllModels'){

    $PredictionList = [];

    print_r($GLOBALS['ModelList']);
    
    foreach ($GlOBALS['ModelList'] as $ModelName){

        $PredictionList[$ModelName] = file_get_contents('../../Prediction/Data/PredictionData_'.$ModelName.'.csv');

    } 

    echo json_encode($PredictionList);
    die();
}

?>