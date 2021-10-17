<?php

require_once 'MyTools.php';


try {


    $InputData = json_decode(file_get_contents('php://input'),true);

    if(!is_numeric($InputData['Season']))
       throw new BadSeason('Bad input : '.$InputData['Season']);


    $Season = int($InputData['Season']);

    $DomCode = MyTools::GetTeamCodeFromName($InputData['TeamDom']);
    $VisCode = MyTools::GetTeamCodeFromName($InputData['TeamVis']);


    $DomFile = file_get_contents(MyTools::GetTeamFilePath($DomCode,$Season));
    $VisFile = file_get_contents(MyTools::GetTeamFilePath($VisCode,$Season));

    $ArrayDom = array_map("str_getcsv", explode("\n", $DomFile));
    $ArrayVis = array_map("str_getcsv", explode("\n", $DomFile));

    $response['Success'] = True; 
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