<?php


require_once 'MyTools.php';

$PlayTimePerTeam = 48*5;

$MyUUID  =  Generate_UUID(20); 



function GetPrediction($Dom,$Vis){

    $p = shell_exec( 'conda run -n NBAPrediction python MakePrediction.py 2021-07-20 Phoenix\ Suns Milwaukee\ Bucks 456');
    $response['Prediction'] = json_decode(file_get_contents('/home/ubuntu/Projects/ParisSportifIA/Data/Prediction/Ouputs/Prediction_456.json'),true);
    // $p = shell_exec( 'whoami ');
    // print_r($p);
    // $p = shell_exec( ' env ');
    // print_r($p);
    // $p = shell_exec( ' conda env list ');
    // print_r($p);

}


function IsValidPlayerList($Playerlist){
    LogThis($PlayerList);

    try{

        if(!$PlayerList['Name']  || !$PlayerList['MP'] || !$PlayerList['Side'] ){
            return False;
        }
        return True;
    }
    catch(Exception  $e){
        return False;
    }
}


function CreatePlayerListFile(&$PlayerList,$Dom,$Vis,$Season){

    $Data = [];
    $Data["Name"] = [];
    $Data["MP"] = [];
    $Data["Side"] = [];

    foreach($PlayerList as $Side=>$Players){

        $totplaytime = $GLOBALS['PlayTimePerTeam'];

        foreach($Players as $p=>$mp){

            if(intval($mp)==0)
                continue;

            CheckPlayerName($p,$Side=='Dom' ? $Dom : $Vis,$Season);

            if(intval($mp)>48 || intval($mp)<0)
                throw new Exception('Minute played for player '.$p.' is '.$mp);

            if($Side !='Dom' && $Side != 'Vis')
                throw new Exception('Side for player '.$p.' is '.$Side);

            $totplaytime -= intval($mp);

            array_push($Data['Name'],$p);
            array_push($Data['MP'],$mp.':00');
            array_push($Data['Side'],$Side);
            
        }

        if($totplaytime !=0 && $totplaytime!=$GLOBALS['PlayTimePerTeam'])
            throw new Exception('Total play time left for team '.$Side.' is '.$totplaytime.'. It should be ==0  or =='.$GLOBALS['PlayTimePerTeam'].'.');


    }

    $PlayerList = $Data;


    $path = $GLOBALS['InputsTempFileLocation'];
    if(!is_dir($path))
       throw new FileNotFound('Bad location for Player list : '.$path);

    LogThis($PlayerList);
    if(IsValidPlayerList($PlayerList)){
        $fp = fopen($path.'PlayerList_'.$GLOBALS['MyUUID'].'.json', 'w');
        fwrite($fp, json_encode($PlayerList));
        fclose($fp);
        LogThis('Wrote player list at '.$path.'PlayerList_'.$GLOBALS['MyUUID'].'.json');
    }
    LogWarning('Did not print Player List');

}


try{

    $InputData = json_decode(file_get_contents('php://input'),true);

    print($InputData);

    $response = [];
    // $response['Success'] = False;

    $response['PlayerList'] = $InputData['PlayerList'] ?? null;
    $response['Dom'] =  $InputData['Dom'] ?? null;
    $response['Vis'] =  $InputData['Vis'] ?? null;
    $response['Model'] =  $InputData['Model'] ?? null;
    $response['Season'] =  $InputData['Season'] ?? null;
    $response['Date'] =  $InputData['Date'] ?? null;
    $response['UUID'] = $GLOBALS['MyUUID'];


    $Prediction = new RunPrediction();
    $Prediction->SetDomTeam($response['Dom']);
    $Prediction->SetVisTeam($response['Vis']);
    $Prediction->SetDate($response['Date']);
    $Prediction->SetModelName($response['Model']);
    $Prediction->SetPathOutputs($OutputsTempFileLocation);
    $Prediction->SetPathInputs($InputsTempFileLocation);
    $Prediction->SetSeason($response['Season']);

    $Prediction->Prepare();


    if(!$response['PlayerList'])
        throw new Exception('No player list');

    CreatePlayerListFile($response['PlayerList'],$response['Dom'],$response['Vis'],$response['Season']);


    $Prediction->Run($GLOBALS['MyUUID']);
    $response['Success'] = $Prediction->IsSuccess(); //True;
    $response['Prediction'] =  $Prediction->GetPredictionResults($GLOBALS['MyUUID']);

    // $response['PlayerList'] = json_encode($response['PlayerList']);
}
catch(Exception  $e){

    $response['Success'] = False;
    $response['ErrorMessage'] = $e->getMessage();
}
    
echo json_encode($response);






?>