var MatchData = {Season : 2021, Model : 'TestForAPI', Date : '2021-07-20'}
var MatchPlayerData = {Dom : '', Vis : ''}

var TeamPlayers =  {'Dom' : '', 'Vis' : ''}



const MatchDuration = 48
const TotalPlayerTimePerTeam = MatchDuration*5

const MinPlayerTime = 10 // to avoid small times

const NotifDuration = 10


function IsValidMatch(){
    lefttimeDom = CheckTeamTotalTimePlayed('Dom') 
    lefttimeVis = CheckTeamTotalTimePlayed('Vis')

    if(  MatchData['Dom']!=undefined &&  MatchData['Vis']!=undefined &&  MatchData['Dom'] != MatchData['Vis'] 
            && lefttimeDom==0 && lefttimeVis==0){

        return true;
    }
    else{
        return false;
    }

}

function UpdatePredictButton(){

    if( IsValidMatch()){
        document.getElementById('ResultsPrediction').classList.add('Active');
        console.log("Active")
    }
    else{
        document.getElementById('ResultsPrediction').classList.remove('Active');
        console.log("Not Active")
    }
}

function selectTeam(Side='Dom',value){

    MatchData[Side] = value
    console.log(MatchData)
    ResetPredictions()
    
    UpdateTeamInfo()
    
    // UpdatePredictButton()
}


async function UpdateTeamInfo(){

    ResetTeamPlayers('Dom')
    ResetTeamPlayers("Vis")
    
    let ResponseData = await fetch('./TeamInfo.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({TeamDom: MatchData['Dom'], TeamVis : MatchData['Vis'],Season :  MatchData['Season'] }),
    })
    .then(response => response.json())

    
    TeamPlayers['Dom'] = JSON.parse(ResponseData['Dom'])
    TeamPlayers['Vis'] = JSON.parse(ResponseData['Vis'])


    DrawTeamPlayer('Dom')
    DrawTeamPlayer('Vis')
    
    MatchPlayerData = {Dom : '', Vis : ''}

    
}

async function CheckPlayerDataAvailable(){

    let ResponseData = await fetch('./CheckPlayerStats.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({Dom: MatchData['Dom'], Vis : MatchData['Vis'],Season :  MatchData['Season'], Date : MatchData['Date'], PlayerList : MatchPlayerData }),
    })
    .then(response => response.json())

    if(!ResponseData['Success'])
        $message =''
        ResponseData['Dom'].forEach(element=>{
            if(!element['Success'])
                $message += element['NotifMessage']+'\n'
        })
        ResponseData['Vis'].forEach(element=>{
            if(!element['Success'])
                $message += element['NotifMessage']+'\n'
        })       
        CreateNotification('Fail',$message)


    return ResponseData

}

function ResetTeamPlayers(Side){

    let div = document.getElementById('TeamPlayers'+Side)
    while (div.firstChild) {
        div.removeChild(div.lastChild);
      }

}

