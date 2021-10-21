<?php

require_once 'MyTools.php';
// require_once 'Predict.php';


try {



    $InputData = json_decode(file_get_contents('php://input'),true);

    $response = [];
    // $response['Success'] = False;

    


    $response['PlayerList'] = $InputData['PlayerList'] ?? null;
    $response['Dom'] =  $InputData['Dom'] ?? null;
    $response['Vis'] =  $InputData['Vis'] ?? null;
    $response['Season'] =  $InputData['Season'] ?? null;
    $response['Date'] =  $InputData['Date'] ?? null;


    $Check = new CheckPlayerStat();
    $Check->SetDomTeam($response['Dom']);
    $Check->SetVisTeam($response['Vis']);
    $Check->SetDate($response['Date']);
    $Check->SetPlayerList($response['PlayerList']);
    $Check->SetPathOutputs($OutputsTempFileLocation);
    $Check->SetSeason($response['Season']);

    $Check->Prepare();
    $response['Results'] = [];


    $a =$Check->Run('Dom');
    print($a);
    print_r(explode('++--++',$Check->Run('Dom')));
    die();

    $response['Results']['Dom'] = explode('\n',$Check->Run('Dom'));
    foreach($response['Results']['Dom'] as &$arr)
        $arr = json_decode($arr,true);

    $response['Results']['Vis'] = explode('\n',$Check->Run('Vis'));
    foreach($response['Results']['Vis'] as &$arr)
        $arr = json_decode($arr,true);
    $response['Results']['Success'] = True; 


}
// catch( BadInput | FileNotFound $e){
catch( Exception $e){

    $response['Success'] = False; 
    $response['ErrorMessage'] = $e->getMessage();

}

echo json_encode($response['Results']);



