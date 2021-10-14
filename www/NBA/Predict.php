<?php





function GetPrediction($Dom,$Vis){


    $p = shell_exec( ' env ');

    print_r($p);

}


function IsValidTeamName($Name,$Teams){

    return in_array($Name,array_values($Teams));

}

$InputData = json_decode(file_get_contents('php://input'));

$response = [];
$response['Success'] = False;
$Teams = json_decode(file_get_contents('./Data.json'),true);


if(isset($_POST['Dom']) && isset($_POST['Vis']) ){
    $Dom =  htmlspecialchars($InputData['Dom']);
    $Vis =  htmlspecialchars($InputData['Vis']);

    if(IsValidTeamName($Dom,$Teams) && IsValidTeamName($Vis,$Teams)){

        $prediction = GetPrediction($Dom,$Vis);
        $response['Dom']  = $prediction['Dom'];
        $response['Vis']  = $prediction['Vis'];
        $response['Success'] = True;


    }

}


echo json_encode($response);




?>