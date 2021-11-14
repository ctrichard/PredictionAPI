#!/bin/bash

echo ''
echo ''
echo '=============================='
date
echo "Bet Prediction Bot "
echo '=='

source ~/.conda_init


# cd  ~/Projects/PredictionAPI/Prediction/
cd /var/www/PredictionAPI/Prediction

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

rm -rf $PathToOutputs/Prediction*
rm -rf $PathToInputs/MatchList_*

env

for BaseModelName in 'TrainWo_' 'TrainLast5Years_' 'TrainUpTo' 'TrainLast3Years_'
do
    for ModelYear in 2005 2006 2007 2008 2009 2010 2011 2012 2013 2014 2015 2016 2017 2018 2019 2020 2021 2022
    do
        ModelName=$BaseModelName$ModelYear
        echo $ModelName
    done
done

exit
# python ~/Projects/ParisSportifIA/MakePrediction.py ${MatchDate} "$DomTeam" "$VisTeam" $UUID $PathToInputs $PathToOutputs $Season

python MakeBotPredictions.py $PathToInputs $PathToOutputs $Season $UUID $ModelName ${MatchDate}
# a=`grep "UUID====" $UUID_finder`

# echo $a

python StoreBotPredictions.py $UUID $BetType $PathToInputs $PathToOutputs $ModelName ${MatchDate}