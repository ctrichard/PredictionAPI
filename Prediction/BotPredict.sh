#!/bin/bash

echo ''
echo ''
echo '=============================='
date
echo "Bet Prediction Bot "
echo '=='

source ~/.conda_init


# cd  ~/Projects/PredictionAPI/Prediction/

here=`pwd`


CondaEnv='BetScraper'
MatchDate=$1
# DomTeam=$2
# VisTeam=$3
UUID=`uuidgen`
PathToInputs=${here}'/Inputs/'
PathToOutputs=${here}'/Outputs/'



Season='2022'
BetType='Win'

echo $1
echo $2
echo $3

conda activate $CondaEnv

# python ~/Projects/ParisSportifIA/MakePrediction.py ${MatchDate} "$DomTeam" "$VisTeam" $UUID $PathToInputs $PathToOutputs $Season

python MakeBotPredictions.py $PathToInputs $PathToOutputs $Season $UUID ${MatchDate}
# a=`grep "UUID====" $UUID_finder`

# echo $a

python StoreBotPredictions.py $UUID $BetType $PathToInputs $PathToOutputs ${MatchDate}