function DrawTeamPlayer(Side){

    TeamPlayers[Side].forEach(element => {
        if(element[1]=='Name' | element[0]==undefined)
          return //=>continue

        let div = document.createElement('div');
        div.classList.add("TeamPlayer")
        div.innerHTML = element[1]+' '+element[element.length - 1]
        if(element[5] > 0 && element[3]>0 ) //&& ( (element[5] / element[3]) > MinPlayerTime) )
            div.innerHTML += ' '+parseInt(element[5] / element[3]);

        let input = document.createElement("input");
        input.classList.add("TeamPlayerTimeInput")
        input.type = "number";
        input.id = 'TimeInput'+element[1];
        input.min = '10'
        input.max = '48'
        input.PlayerName = element[1];
        input.Side = Side;
        input.size = 2;

        // input.value =  element[1] / element[3] : 0;
        // input.value = 0    


        let parentdiv = document.getElementById('TeamPlayers'+Side)
        parentdiv.appendChild(div);
        div.appendChild(input);


        function InputModifs(evt){

            if(evt.currentTarget.value > MatchDuration){
                CreateNotification('Fail','Un match dure au maximum '+MatchDuration+' minutes')
                evt.currentTarget.value = MatchDuration
            }
            
            if(evt.currentTarget.value < MinPlayerTime && evt.currentTarget.value >0){
                CreateNotification('Fail','Ne pas mettre moins de '+MinPlayerTime+' minutes')
                evt.currentTarget.value = MinPlayerTime
            }

            if(evt.currentTarget.value < 0){
                evt.currentTarget.value = 0
            }

            if(MatchPlayerData[evt.currentTarget.Side]=='')
                MatchPlayerData[evt.currentTarget.Side]={}

            MatchPlayerData[evt.currentTarget.Side][evt.currentTarget.PlayerName] = evt.currentTarget.value;
            console.log(MatchPlayerData)
        }

        document.getElementById(input.id).addEventListener('change', (event) => {

            InputModifs(event);
            // CheckTeamTotalTimePlayed(event.currentTarget.Side)
            UpdatePredictButton()
        });

    });

}

function CheckTeamTotalTimePlayed(Side){

    TotalTimePlayed = TotalPlayerTimePerTeam

    TeamPlayers[Side].forEach(playerdata => {
        if(playerdata[1]=='Name' | playerdata[0]==undefined)
          return //=>continue

        let element = document.getElementById('TimeInput'+playerdata[1])
        TotalTimePlayed -= element.value

    })

    if(TotalTimePlayed <0 ){
        CreateNotification('Warning','Le nombre total de temps par équipe ne peut pas excéder '+TotalPlayerTimePerTeam+' minutes.')
    }

    console.log('Left ',TotalTimePlayed,'minutes for team',Side)

    return TotalTimePlayed

}


function selectLogo(Side='LogoA',value){

    teamname = value.replaceAll(" ","_").toLowerCase();
    document.getElementById(Side).src = '../pictures/'+teamname+'_2021.png'

}


function CreateTeamSelectionOptions(Team='A',NBAData){
    let element = document.getElementById('Teamselection'+Team);
  
    let x = element.length +1;
    for (let i = 0; i <  x ; i++) {
        element.remove(element.length-1);
    }

    for (team of Object.entries(NBAData['TEAMCodes_Names'])) {

      let opt = document.createElement('option');   
      opt.appendChild( document.createTextNode(NBAData['TEAMCodes_Names'][team[0]]) );
      element.appendChild(opt); 
  
    }


  }

function ResetPredictions(){

    temptransi = document.getElementById('BarA').style.transitionDuration
    document.getElementById('BarA').style.transitionDuration = '0'
    document.getElementById('BarB').style.transitionDuration = '0'
    
    document.getElementById('BarA').style.width = '0%'
    document.getElementById('BarB').style.width = '0%'

    document.getElementById('TextBarA').innerHTML =  ''
    document.getElementById('TextBarB').innerHTML =  ''

    document.getElementById('TextBarA').style.opacity = 0
    document.getElementById('TextBarB').style.opacity = 0

    document.getElementById('BarA').style.transitionDuration = temptransi
    document.getElementById('BarB').style.transitionDuration = temptransi


}

function ShowPrediction(Data){


    document.getElementById('BarA').style.width = (Data['Prediction'][0]*100).toFixed(2)+'%'
    document.getElementById('BarB').style.width = (Data['Prediction'][1]*100).toFixed(2)+'%'

    document.getElementById('TextBarA').innerHTML =  (Data['Prediction'][0]*100).toFixed(2)+'%'
    document.getElementById('TextBarB').innerHTML =  (Data['Prediction'][1]*100).toFixed(2)+'%'

    document.getElementById('TextBarA').style.opacity = 1
    document.getElementById('TextBarB').style.opacity = 1

}

