<?php



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
    const CheckSeason = True;

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
    public function SetSeason($Season){
        $this->Season = $Season;
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

        if(static::CheckSeason && (!is_numeric($this->Season) || intval($this->Season)<0) )
           throw new Exception('Bad season :'.$this->Season);

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
        $this->log = 'Executing : '.$command.PHP_EOL;
        $this->log .= '======================'.PHP_EOL;
        $this->log .= shell_exec($command).PHP_EOL;

        
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

    protected function CheckInputs(){

        parent::CheckInputs();

        if(!$this->PlayerList || !$this->PlayerList['Dom'] || !$this->PlayerList['Vis'])
           throw new Exception('No player list');
           
    }



    public function Run($Side = 'Dom'){

        $command = 'conda run ';
        $command .= '-n '.$this->CondaEnv.' ';
        $command .= ' python ';
        $command .= ' ~/Projects/ParisSportifIA/CheckPlayerHasData.py ';
        $command .= ' '.$this->Date.' ';
        $command .= ' '.$this->$Side.' ';
        $command .= ' \''.json_encode(array_keys($this->PlayerList[$Side])).'\' ';
        $command .= ' '.$this->Season.' ';
        // $command .= ' '.$this->UUID.' ';
        // $command .= ' '.$this->PathToInputs.' ';
        // $command .= ' '.$this->PathToOutputs.' ';


        // $this->log = shell_exec( 'conda run -n NBAPrediction python MakePrediction.py 2021-07-20 Phoenix\ Suns Milwaukee\ Bucks 456');
        $this->log = 'Executing : '.$command.PHP_EOL;
        $this->log .= '======================'.PHP_EOL;
        $this->Results = shell_exec($command);
        $this->log .= $this->Results.PHP_EOL;

        
        $fp = fopen($this->PathToOutputs.'LogCheckPlayerData_'.$this->UUID.'.log', 'a');
        fwrite($fp, $this->log);
        fclose($fp);

        $this->Success = True;

        return $this->Results; 

    }


}



class BadInput extends Exception{


    protected $BadInputType = "BadInput";

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null){
        // some code
    
        $message = $this->BadInputType.' : '.$message;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}

class BadTeamName extends BadInput{

    protected $BadInputType = "BadTeamName";


}
class BadSeason extends BadInput{

    protected $BadInputType = "BadSeason";

}

class FileNotFound extends Exception{

    protected $BadInputType = "File not found";

}

$ProjectLocation = "/home/ubuntu/Projects/ParisSportifIA/";
$DataLocation = $ProjectLocation.'Data/';
$ModelLocation = $ProjectLocation.'Models/';

$TeamDataLocation = $DataLocation.'Teams/';

$InputsTempFileLocation = './Inputs/';
$OutputsTempFileLocation = './Outputs/';


function GetModelResults($ModelName){
    
    $ModelResults = json_decode(file_get_contents($ModelLocation.$ModelName));
    return $ModelResults;

}

function GetTeamFilePath($DomCode,$Season){
    $path = $GLOBALS['TeamDataLocation'].$DomCode.'_'.$Season.'.csv';
    if(file_exists($path))
        return $path;
    else
        throw new FileNotFound('Team file missing : '.$path);
    
}

function GetModels(){

    return json_decode(file_get_contents('Models.json'),true);
}



function GetTeamCodeFromName($TeamName){

    $Teams = json_decode(file_get_contents('./Data.json'),true)['TEAMCodes_Names'];


    if(!IsValidTeamName($TeamName,$Teams) ){
        throw new BadTeamName("Bad team name : ".$TeamName);
    }


    foreach($Teams as $key => $value) {

        if($value == $TeamName)
            return $key;
    }


}



function IsValidModelName($Name){



    return in_array($Name,array_keys(GetModels()));

}



function IsValidTeamName($Name,$Teams){

    return in_array($Name,array_values($Teams));

}


function GetTeamInfo($TeamCode,$Season){

    $File = file_get_contents(GetTeamFilePath($TeamCode,$Season));
    $Array = array_map("str_getcsv", explode("\n", $File));
    return $Array;

}

function CheckPlayerName($playername,$teamname,$season){

    $Team = GetTeamInfo(GetTeamCodeFromName($teamname),$season);

    if(!in_array($playername , array_keys($Team)))
        throw new Exception('Player '.$playername.' is not in team '.$teamname.' during season '.$season);
    
    return true;
    
}



function Generate_UUID($length = 20){

    $bytes = random_bytes($length);
    return bin2hex($bytes);

}


