<?php


require_once 'MyTools.php';

$PlayTimePerTeam = 48*5;

$MyUUID  =  Generate_UUID(20); 



class RunPrediction{

    protected $Dom ='';
    protected $Vis ='';
    protected $Date = '';
    protected $PathToInputs = './';
    protected $PathToOutputs = './';
    protected $ModelName = '';
    protected $UUID = null;
    protected $Success = False;
    
    protected $log = '';

    protected $CondaEnv = 'NBAPrediction';


    const CheckTeamNames = True;
    const CheckModelName = True;
    const CheckPaths = True;

    public function __construct(){


    }


    public function SetDomTeam($Name){
        $this->Dom = $Name;
    }
    public function SetVisTeam($Name){
        $this->Vis = $Name;
    }
    public function SetDate($Date){
        $this->Date = $Date;
    }
    public function SetPathInputs($P){
        $this->PathToInputs = $P;
    }
    public function SetPathOutputs($P){
        $this->PathToOutputs = $P;
    }
    public function SetModelName($Name){
        $this->ModelName = $Name;
    }
    public function IsSuccess(){
        return $this->Success;
    }

    protected function CheckInputs(){

        $Teams = json_decode(file_get_contents('./Data.json'),true)['TEAMCodes_Names'];

        if(static::CheckTeamNames &&  (  !$this->Dom || !IsValidTeamName($this->Dom,$Teams) ) )
            throw new Exception('Bad dom team name :'.$this->Dom);

        if(static::CheckTeamNames &&  (  !$this->Vis  || !IsValidTeamName($this->Vis,$Teams)) )
           throw new Exception('Bad vis team name :'.$this->Vis);

        if(static::CheckModelName  && !IsValidModelName($this->ModelName))
           throw new Exception('bad model name : '.$this->ModelName);

        if(static::CheckPaths && !is_dir($this->PathToInputs))
           throw new FileNotFound('Bad location for inputs : '.$this->PathToInputs);

        if(static::CheckPaths && !is_dir($this->PathToOutputs))
           throw new FileNotFound('Bad location for outputs : '.$this->PathToOutputs);

    }

    public function Prepare(){

        $this->CheckInputs();


        $this->Dom = str_replace(' ','\ ',$this->Dom);
        $this->Vis = str_replace(' ','\ ',$this->Vis);

    }

    public function Run($UUID=0){

        $this->UUID = $UUID;
        $command = 'conda run ';
        $command .= '-n '.$this->CondaEnv.' ';
        $command .= ' python ';
        $command .= ' ~/Projects/ParisSportifIA/MakePrediction.py ';
        $command .= ' '.$this->Date.' ';
        $command .= ' '.$this->Dom.' ';
        $command .= ' '.$this->Vis.' ';
        $command .= ' '.$this->UUID.' ';
        $command .= ' '.$this->PathToInputs.' ';
        $command .= ' '.$this->PathToOutputs.' ';


        // $this->log = shell_exec( 'conda run -n NBAPrediction python MakePrediction.py 2021-07-20 Phoenix\ Suns Milwaukee\ Bucks 456');
        $this->log .= 'Executing : '.$command;
        $this->log .= '======================';
        $this->log = shell_exec($command);

        
        $fp = fopen($this->PathToOutputs.'LogPrediction_'.$this->UUID.'.log', 'w');
        fwrite($fp, $this->log);
        fclose($fp);

        $this->Success = True;


    }

    public function GetPredictionResults(){

        $path = $this->PathToOutputs.'Prediction_'.$this->UUID.'.json';

        if(!file_exists($path))
            throw new FileNotFound('Cant find prediction results : '.$path );

        return json_decode(file_get_contents($path),true);

    }


}

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


function Generate_UUID($length = 20){

    $bytes = random_bytes($length);
    return bin2hex($bytes);

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

        if($totplaytime !=0)
            throw new Exception('Total play time left for team '.$Side.' is '.$totplaytime.'. It should be ==0');

    }

    $PlayerList = $Data;


    $path = $GLOBALS['InputsTempFileLocation'];
    if(!is_dir($path))
       throw new FileNotFound('Bad location for Player list : '.$path);

    $fp = fopen($path.'PlayerList_'.$GLOBALS['MyUUID'].'.json', 'w');
    fwrite($fp, json_encode($PlayerList));
    fclose($fp);

}


try{

    $InputData = json_decode(file_get_contents('php://input'),true);

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

    $Prediction->Prepare();


    if(!is_numeric($response['Season']) || intval($response['Season'])<0)
        throw new Exception('Bad season :'.$response['Season']);

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