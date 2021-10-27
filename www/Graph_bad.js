
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
    
    this.svg = strToDom(`<svg class="StdGraph" viewBox ="-1 -1 2 2"></svg>`)

    this.paths = this.Series.map( (_,k)=>{
        const color = '#f4a261'
        const path = document.createElementNS('http://www.w3.org/2000/svg','path')
        path.setAttribute('fill',color)
        this.svg.appendChild(path)
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
    shadow.appendChild(this.svg)


  }

  //only when element is connected to dom
  connectedCallback(){
      this.draw()
  }
  
  GetSeries(){
    let DataKeys = this.getAttribute('data').split(';')
    console.log('e')
    console.log(DataKeys)

    this.Bins =[]
    this.Series = []

    DataKeys.forEach((k,i)=>{
      k = k.split('-')
      this.Bins[i] = Data[k[0]]['Bins']
      this.Series[i] = Data[k[0]][k[1]]
        // .map(v=>parseFloat(v))
    })
  }


  draw(){

    for(let ip=0 ; ip<this.Bins.length ; ip++){

      let start = new Point(this.Bins[ip][0],this.Series[ip][0])
      let path = `M ${start.toSVGPath()} L `
      for(let i=1 ; i<this.Series[ip].length ; i++){
        let next = new Point(this.Bins[ip][i],this.Series[ip][i])
        path += `${next.toSVGPath()} `
      }

      console.log('ppp')
      console.log(path)
      this.paths[ip].setAttribute('d',path)
    }

  }

}


class GraphWithAxises extends Graph{

  constructor() {

    super()
    this.GetAxises()
    this.Naxises = 2


  }

  GetAxises(){
    this.AxisRange  = this.getAttribute('axis')?.split(',') ?? ['0;1','0;1']
    this.AxisRange.forEach((s,i)=>{
      this.AxisRange[i] = s.split(';').map(v=>parseFloat(v))
    })

    for(let ia = 0 ; ia < this.Naxises; ia++){

      this.AddAxis()
    }

    const color = '#001219'
    this.AxisPaths[0] = document.createElementNS('http://www.w3.org/2000/svg','path')
    this.AxisPaths[0].setAttribute('stroke',color)
    this.AxisPaths[0].setAttribute('stroke',color)
    this.svg.appendChild(this.AxisPaths[0])
  })

  }

  AddAxis(){
    const color = '#001219'
    this.AxisPaths[0] = document.createElementNS('http://www.w3.org/2000/svg','path')
    this.AxisPaths[0].setAttribute('stroke',color)
    this.AxisPaths[0].setAttribute('stroke',color)
    this.svg.appendChild(this.AxisPaths[0])

    t
  }

  draw(){
    super.draw()
    console.log(this.AxisPaths)
    console.log(this.AxisRange)
    
    //X axis
    let start = new Point( this.AxisRange[0][0], this.AxisRange[0][0])
    let path = `M ${start.toSVGPath()} L `
    let next = new Point( this.AxisRange[0][0], this.AxisRange[0][1])
    path += `${next.toSVGPath()} `
    console.log('ppp')
    console.log(path)
    this.AxisPaths[0].setAttribute('d',path)
    
    //Y axis
    start = new Point( this.AxisRange[1][0], this.AxisRange[1][0])
    path = `M ${start.toSVGPath()} L `
    next = new Point( this.AxisRange[1][1], this.AxisRange[1][0])
    path += `${next.toSVGPath()} `
    console.log('ppp')
    console.log(path)
    this.AxisPaths[1].setAttribute('d',path)

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


        //CustomElements
        // customElements.define('graph-std',GraphWithAxises)




