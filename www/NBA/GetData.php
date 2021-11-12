<?php





if($_GET['Data']=='PredictionData'){

    echo file_get_contents('../../Prediction/Data/PredictionData.csv')
    exit()
}



?>