<?php


require_once 'MyTools.php';




$ModelName = $_GET['ModelName'] ?? "TestForAPI" ;

if(!IsValidModelName($ModelName))
    throw new Exception('Bad Model name : '.$ModelName);

try{

    $ModelResults = GetModelResults($ModelName);
    print_r($ModelResults);

}
catch(Exception $a){
    echo 'ERROR';
    print_r($a);
}




?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />

        <title>Prediction</title>
        <link rel="stylesheet" type="text/css" href="../root.css">
        <link rel="stylesheet" type="text/css" href="../match.css">

    </head>
        
    <body>








    <script type="text/javascript" src="http://d3js.org/d3.v5.js" defer></script>
    <script type="text/javascript" src="./app.js" defer></script>

    </body>
    </html>