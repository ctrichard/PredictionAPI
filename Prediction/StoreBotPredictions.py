import sys,os

sys.path.insert(0, '/home/ubuntu/Projects/ParisSportifIA')
sys.path.insert(0, '/home/ubuntu/Projects/Sports-betting')
from MyTools import MyTools
from BotBetScraper import Bet

import pandas as pd
import json
# from datetime import date, datetime
import datetime


from MakeBotPredictions import GetListBets

Storage_path = 'Data/PredictionData.csv'
ArxivStorage_path = 'Data/AxivPredictionData.csv'


class BetPrediction(Bet):  #Sports-betting/BotBetScraper.py



    def __init__(self):


        print(self.DataCols)
        try:
            self.DataCols.remove('SiteMatchId')
            self.DataCols.remove('Site')
            self.DataCols.remove('BetOdds')
            self.DataCols.remove('%SiteBet')
    
            self.DataCols.append('Prediction')
            self.DataCols.append('UUID')
            self.DataCols.append('BetOddDate')
            self.DataCols.append('PredictionDate')
        except ValueError:
            pass #already removed

        super().__init__()


    def IsValid(self):

        if(self.IsGoodType(self.Data['BetType'])):
            return True

        else : 
            return False    



def StorePredictionOutputs(PredictionBet,First):

    ArxivIsNewFile = False

    if(not os.path.exists(Storage_path)):
        First = True

    if(not os.path.exists(ArxivStorage_path)):
        ArxivIsNewFile = True


    #append to arxiv
    with open(ArxivStorage_path,'a') as f:
        f.write(PredictionBet.to_csv(PrintCols = ArxivIsNewFile))

    #rewrite file. To have only this file read by API
    with open(Storage_path,'a') as f:
        f.write(PredictionBet.to_csv(PrintCols = First))



def StorePredictions(UUID,BetInfo,First):

    prediction_outputs = MyTools.GetPredictionOutput(UUID,PathPredictionOutputs)
    prediction_inputs = MyTools.GetPredictionInput(UUID,PathPredictionInputs)
    try:
        prediction_playerlist = MyTools.GetPlayerListForPrediction(UUID,PathPredictionInputs)
    except FileNotFoundError:
        prediction_playerlist  = None    


        
    print(prediction_outputs)
    print(prediction_inputs)
    print(prediction_playerlist)


    now = datetime.datetime.now()#BetScraper_timezone)

    Side =None
    if(BetInfo['BetValue'] == BetInfo['Dom'] ):
        Side = 'Dom'
    elif(BetInfo['BetValue'] ==  BetInfo['Vis']):    
        Side = 'Vis'
    else:
        print(BetInfo)
        raise ValueError('Wrong bet info !')    #only for  win bet

    B = BetPrediction()

    B.SetData({
            'UUID' : UUID,
            'BetOddDate' : now.strftime('%Y:%m:%d %H:%M'),
            'PredictionDate' : BetInfo['Date'],
            'Match' : str(prediction_inputs['Dom'][0])+' - '+str(prediction_inputs['Vis'][0]),  # je sais pas trop pourquoi les valeurs sont dans des tableaux...
            'Dom' : str(prediction_inputs['Dom'][0]), 
            'Vis' : str(prediction_inputs['Vis'][0]), 
            'MatchDate': prediction_inputs['Date'][0],
            'BetType': 'Win',
            'BetValue' : str(prediction_inputs[Side][0]), #to keep name consistancy
            'Prediction' : prediction_outputs[(0 if Side=='Dom' else 1)],
    })  



    StorePredictionOutputs(B,First)


if __name__ == '__main__':

    print(sys.argv)
    
    UUID = sys.argv[1] 
    BetType = sys.argv[2] 

    # if(not BetPrediction.IsGoodType(BetType)):
    #     raise ValueError('BetType = ',BetType,'. It should be one in ', json.dumps(BetPrediction.ValidBetTypes))

    PathPredictionInputs = sys.argv[3] 
    PathPredictionOutputs = sys.argv[4] 

    try:
        Date = datetime.date(*map(int,sys.argv[5].split('-')))
    except : 
        Date = datetime.datetime.now()+datetime.timedelta(days=1)


    Bets = GetListBets(Date)

    print(Bets)
    First=True
    for i,row in Bets.iterrows():
        try:
            StorePredictions(UUID+'_'+str(i),row,First)
            First=False
        except FileNotFoundError:    #because prediction failed
            print("ERROR  =========================== Prediction failed")
            print(row)
            print("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx")
            pass

