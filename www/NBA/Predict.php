<?php





function GetPrediction($Dom,$Vis){


    $p = shell_exec( ' env ');

    print_r($p);

}


function IsValidTeamName($Name,$Teams){

    return in_array($Name,array_values($Teams));

}


$response = [];
$response['Success'] = False;
$Teams = json_decode(file_get_contents('./Data.json'),true);


if(isset($_GET['Dom']) && isset($_GET['Vis']) ){
    $Dom =  htmlspecialchars($_GET['Dom']);
    $Vis =  htmlspecialchars($_GET['Vis']);

    if(IsValidTeamName($Dom,$Teams) && IsValidTeamName($Vis,$Teams)){

        $prediction = GetPrediction($Dom,$Vis);
        $response['Dom']  = $prediction['Dom'];
        $response['Vis']  = $prediction['Vis'];
        $response['Success'] = True;


    }

}


echo json_encode($response);




?>