<?php


require_once 'MyTools.php';

$PlayTimePerTeam = 48*5;

function GetPrediction($Dom,$Vis){


    $p = shell_exec( 'whoami ');
    print_r($p);
    $p = shell_exec( ' env ');
    print_r($p);
    $p = shell_exec( ' conda env list ');
    print_r($p);

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

            if($Side !='Dom' || $Side |= 'Vis')
                throw new Exception('Side for player '.$p.' is '.$Side);

            $totplaytime -= intval($mp);

            $Data['Name'].append($p);
            $Data['MP'].append($mp.':00');
            $Data['Side'].append($Side);
            
        }

        if($totplaytime !=0)
            throw new Exception('Total play time left for team '.$Side.' is '.$totplaytime.'. It should be ==0');

    }

    $PlayerList = $Data;

}

try{

    $InputData = json_decode(file_get_contents('php://input'),true);

    $response = [];
    // $response['Success'] = False;
    $Teams = json_decode(file_get_contents('./Data.json'),true)['TEAMCodes_Names'];

    $response['PlayerList'] = $InputData['PlayerList'] ?? null;
    $response['Dom'] =  $InputData['Dom'] ?? null;
    $response['Vis'] =  $InputData['Vis'] ?? null;
    $response['Model'] =  $InputData['Model'] ?? null;
    $response['Season'] =  $InputData['Season'] ?? null;

    if(!is_numeric($response['Season']) || intval($response['Season'])<0)
        throw new Exception('Bad season :'.$response['Season']);

    if(!$response['Dom'] || !IsValidTeamName( $response['Dom'],$Teams))
        throw new Exception('Bad dom team name :'.$response['Dom']);

    if(!$response['Vis'] || !IsValidTeamName( $response['Vis'],$Teams))
        throw new Exception('Bad vis team name :'.$response['Vis']);

    if(!$response['PlayerList'])
        throw new Exception('No player list');

    if(IsValidModelName($response['Model']))
        throw new Exception('bad model name : '.$response['Model']);

    CreatePlayerListFile($response['PlayerList'],$response['Dom'],$response['Vis'],$response['Season']);

    $prediction = GetPrediction($Dom,$Vis);
    $response['Success'] = True;
    $p = shell_exec( 'conda run -n NBAPrediction python MakePrediction.py 2021-07-20 Phoenix\ Suns Milwaukee\ Bucks 456');
    $response['Prediction'] = file_get_contents('/home/ubuntu/Projects/ParisSportifIA/Data/Prediction/Ouputs/Prediction_456.json');
    $response['PlayerList'] = json_encode($response['PlayerList']);
}
catch(Exception  $e){

    $response['Success'] = False;
    $response['ErrorMessage'] = $e->getMessage();

}
finally{
    
    echo json_encode($response);
    die();
}






?>