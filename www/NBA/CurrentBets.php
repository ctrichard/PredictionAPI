<?php


require_once 'MyTools.php';

$DateStr =  date("F j, Y, g:i a");         
$Date =  date("Y-M-D")
echo $Date

?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />

        <title>Prediction</title>
        <link rel="stylesheet" type="text/css" href="../root.css">
        <link rel="stylesheet" type="text/css" href="../perf.css">

    </head>
        
    <body>


        <!-- <div class="GraphContainer"> -->

            <!-- <graph-std data="ProbaGoodAnswerVsEstimatedProba-CountDomProba"> -->
            <!-- </graph-std> -->
        <!-- </div> -->
        <div class="Title">
            Paris en cours : <?php echo $Date ?>
        </div>
        <div class="SubTitle" id="accuracy">
        </div>




    <script type="text/javascript" src="http://d3js.org/d3.v5.js"></script>
    <script type="text/javascript" src="../Graphs.js"></script>
    <!-- <script type="text/javascript" src="./app.js"></script> -->
    <script>
    

    </script>
    </body>
    </html>