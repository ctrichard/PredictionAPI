<?php


require_once 'MyTools.php';




$ModelName = $_GET['ModelName'] ?? "TestForAPI" ;


try{

    if(!IsValidModelName($ModelName))
        throw new Exception('Bad model name : '.$ModelName);

    $ModelResults = GetModelResults($ModelName);
    // print_r($ModelResults);

}
catch(Exception $a){
    echo 'ERROR'.PHP_EOL;
    print_r($a->getMessage());
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



        <Graph data ='0;1;2;3,1;2;3'>
        </Graph>





    <script type="text/javascript" src="http://d3js.org/d3.v5.js" defer></script>
    <script type="text/javascript" src="./app.js" defer></script>
    <script type="text/javascript" src="../Graph.js" defer></script>

    </body>
    </html>