<?php

require_once 'MyTools.php';


try {


    $InputData = json_decode(file_get_contents('php://input'),true);

    if(!is_numeric($InputData['Season']))
       throw new BadSeason('Bad input : '.$InputData['Season']);


    $Season = intval($InputData['Season']);

    $DomCode = GetTeamCodeFromName($InputData['TeamDom']);
    $VisCode = GetTeamCodeFromName($InputData['TeamVis']);

    $response['Dom'] = json_encode(GetTeamInfo($DomCode,$Season));
    $response['Vis'] = json_encode(GetTeamInfo($VisCode,$Season));
    $response['Success'] = True; 

}
// catch( BadInput | FileNotFound $e){
catch( Exception $e){

    $response['Success'] = False; 
    $response['ErrorMessage'] = $e->getMessage();

}


echo json_encode($response);




?>