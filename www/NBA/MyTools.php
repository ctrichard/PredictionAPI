<?php



class BadInput extends Exception{


    protected $BadInputType = "BadInput";

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null){
        // some code
    
        $message = $BadInputType.' : '.$message;
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


$DataLocation = '/home/ubuntu/Projects/ParisSportifIA/Data/';

$TeamDataLocation = $DataLocation.'Teams/';

$InputsTempFileLocation = 'Inputs/';
$OutputsTempFileLocation = 'Outputs/';


function GetTeamFilePath($DomCode,$Season){
    $path = $GLOBALS['TeamDataLocation'].$DomCode.'_'.$Season.'.csv';
    if(file_exists($path))
        return $path;
    else
        throw new FileNotFound($path);

    
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



function IsValidTeamName($Name,$Teams){

    return in_array($Name,array_values($Teams));

}


