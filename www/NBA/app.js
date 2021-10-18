const MatchData = {Season : 2021}
var DomTeamPlayers = {}
var VisTeamPlayers = {}


function IsValidMatch(){

    if(  MatchData['Dom']!=undefined &&  MatchData['Vis']!=undefined &&  MatchData['Dom'] != MatchData['Vis']  ){
        return true;
    }
    else{
        return false;
    }

}

function UpdatePredictButton(){

    if( IsValidMatch() ){
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
    UpdatePredictButton()
    ResetPredictions()
    UpdateTeamInfo()

}


async function UpdateTeamInfo(){
    
    let ResponseData = await fetch('./TeamInfo.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({TeamDom: MatchData['Dom'], TeamVis : MatchData['Vis'],Season :  MatchData['Season']}),
    })
    .then(response => response.json())

    
    DomTeamPlayers=JSON.parse(ResponseData)['Dom']
    VisTeamPlayers=JSON.parse(ResponseData)['Vis']

    console.log(DomTeamPlayers)
    console.log(VisTeamPlayers)
    
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


    document.getElementById('BarA').style.width = Data['Dom']
    document.getElementById('BarB').style.width = Data['Vis']

    document.getElementById('TextBarA').innerHTML =  Data['Dom']
    document.getElementById('TextBarB').innerHTML =  Data['Vis']

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


async function GetPrediction(DomCode,VisCode){

    if(!IsValidMatch() ){
        console.error('Not valid match to fetch predictions')
        // arclassList.add('Active');

        return
    }
    else
        console.log('Valid match to fetch predictions')



    const Prediction = await fetch('./Predict.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({Dom: DomCode, Vis : VisCode}),
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

    let Prediction =  GetPrediction(MatchData['Dom'],MatchData['Vis'])

});