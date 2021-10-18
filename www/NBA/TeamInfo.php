<?php

require_once 'MyTools.php';


try {


    $InputData = json_decode(file_get_contents('php://input'),true);

    if(!is_numeric($InputData['Season']))
       throw new BadSeason('Bad input : '.$InputData['Season']);


    $Season = intval($InputData['Season']);

    $DomCode = GetTeamCodeFromName($InputData['TeamDom']);
    $VisCode = GetTeamCodeFromName($InputData['TeamVis']);


    $DomFile = file_get_contents(GetTeamFilePath($DomCode,$Season));
    $VisFile = file_get_contents(GetTeamFilePath($VisCode,$Season));

    $ArrayDom = array_map("str_getcsv", explode("\n", $DomFile));
    $ArrayVis = array_map("str_getcsv", explode("\n", $DomFile));

    $response['Success'] = True; 
    $response['Dom'].pop(0);
    $response['Vis'].pop(0);
    $response['Dom'] = json_encode($ArrayDom);
    $response['Vis'] = json_encode($ArrayVis); 

}
// catch( BadInput | FileNotFound $e){
catch( Exception $e){

    $response['Success'] = False; 
    $response['ErrorMessage'] = $e->getMessage();

}



echo json_encode($response);




?>