
function strToDom(str){
    return document.createRange().createContextualFragment(str).firstChild
}

class Point{

    constructor(x,y){
        this.x = x
        this.y = y
    }

    toSVGPath(){
        return `${this.x} ${this.y}`
    }
}


class Graph extends HTMLElement{


  constructor() {

    super()

    const shadow = this.attachShadow({mode : 'open'})

    this.GetSeries()
    
    const svg = strToDom(`<svg class="StdGraph" viewBox ="-1 -1 2 2"></svg>`)

    this.paths = this.Series.map( (_,k)=>{
        const color = '#f4a261'
        const path = document.createElementNS('http://www.w3.org/2000/svg','path')
        path.setAttribute('fill',color)
        svg.appendChild(path)
        return path
    })

    const style = document.createElement('style')
    style.innerHTML = `
      :host : {
        display : block;
        position : relative;
      }

      svg :{
        width : 100%;
        height: 100%;
      }
      path :{
        cursor : pointer;
        transition : opacity .3s;
      }
      path:hover{
        opacity 0.5;
      }

      .StdGraph{
        width : 100%;
        height: 100%; 
      }
    
    `


    shadow.appendChild(style)
    shadow.appendChild(svg)


  }

  //only when element is connected to dom
  connectedCallback(){
      this.draw()
  }
  
  GetSeries(){
    this.Series = Data['DetailedResults']
    // this.getAttribute('data').split(',')
    this.Series.forEach((s)=>{
        s = s.split(';').map(v=>parseFloat(v))
    })
  }


  draw(){

    let start = new Point(this.Series[0].x,this.Series[0].y)

    for(let i=0 ; i<this.Series.length ; i++){
      let start = new Point(this.Series[i].x,this.Series[i].y)
      this.paths[i].setAttribute('d',`M 0 0 L ${start.toSVGPath()} ${end.toSVGPath()}`)
    }

  }

}


class GraphWithAxises extends Graph{

  constructor() {

    super()
    this.GetAxises()


  }

  GetAxises(){
    this.AxisRange  = this.getAttribute('axis')?.split(',') ?? ['0;1','0;1']
    this.AxisRange.forEach((s)=>{
      s = s.split(';').map(v=>parseFloat(v))
    })
  }


}




class LineGraph extends GraphWithAxises {

}

class BarGraph extends GraphWithAxises{

    
  constructor() {

    super()
  }


}

class PieGraph extends Graph{

    
    constructor() {
  
      super()
    }
  
  
}




