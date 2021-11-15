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

#!!!!!!!!!!!!!!!
MainModelName='TrainWo_2022'


Storage_path = 'Data/PredictionData'
ArxivStorage_path = 'Data/AxivPredictionData.csv'


def GetStoragePath(ModelName,MainModel=False):
    if(MainModel):
        Path = Storage_path+'.csv'
    else:
        Path = Storage_path+'_'+ModelName+'.csv'

    return Path



class BetPrediction(Bet):  #Sports-betting/BotBetScraper.py



    def __init__(self,ModelName):


        self.ModelName = ModelName

        self.MinimalCols = []

        print(self.DataCols)
        try:
            self.DataCols.remove('SiteMatchId')
            self.DataCols.remove('Site')
            # self.DataCols.remove('BetOdds')
            self.DataCols.remove('%SiteBet')
            self.DataCols.remove('Date')
    
            self.DataCols.append('Prediction')
            self.DataCols.append('UUID')
            self.DataCols.append('BetOddDate')
            self.DataCols.append('PredictionDate')



        except ValueError:
            pass #already removed

        self.MinimalCols.append('UUID')
        self.MinimalCols.append('PredictionDate')
        self.MinimalCols.append('Prediction')


        super().__init__()


    def IsValid(self):

        if(self.IsGoodType(self.Data['BetType'])):
            return True

        else : 
            return False  





def StorePredictionOutputs(PredictionBet,First,MainModel=False):


    ArxivIsNewFile = False

    if(not os.path.exists(GetStoragePath(PredictionBet.ModelName,MainModel))):
        First = True

    if(not os.path.exists(ArxivStorage_path)):
        ArxivIsNewFile = True


    #append to arxiv
    with open(ArxivStorage_path,'a') as f:
        f.write(PredictionBet.to_csv(PrintCols = ArxivIsNewFile))

    #rewrite file. To have only this file read by API
    Short=True
    if(MainModel):
        Short=False


    with open(GetStoragePath(PredictionBet.ModelName,MainModel),'w' if First else 'a') as f:
        f.write(PredictionBet.to_csv(PrintCols = First, Short=Short))

        print('----------------------')
        print('------')
        print(PredictionBet.Data)
        print(GetStoragePath(PredictionBet.ModelName,MainModel))
        print(PredictionBet.to_csv(PrintCols = First, Short=Short))
        print(PredictionBet.ModelName)

    if(MainModel):  #To save also in specific model file
        with open(GetStoragePath(PredictionBet.ModelName,False),'w' if First else 'a') as f:
            f.write(PredictionBet.to_csv(PrintCols = First, Short=True))



def StorePredictions(UUID,BetInfo,First,ModelName='',MainModel=False):

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

    B = BetPrediction(ModelName)

    B.SetData({
            'UUID' : UUID,
            'BetOddDate' : BetInfo['Date'],
            'BetOdds' : BetInfo['BetOdds'],
            'PredictionDate' : now.strftime('%Y:%m:%d %H:%M') ,
            'Match' : str(prediction_inputs['Dom'][0])+' - '+str(prediction_inputs['Vis'][0]),  # je sais pas trop pourquoi les valeurs sont dans des tableaux...
            'Dom' : str(prediction_inputs['Dom'][0]), 
            'Vis' : str(prediction_inputs['Vis'][0]), 
            'MatchDate': prediction_inputs['Date'][0],
            'BetType': 'Win',
            'BetValue' : str(prediction_inputs[Side][0]), #to keep name consistancy
            'Prediction' : prediction_outputs[(0 if Side=='Dom' else 1)],
    })  



    StorePredictionOutputs(B,First,MainModel)


if __name__ == '__main__':

    print(sys.argv)
    
    UUID = sys.argv[1] 
    BetType = sys.argv[2] 

    # if(not BetPrediction.IsGoodType(BetType)):
    #     raise ValueError('BetType = ',BetType,'. It should be one in ', json.dumps(BetPrediction.ValidBetTypes))

    PathPredictionInputs = sys.argv[3] 
    PathPredictionOutputs = sys.argv[4] 

    ModelName=sys.argv[5]

    IsMainModel=False
    if(ModelName==MainModelName):
        IsMainModel=True
    try:
        Date = datetime.date(*map(int,sys.argv[6].split('-')))
    except : 
        Date = datetime.datetime.now()+datetime.timedelta(days=1)


    Bets = GetListBets(Date)

    print(Bets)
    First=True
    for i,row in Bets.iterrows():
        try:
            StorePredictions(UUID+'_'+str(i),row,First,ModelName,IsMainModel)
            First=False
        except FileNotFoundError:    #because prediction failed
            print("ERROR  =========================== Prediction failed")
            print(row)
            print("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx")
            pass