function ShowPredictionError(){

    document.getElementById('BarA').style.width = '100%'
    document.getElementById('BarA').style.backgroundColor = 'rgba(148, 148, 148, 0.582)'
    document.getElementById('BarB').style.width = '100%'
    document.getElementById('BarB').style.backgroundColor = 'rgba(148, 148, 148, 0.582)'

    document.getElementById('TextBarA').innerHTML =  '?'
    document.getElementById('TextBarB').innerHTML =  '?'

    document.getElementById('TextBarA').style.opacity = 1
    document.getElementById('TextBarB').style.opacity = 1


}


async function GetPrediction(DomCode,VisCode,Season,Model,MatchDate){

    if(!IsValidMatch() ){
        console.error('Not valid match to fetch predictions')
        // arclassList.add('Active');

        return
    }
    else
        console.log('Valid match to fetch predictions')


    PlayersHaveData = await CheckPlayerDataAvailable()

    if(!PlayersHaveData['Success'])
        console.log(PlayersHaveData)
        return


    const Prediction = await fetch('./Predict.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({Season : Season, Model : Model, Date : MatchDate, Dom: DomCode, Vis : VisCode, PlayerList : MatchPlayerData}),
    })
    .then(response => response.json())

    console.log(Prediction)

    if(Prediction['Success'])
        ShowPrediction(Prediction)
    else
        ShowPredictionError()


    return Prediction;
}

async function LoadData(){
      
    const NBAData = await fetch('./Data.json', {
          headers: {
              'Accept': 'application/json'
          }
      })
      .then(response => response.json());

    // const NBAData = NBADataResp.json()

    CreateTeamSelectionOptions('A',NBAData)
    CreateTeamSelectionOptions('B',NBAData)

}

LoadData();

document.getElementById('TeamselectionA').addEventListener('change', (event) => {
    selectTeam("Dom",document.getElementById('TeamselectionA').value);
    selectLogo("LogoA",document.getElementById('TeamselectionA').value);

});
document.getElementById('TeamselectionB').addEventListener('change', (event) => {
    selectTeam("Vis",document.getElementById('TeamselectionB').value);
    selectLogo("LogoB",document.getElementById('TeamselectionB').value);

});
// document.getElementById('matchDate').addEventListener('change', (event) => {
//     selectTeam("Date",document.getElementById('matchDate').value);
// });

document.getElementById('ResultsPrediction').addEventListener('click', (event) => {


    let Prediction =  GetPrediction(MatchData['Dom'],MatchData['Vis'],MatchData['Season'],MatchData['Model'],MatchData['Date'])

});









//Notifs


function CreateNotification(Type='Fail',Message=''){

    let Notif = {}
    Notif['Type']= Type
    Notif['Messages'] = [Message]
    console.log(Message)
    
    Notify(Notif);

}

function Notify(Notif){

    if(document.getElementById('Notif'))
      document.getElementById('Notif').remove();
  
    let newDiv = document.createElement("div");
    newDiv.id = 'Notif';
    newDiv.classList.add('basicNotif');
  
    if(Notif.Type=='Success'){
      newDiv.classList.add('SuccessNotif');
      Notif['Messages'].push('Shape Valid');
    }
    else if(Notif['Type']=='Neutral')
      newDiv.classList.add('NeutralNotif');
    else if(Notif['Type']=='Warning')
      newDiv.classList.add('WarningNotif');
    else if(Notif['Type']=='Fail')
      newDiv.classList.add('FailNotif');
  
    Array.prototype.forEach.call(Notif['Messages'], element => {
      //newDiv.appendChild(document.createTextNode(''));
      if(typeof element === 'object'){
        for(let ii in element){
          newDiv.innerHTML += element[ii];
          newDiv.innerHTML += '<br>';
        }
      }
      else{
        newDiv.innerHTML += element;
        newDiv.innerHTML += '<br>';
      }
    });
  
    document.body.appendChild(newDiv);
  
    d3.select('#Notif').transition().delay(50000).duration(NotifDuration)
    .style('opacity','0')
    .on('end',function (){
      newDiv.remove();
    });
  
  }
  