const MatchData = {}


function selectTeam(Side='Dom',value){

    MatchData[Side] = value
    console.log(MatchData)

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


function ShowPrediction(Data){


    document.getElementById('LogoA').src = Data['Dom']
    img.src.replace("_t", "_b");

}


async function GetPrediction(){
      
    const Prediction = await fetch('./Predict.php', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    console.log(Prediction)

    ShowPrediction(Prediction)

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
document.getElementById('matchDate').addEventListener('change', (event) => {
    selectTeam("Date",document.getElementById('matchDate').value);
});

document.getElementById('ResultsPrediction').addEventListener('click', (event) => {

    let Prediction =  GetPrediction()

});