
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

//   constructor(element,name,svgwidth=0,svgheight=0) {
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


    this.style = document.createElement('style')
    this.style.innerHTML = `
      :host : {
        display : bloc;
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
    
    `


    shadow.appendChild(svg)


    // this.IdDomElement = element;
    // this.Name = name;
    // this.DrawGradient = false; 
    // this.DoTransition = true; 
    // this.KeysAreX = true;


    // this.AxisMinMarge = 0.1 ; //percent 
    // this.AxisMaxMarge = 0.1 ; //percent 
    // this.ExpandedAxises = false;

    // this.FixedAxis = [];

    // this.FilledLine = false;

    // this.DrawAxises = true;
    // this.AxisDrawn = false;
    // this.ShrinkAxisLabelNumber={X:false,Y:true} 

    // this.Npoints=0;
    // this.ToSortPointOnX = false;

    // this.CreateSvg(svgwidth,svgheight);

  }

  //only when element is connected to dom
  connectedCallback(){
      this.draw()
  }
  

  GetSeries(){
    this.Series = this.getAttribute('data').split(',')
    this.Series.forEach((s)=>{
        s = s.split(';').map(v=>parseFloat(v))
    })
  }

  draw(){

    let start = new Point(1,0)
    let end  = new Point(2,3)
    this.paths[0].setAttribute('d',`M 0 0 L ${start.toSVGPath()} ${end.toSVGPath()} 3 4`)

  }

}




class LineGraph extends Graph {

}

class BarGraph extends Graph{

    
  constructor() {

    super()
  }


}

class PieGraph extends Graph{

    
    constructor() {
  
      super()
    }
  
  
}




