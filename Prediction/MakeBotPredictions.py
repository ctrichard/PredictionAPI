import sys,os
from typing import Match

sys.path.insert(0, '/home/ubuntu/Projects/ParisSportifIA')
from MyTools import MyTools
from MakePrediction import MakePrediction
sys.path.insert(0, '/home/ubuntu/Projects/Sports-betting')
from BotBetScraper import GetOddDataPath

import pandas as pd
import json
from datetime import date

import datetime

Storage_path = 'Data/PredictionData.csv'


BetScrapper_location = '/home/ubuntu/Projects/Sports-betting/'  


def FullOddDataPath(MatchDate):
    return BetScrapper_location+GetOddDataPath(MatchDate)


def GetListBets(MatchDate):

    df  = None

    with open(FullOddDataPath(MatchDate),'r') as f:
        df = pd.read_csv(f)
        df.drop_duplicates(subset=['Match','MatchDate','BetType','BetValue'] ,keep='last',inplace=True,ignore_index=True)



    return df




def make_predict(Date,VisName,DomName,UUID="0",PathInputs='',PathOutputs='',ModelName='TrainWo_2022',Season='2022'):

    MakePrediction(Date,VisName,DomName,UUID=UUID,PathInputs=PathInputs,PathOutputs=PathOutputs,ModelName=ModelName,Season=Season)



def MakeBotPredictions(UUID,MatchDate,PathInputs='',PathOutputs='',ModelName='TrainWo_2022',Season='2022'):

    Matches = GetListBets(MatchDate)

    print(Matches)

    for i,row in Matches.iterrows():

        print('......................................')
        print(row)
        Date = row['MatchDate'].split(' ')[0]  # just YYY-MM-DD
        VisName = row['Vis']
        DomName = row['Dom']
        make_predict(Date=Date,VisName=VisName,DomName=DomName,UUID=UUID+'_'+str(i),PathInputs=PathInputs,PathOutputs=PathOutputs,ModelName=ModelName)


if __name__ == '__main__':

    print(sys.argv)


    PathInputs  = sys.argv[1]
    PathOuputs = sys.argv[2]
    Season=sys.argv[3]
    ModelName='TrainWo_'+Season

    UUID = sys.argv[4]

    try:
        Date = datetime.date(*map(int,sys.argv[5].split('-')))
    except : 
        Date = datetime.datetime.now()+datetime.timedelta(days=1)



    MakeBotPredictions(UUID,Date,PathInputs=PathInputs,PathOutputs=PathOuputs,ModelName=ModelName,Season=Season)



