function CreateMaterialSelectionOptions(Team='A'){
    let element = document.getElementById('TeamAselection'+Team);
  
    let x = element.length +1;
    for (let i = 0; i <  x ; i++) {
        element.remove(element.length-1);
    }

    let count=0;

    fetch()

    for (code of Object.entries(NBAData['TEAMCodes_Names'])) {

      let opt = document.createElement('option');   
      opt.appendChild( document.createTextNode(NBAData['TEAMCodes_Names'][code]) );
      element.appendChild(opt); 
  
    }


  }


async function LoadData(){
      
      const NBAData = await fetch('/Data.json', {
          headers: {
              'Accept': 'application/json'
          }
      });
}

LoadData();

document.getElementById('TeamAselection').addEventListener('change', (event) => {
    selectTeam();
});