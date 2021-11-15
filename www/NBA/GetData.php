<?php



$ModelListNames = ['TrainWo_','TrainUpTo','TrainLast3Years_','TrainLast5Years_'];
$ModelList = [];

function DefineModelList(){

    foreach($GLOBALS['ModelListNames'] as $ModelBaseName){

        foreach(range(2004,2022) as $Year){
         
            array_push($GLOBALS['ModelList'],$ModelBaseName.$Year);

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

    
    foreach ($GLOBALS['ModelList'] as $ModelName){

        $PredictionList[$ModelName] = array();
        
        $handle = fopen("../../Prediction/Data/PredictionData_'.$ModelName.'.csv", "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            array_push($PredictionList[$ModelName],$data);
        }
        fclose($handle);

        // $PredictionList[$ModelName] = json_encode(file_get_contents('../../Prediction/Data/PredictionData_'.$ModelName.'.csv'));

    } 

    echo json_encode($PredictionList);
    die();
}

?>