<?php





function GetPrediction($Dom,$Vis){


    $p = shell_exec( ' env ');

    print_r($p);

}


function IsValidTeamName($Name,$Teams){

    return in_array($Name,array_values($Teams));

}

$request_body = file_get_contents('php://input');
print_r($request_body);
echo 'ok';
print_r($_POST);
print_r($_GET);
exit();

$response = [];
$response['Success'] = False;
$Teams = json_decode(file_get_contents('./Data.json'),true);


if(isset($_POST['Dom']) && isset($_POST['Vis']) ){
    $Dom =  htmlspecialchars($_POST['Dom']);
    $Vis =  htmlspecialchars($_POST['Vis']);

    if(IsValidTeamName($Dom,$Teams) && IsValidTeamName($Vis,$Teams)){

        $prediction = GetPrediction($Dom,$Vis);
        $response['Dom']  = $prediction['Dom'];
        $response['Vis']  = $prediction['Vis'];
        $response['Success'] = True;


    }

}


echo json_encode($response);




?>