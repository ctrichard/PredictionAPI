<?php

require_once 'MyTools.php';
require_once 'Predict.php';


class CheckPlayerStat extends RunPrediction{

    protected $PlayerList = [];
    protected $Results = '';


    
    const CheckModelName = False;

    public function __construct(){

        parent::__construct();
        $this->UUID = Generate_UUID(20);
    }

    public function SetPlayerList($PlayerList){
        $this->PlayerList = $PlayerList;
    }


    public function Run($Side = 'Dom'){

        $command = 'conda run ';
        $command .= '-n '.$this->CondaEnv.' ';
        $command .= ' python ';
        $command .= ' ~/Projects/ParisSportifIA/CheckPlayerHasData.py ';
        $command .= ' '.$this->Date.' ';
        $command .= ' '.$this->$Side.' ';
        $command .= ' '.json_encode($this->Players[$Side]).' ';
        // $command .= ' '.$this->Vis.' ';
        // $command .= ' '.$this->UUID.' ';
        // $command .= ' '.$this->PathToInputs.' ';
        $command .= ' '.$this->PathToOutputs.' ';


        // $this->log = shell_exec( 'conda run -n NBAPrediction python MakePrediction.py 2021-07-20 Phoenix\ Suns Milwaukee\ Bucks 456');
        $this->log .= 'Executing : '.$command;
        $this->log .= '======================';
        $this->Results = shell_exec($command);
        $this->log .= $this->Results;

        
        $fp = fopen($this->PathToOutputs.'LogCheckPlayerData_'.$this->UUID.'.log', 'a');
        fwrite($fp, $this->log);
        fclose($fp);

        $this->Success = True;

        return $this->Results; 

    }


}

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
    $Prediction->SetPathOutputs($OutputsTempFileLocation);

    $Check->Prepare();
    $response['Results'] ='';
    $response['Results'] .= $Check->Run('Dom');
    $response['Results'] .= $Check->Run('Vis');
    $response['Success'] = True; 


}
// catch( BadInput | FileNotFound $e){
catch( Exception $e){

    $response['Success'] = False; 
    $response['ErrorMessage'] = $e->getMessage();

}


echo json_encode($response);



