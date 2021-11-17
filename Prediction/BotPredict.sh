#!/bin/bash

echo ''
echo ''
echo '=============================='
date
echo "Bet Prediction Bot "
echo '=='

source ~/.conda_init


DoPrediction=$1  # false to juste update bet odds

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


conda activate $CondaEnv

rm -rf $PathToOutputs/Prediction*
rm -rf $PathToInputs/MatchList_*

env

ModelNames=()
i=0
for BaseModelName in 'TrainWo_' 'TrainLast5Years_' 'TrainUpTo' 'TrainLast3Years_'
do
    for ModelYear in 2005 2006 2007 2008 2009 2010 2011 2012 2013 2014 2015 2016 2017 2018 2019 2020 2021 2022
    do
        ModelName=$BaseModelName$ModelYear
        echo $ModelName

        ModelNames+=($ModelName)

    done
done

ModelNamesInStr=$(IFS=$' '; echo "${ModelNames[@]}" )


if [$DoPrediction =='True']
then
    # python ~/Projects/ParisSportifIA/MakePrediction.py ${MatchDate} "$DomTeam" "$VisTeam" $UUID $PathToInputs $PathToOutputs $Season
    python MakeBotPredictions.py $PathToInputs $PathToOutputs $Season $UUID "$ModelNamesInStr" ${MatchDate}
fi

for ModelName in ${ModelNames[@]}
do
    python StoreBotPredictions.py $UUID $BetType $PathToInputs $PathToOutputs $ModelName ${MatchDate}
done    

