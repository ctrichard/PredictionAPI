<?php



class BadInput extends Exceptions{


    $BadInputType = "BadInput"

    public __construct(string $message = "", int $code = 0, ?Throwable $previous = null){
        // some code
    
        $message = $BadInputType.' : '.$message;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}

class BadTeamName extends BadInput{

    $BadInputType = "BadTeamName"


}
class BadSeason extends Exceptions{

    $BadInputType = "BadSeason"

}

class FileNotFound extends Exceptions{

    $BadInputType = "File not found"

}


$DataLocation = '/home/ubuntu/Projects/ParisSportifIA/Data/';

$TeamDataLocation = $DataLocation.'Teams/';


function GetTeamFilePath($DomCode,$Season){
    $path = $TeamDataLocation.$DomCode.'_'.$Season.'.csv';
    if(file_exists($path))
        return $path;
    else
        throw new FileNotFound($path)

    
}

function GetTeamCodeFromName($TeamName){

    $Teams = json_decode(file_get_contents('./Data.json'),true)['TEAMCodes_Names'];


    if(!IsValidTeamName($TeamName,$Teams) ){
        throw new BadTeamName("Bad team name : ".$TeamName);
    }


}



function IsValidTeamName($Name,$Teams){

    return in_array($Name,array_values($Teams));

}


