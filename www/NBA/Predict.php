<?php


require_once 'MyTools.php';



function GetPrediction($Dom,$Vis){


    $p = shell_exec( 'whoami ');
    print_r($p);
    $p = shell_exec( ' env ');
    print_r($p);
    $p = shell_exec( ' conda env list ');
    print_r($p);

}


$InputData = json_decode(file_get_contents('php://input'),true);

$response = [];
$response['Success'] = False;
$Teams = json_decode(file_get_contents('./Data.json'),true)['TEAMCodes_Names'];

$response['PlayerList'] = $InputData['PlayerList'] ?? 'Unknown';


if(isset($InputData['Dom']) && isset($InputData['Vis']) ){
    $Dom =  htmlspecialchars($InputData['Dom']);
    $Vis =  htmlspecialchars($InputData['Vis']);

    if(IsValidTeamName($Dom,$Teams) && IsValidTeamName($Vis,$Teams)){

        $prediction = GetPrediction($Dom,$Vis);
        $response['Dom']  = $prediction['Dom'];
        $response['Vis']  = $prediction['Vis'];
        $response['Success'] = True;
        $p = shell_exec( 'conda run -n NBAPrediction python MakePrediction.py 2021-07-20 Phoenix\ Suns Milwaukee\ Bucks 456');
        $response['Prediction'] = file_get_contents('/home/ubuntu/Projects/ParisSportifIA/Data/Prediction/Ouputs/Prediction_456.json');

    }

}



echo json_encode($response);




?>