
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//   https://observablehq.com/@d3/selection-join

//   https://www.d3-graph-gallery.com/graph/line_color_gradient_svg.html
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!






class Graph{

  constructor(element,name,svgwidth=0,svgheight=0) {
    this.IdDomElement = element;
    this.Name = name;
    this.DrawGradient = false; 
    this.DoTransition = true; 
    this.KeysAreX = true;


    this.AxisMinMarge = 0.1 ; //percent 
    this.AxisMaxMarge = 0.1 ; //percent 
    this.ExpandedAxises = false;

    this.FixedAxis = [];

    this.FilledLine = false;

    this.DrawAxises = true;
    this.AxisDrawn = false;
    this.ShrinkAxisLabelNumber={X:false,Y:true} 

    this.DataSet = []
    this.points = []
    this.SavedPoints = []

    // this.Npoints=0;
    this.ToSortPointOnX = false;

    this.CreateSvg(svgwidth,svgheight);

  }

  SortPointOnX(bool){
    this.ToSortPointOnX = bool;

  }

  DataKeysAreX(bool){
    this.KeysAreX = bool;
  }

  setPoints(points,name=undefined){


    if(name==undefined){
      name=this.points.length
    }

    this.points[name] = points;
    // console.log(this.points[name]);


    if(this.points[name] === null){

      console.Error('Graph :  setPoints received null. Emptya data');

      this.points[name] = [];

    }

    if(typeof this.points[name] === 'object'){
      //transform Object into array 

      if(this.points[name].values[0] > 2)
          this.points[name] = Object.entries(this.points[name]).map(([key, value, err]) => ([parseFloat(key),parseFloat(value),parseFloat(err)]));
      else    
          this.points[name] = Object.entries(this.points[name]).map(([key, value]) => ([parseFloat(key),parseFloat(value)]));

      this.KeysAreX = false;

    }

    this.Npoints = this.points[name].length;

    this.SavedPoints[name] = JSON.parse(JSON.stringify(this.points[name])); //deep copy no reference ; maybe slow? 

    if(this.KeysAreX){

      this.points[name].forEach((d,i)=>{

        let point = [parseFloat(i),d]
        this.points[name][i] = point;

      })

    }

    if(!this.ScaleDefined){
      
      this.max = d3.max(this.points[name], function (d) {
        return d[1];
      });
      this.min = d3.min(this.points[name], function (d) {
        return d[1];
      });
      
      this.xmin = d3.min(this.points[name], function (d) {
        return d[0];
      });
      
      this.xmax = d3.max(this.points[name], function (d) {
        return d[0];
     });


     this.CreateAxises();
     this.ScaleDefined = true
    }



    if(this.ToSortPointOnX){

      this.points[name].sort((a,b) =>  a[0]-b[0]);

    }


    if(this.FilledLine){
      this.points[name].push([this.points[name][this.points[name].length - 1][0],this.min])
      this.points[name].push([this.xmin,this.min])
      this.points[name].push([this.points[name][0][0],this.points[name][0][1]])
    }


    this.points[name] = this.scalePoints(this.points[name])

  }

  scalePoints(Points){

    Points.forEach((d)=>{

      d[1] = this.yscale(d[1]);
      d[0] = this.xscale(d[0]);
    })
    return Points
  }

  setType(type){
      //line , bar, points
    this.type = type;
  }

  setPointShape(shape){
      //only for nuage de point
    this.pointshape = shape;
  }

  ShrinkAxisLabel(axis='Y',bool=true){

    this.ShrinkAxisLabelNumber[axis]=bool;

  }

  setTransition(duration){
    this.DoTransition = true;

    this.transition = this.svg.transition()
      .duration(duration);
  }

  ExpandAxises(bool=true){ //Before SetPoints
    this.ExpandedAxises = bool;
  }

  FillLine(bool=true){//Before SetPoints
    this.FilledLine = bool;
  }

  FixAxis(AxisName='X',axismin=0,axismax=1){

    this.FixedAxis[AxisName] = [axismin,axismax]; 

  }

  CreateAxises(){

    //Expand axis by a fraction to avoid line overlap axises

    let deltax_min = 0;
    let deltax_max = 0;
    let deltay_min = 0;
    let deltay_max = 0;

    if(this.ExpandedAxises){

      deltax_min = this.xmin*this.AxisMinMarge < this.AxisMinMarge ? Math.abs(this.xmin-this.xmax)* this.AxisMinMarge : this.xmin*this.AxisMinMarge ;
      deltax_max = this.xmax*this.AxisMaxMarge < this.AxisMaxMarge ? Math.abs(this.xmin-this.xmax)* this.AxisMaxMarge : this.xmax*this.AxisMaxMarge ;
      deltay_min = this.xmin*this.AxisMinMarge < this.AxisMinMarge ? Math.abs(this.xmin-this.xmax)* this.AxisMinMarge : this.xmin*this.AxisMinMarge ;
      deltay_max = this.xmax*this.AxisMaxMarge < this.AxisMaxMarge ? Math.abs(this.xmin-this.xmax)* this.AxisMaxMarge : this.xmax*this.AxisMaxMarge ;
    }

    let minx = this.xmin - deltax_min ;
    let maxx = this.xmax + deltax_max ;
    let miny = this.min - deltay_min ;
    let maxy = this.max + deltay_max ;

    if(this.FixedAxis['X'] !== undefined){
      minx = this.FixedAxis['X'][0];
      maxx = this.FixedAxis['X'][1];
    }

    if(this.FixedAxis['Y'] !== undefined){
      miny = this.FixedAxis['Y'][0];
      maxy = this.FixedAxis['Y'][1];
    }

    this.xscale = d3.scaleLinear()
    .domain([minx,maxx])  
    .range([0, this.width]);

    this.yscale = d3.scaleLinear()
    .domain([miny,maxy])  
    .range([this.height, 0]);

  }


  CreateSvg(svgwidth=0,svgheight=0){


    this.margin = {top: 10, right: 10, bottom: 20, left: 50};
    this.width = svgwidth - this.margin.left - this.margin.right;
    this.height = svgheight - this.margin.top - this.margin.bottom;

    if(svgwidth==0){
      this.width = parseInt(d3.select(this.IdDomElement).style('width')) - this.margin.left - this.margin.right;
    }

    if(svgheight==0){
      this.height = parseInt(d3.select(this.IdDomElement).style('height')) - this.margin.top - this.margin.bottom;
    }

    this.svg =  d3.select(this.IdDomElement).append("svg").attr('id', this.Name)
    .attr("width", this.width + this.margin.left + this.margin.right)
    .attr("height", this.height + this.margin.top + this.margin.bottom)
    .append("g")
    .attr("transform", "translate(" + this.margin.left + "," + this.margin.top + ")");

  }

  //Points =  [ [x,y] , [x,y] ...] 
  DrawOtherLine(Points,name=undefined){
    if(Points==undefined)
      return
     
    this.setPoints(Points,name)
    // return this.DrawLine(undefined,name)
    return this.DrawPoints(undefined,name)
    
  }


  /**
   * 
   * @param {array} Points   [ [x,y] , [x,y] ...]  
   * @param {string} name 
   * @param {string} type 
   * @param {array} params  
   */
  DrawDataSet(Points,name='tt',type="Points",params=undefined){

    if(Points==undefined){
      console.error('Dataset is empty',Points) 
      return
    }

    this.setPoints(Points,name)

    if(name==undefined){
      name=this.points.length-1
    }

    let drawnobject = undefined

    if(type=='Points'){
      drawnobject= this.DrawPoints(name,params)

    }
    else if(type=='Histo'){

    }
    else if(type=="Line"){
      drawnobject= this.DrawLine(name,params)

    }
    else{
      console.error('unknown type to draw graph :',type) 
    }

    return drawnobject 

  }

  /**
   * 
   * @param {*} name 
   * @param {*} params  'color', 'width', 'linecap' , "DrawGradient" => 'GradientName', 'FilledLine', 'FilledLine','DrawAxises'
   * @returns 
   */
  DrawLine(name,params=undefined){

    if(params == undefined){
      params=[]
    }

    let color = ('color' in params) ? params['color'] :  "currentColor"
    let width = ('width' in params) ? params['width'] :  3
    let linecap = ('linecap' in params) ? params['linecap'] :  "round"
    let DrawGradient = ('DrawGradient' in params) ? params['DrawGradient'] :  false
    let GradientName = ('GradientName' in params) ? params['GradientName'] :  ""
    let FilledLine = ('FilledLine' in params) ? params['FilledLine'] :  false
    let DrawAxises = ('DrawAxises' in params) ? params['DrawAxises'] :  true

    let lineName = this.Name+'-'+name

    this.DataSet[name] = this.svg.append("path").attr('id','Line'+lineName)
    .datum(this.points[name])
    .attr("fill", "none")
    // .attr("stroke", "url(#line-gradient)" )
    .attr("stroke", color)
    .attr("stroke-width", width)
    .attr("stroke-linecap",linecap)
    .attr("d", d3.line()
      .x(function(d,i) { return d[0]; })
      .y(function(d,i) { return d[1]; })
      );


    if(DrawGradient)  
       this.DataSet[name]
      // this.svg.select('#Line'+this.Name)
    .attr("stroke", "url(#"+GradientName+")" )

    if(FilledLine){
      // this.svg.select('#Line'+this.Name)
      this.DataSet[name]
      .attr("fill", function(){
        if(DrawGradient)  
          return "url(#"+GradientName+")" 
        else
          return color;
        })
    }


    if(DrawAxises && !this.AxisDrawn){
        this.DrawCustomAxises();
    }

    return this.DataSet[name] // this.svg.select('#Line'+this.Name);

  }

    /**
   * 
   * @param {*} name 
   * @param {*} params  'color','strokecolor' 'linecap' 'radius' 'strokewidth', "DrawGradient" => 'GradientName', 'FilledLine', 'FilledLine','DrawAxises'
   * @returns 
   * 
   * stroke and linecap is for error bars
   */
  DrawPoints(name=undefined,params=undefined){

    if(params == undefined){
      params=[]
    }

    let color = ('color' in params) ? params['color'] :  "currentColor"
    let strokecolor = ('strokecolor' in params) ? params['strokecolor'] : color
    let strokewidth = ('width' in params) ? params['width'] :  1
    let radius = ('radius' in params) ? params['radius'] :  2
    let linecap = ('linecap' in params) ? params['linecap'] :  "round"

    // let DrawGradient = ('DrawGradient' in params) ? params['DrawGradient'] :  false
    // let GradientName = ('GradientName' in params) ? params['GradientName'] :  ""
    // let FilledLine = ('FilledLine' in params) ? params['FilledLine'] :  false
    // let DrawAxises = ('DrawAxises' in params) ? params['DrawAxises'] :  true

    let DrawErrors = ('DrawErrors' in params) ? params['DrawErrors'] :  false


    let DataSetName = this.Name+'-'+name

    this.DataSet[name] = 
    this.svg.selectAll('circle')
    .data(this.points[name])
    .enter()
    .append('circle')
    .attr('id',function(d,i){return DataSetName+'-'+i})
    .attr("fill", color)
    // .attr("stroke", "url(#line-gradient)" )
    // .attr("stroke", strokecolor)
    .attr("stroke-width", 0)   // stroke is for error bar !!
    .attr('cx',function(d){return d[0]})
    .attr("cy",function(d){return d[1]})
    .attr('r',radius)
    .attr('value',function(d){return d[1]/this.ymax})


    if(DrawErrors){

      this.DataSet['Error_'+name] = this.svg.selectAll('line')
      .data(this.points[name])
      .enter()
      .append("line")
      .attr('id',function(d,i){return DataSetName+'-Error-'+i})
      .attr("x1", function(d,i) { return d[0]; })
      .attr("y1", function(d,i) { return d[1]+(d[2] ?? 0); })
      .attr("x2", function(d,i) { return d[0]; })
      .attr("y2", function(d,i) { return d[1]-(d[2] ?? 0); })
      .attr("stroke", strokecolor)
      .attr("stroke-width", strokewidth)
      .attr("stroke-linecap",linecap)

      return [this.DataSet[name] ,this.DataSet['Error_'+name]]

    }

    return this.DataSet[name] // this.svg.select('#Line'+this.Name);


  }

  DrawHisto(Points=undefined,name=undefined){

    if(name==undefined){
      name=this.points.length-1
    }


    let barwidth = Math.abs(this.xmax-this.xmin) / (this.points[name].length-1);
    
    this.svg.selectAll('rect')
    // .data(this.points[name])
    .datum(this.points[name])
    // .enter()
    .append("rect")
    .attr('id','HistoBar'+this.Name)
    .attr("fill", "currentColor")
    // .attr("stroke", "url(#line-gradient)" )
    .attr("x",function(d,i){
        return this.xscale((d-(barwidth/2)))
    })
    .attr("y",function(d){
      return d[1] ;
    })
    .attr("width",this.xscale(barwidth))
    .attr("height",function(d,i){
      return Math.abs(d[1]-this.yscale(this.min)) ;
    })
    .attr('fill',function(d,i){
      if(this.DrawGradient)  
        return this.cp.getColor(this.SavedPoints[name][i][1])
      else
        return 'currentColor';  
    })
    .attr('stroke-width',0)


    if(this.DrawAxises && !this.AxisDrawn){
      this.DrawCustomAxises();
    }

  }


  CustomAxisLabel(x,Type='Shrink'){
    if(Type=='Shrink'){

      if(x>=1e12){
        x = x / 1e9;
        return ToolsJS.FormatNumber_SeparatorForThousands(x.toFixed(0),' ')+' MM'; 
      }
      else if(x>=1e9){
        x = x / 1e9;
        return x.toFixed(0)+' MM'; 
      }
      else if(x>=1e6){
        x = x / 1e6;
        return x.toFixed(0)+' M'; 
      }
      else
        return ToolsJS.FormatNumber_SeparatorForThousands(x.toFixed(0),' ')
    }
    else 
      return ToolsJS.FormatNumber_SeparatorForThousands(x.toFixed(0),' ');
     
  }

  DrawCustomAxises(){


    if(this.AxisDrawn)
      return;

    let xaxis = d3.axisBottom(this.xscale)

    let yaxis = d3.axisLeft(this.yscale).ticks(3)
    // .tickFormat(x => x.toFixed(1));

    if(this.ShrinkAxisLabelNumber['Y'])
      yaxis.tickFormat(x => this.CustomAxisLabel(x,'Shrink'));

    if(this.ShrinkAxisLabelNumber['X'])
      xaxis.tickFormat(x => this.CustomAxisLabel(x,'Shrink'));


    this.xAxis = this.svg.append("g")
    .attr("class", "Xaxis")
    .attr('transform','translate(0,'+this.height+')') 
    .call(xaxis)
    
    this.yAxis = this.svg.append("g")
    .attr("class", "Yaxis")
    .call(yaxis)

    this.yAxis.attr('stroke','yellow')
    d3.select(".domain").attr('stroke','red')

  }


  //todo redo
  DrawCircle(r){

    let svg = d3.select('#'+this.Name);
    console.log('uu');
    console.log(svg);
    svg    // .select('#'+this.Name)
    .selectAll('circle')
    .data(this.points)
    .join(
        enter => enter.append("circle")
            .attr("fill", "green")
            .attr("stroke", "currentColor")
            .attr("stroke-width", 3)
            .attr('cx',function(d,i) { return d[0]; })
            .attr('cy',0)
            .attr('r',r)
            .call(enter => enter.transition(this.t)
              .attr("y", function(d,i) { return d[1]; })),
        // update => update
            // .attr("fill", "yellow")
            // .call(update => update.transition(this.t)
            //   .attr("stroke", 'red')),
      )
    


    if(this.DoTransition){

        svg.selectAll('circle')
        .transition().delay(250)
        .ease(d3.easeCubic)
        .duration(1500)   
        .attr("cy", function(d,i) { return d[1]; })
        // .attr("fill", "green");
    }
    else{
        svg.selectAll('circle')
        .attr("cy", function(d,i) { return d[1]; });
    }

    if(this.DrawGradient)  
    this.svg.selectAll("circle")
  //   .attr("stroke", "url(#line-gradient)" )
    .attr("fill", "url(#"+this.GradientName+")" )




    let cp = new ColorPicker_WeatherTemperature();
    cp.ReverseDomain();


    console.log(cp);

    //test color
    this.svg.append("circle")
    .attr('cx',400)
    .attr('cy',400)
    .attr('r',r)
    .attr("fill",function(d){ return cp.getColor(80)})
    // .attr("fill", "url(#"+this.GradientName+")" )
    // .attr('cx',335)
    // .attr('cy',100)
    .transition().delay(1250)
    .attr('cy',110)

    this.svg.append("circle")
    .attr('cx',450)
    .attr('cy',450)
    .attr('r',r)
    .attr("fill",function(d){ return cp.getColor(150)})
    // .attr("fill", "url(#"+this.GradientName+")" )
    // .attr('cx',335)
    // .attr('cy',100)
    .transition().delay(1250)
    .attr('cy',110)

    this.svg.append("circle")
    .attr('cx',80)
    .attr('cy',110)
    .attr('r',r)
    .attr("fill",function(d){ return cp.getColor(0)})
    // .attr("fill", "url(#"+this.GradientName+")" )
    // .attr('cx',335)
    // .attr('cy',100)
    .transition().delay(1250)
    .attr('cy',400)

    this.svg.append("circle")
    .attr('cx',50)
    .attr('cy',110)
    .attr('r',r)
    .attr("fill",function(d){ return cp.getColor(-10)})
    // .attr("fill", "url(#"+this.GradientName+")" )
    // .attr('cx',335)
    // .attr('cy',100)
    .transition().delay(1250)
    .attr('cy',400)


    this.svg.append("circle")
    .attr('cx',100)
    .attr('cy',110)
    .attr('r',r)
    .attr("fill",function(d){ return cp.getColor(20)})
    // .attr("fill", "url(#"+this.GradientName+")" )
    // .attr('cx',335)
    // .attr('cy',100)
    .transition().delay(1250)
    .attr('cy',400)


    this.svg.append("circle")
    .attr('cx',200)
    .attr('cy',110)
    .attr('r',r)
    .attr("fill",function(d){ return cp.getColor(30)})
    // .attr("fill", "url(#"+this.GradientName+")" )
    // .attr('cx',335)
    // .attr('cy',100)
    .transition().delay(1250)
    .attr('cy',400)


    // cp.DrawColorBar(this.svg,100,100,50,200,"i")
    // cp.DrawColorBar(this.svg,300,300,50,100,"ii")
    // cp.DrawColorBar(this.svg,150,300,50,100,"ii")
  }

  AddTemperatureGradient(){

    this.cp = new ColorPicker_WeatherTemperature();
    this.cp.ReverseDomain();
    this.AddGradient();

  }

  AddHumidityGradient(){

    this.cp = new ColorPicker_Humidity();
    this.cp.ReverseDomain();
    this.AddGradient();

  }

  AddGradient(){

    this.GradientName = 'GlobalGradient'+this.Name;

    this.cp.AddColorGradient(this.svg,0,this.yscale(this.cp.Domain[0]),0,this.yscale(this.cp.Domain[this.cp.Domain.length -1]),this.GradientName)

    this.DrawGradient = true;  
  }


  DrawColorBar(x1,x2,y1,y2){

    if(y2<y1){
      let temp = y2;
      y2 = y1;
      y1=temp;
    }

    this.cp.DrawColorBar(this.svg,
      this.xscale(x1),
      this.yscale(y1),
      this.xscale(x2),
      this.yscale(y2),'ColorBar'+this.Name)
  }
}




class DrawGraph{


  //StaticFunction 
  //==========================

  static CreateStaticFunctionGraph(DomIdElement, Name,data,svgwidth=0,svgheight=0){
    let G = new Graph('#'+DomIdElement,Name,svgwidth,svgheight);
    G.DataKeysAreX(true);
    G.ShrinkAxisLabel('X',false);
    G.ShrinkAxisLabel('Y',false);
    G.SortPointOnX(true);
     G.FixAxis('Y',0,1);
    G.ExpandAxises(false);
    G.FillLine(false);
    G.setTransition(false);
    // G.setPoints(data);

    console.log(G);
    return G;
  }


  static DrawStaticFunctionGraph(DomIdElement,Name='',data=[],svgwidth=0,svgheight=0){

    let G = this.CreateStaticFunctionGraph(DomIdElement,Name,data,svgwidth,svgheight);

    // G.AddTemperatureGradient();
    // G.DrawColorBar(20,40,-15,50); //x1,x2,y1,y2

    let line = G.DrawDataSet(data,'tt','Line',{'color':'red'});
    // line.attr('stroke-width',5);

    return G

  }

  //Climate
  //=========

  static CreateClimateGraph(DomIdElement, Name,Type,data,svgwidth=0,svgheight=0){
    let G = new Graph('#'+DomIdElement,Name+Type,svgwidth,svgheight);
    G.DataKeysAreX(true);
    G.ExpandAxises(false);
    G.FillLine(false);
    G.setTransition(false);
    G.setPoints(data);
    
    return G;
  }


  static DrawClimateTemperatureGraph(DomIdElement,ClimateRegionDataEvolution=[], Type='Temperature',svgwidth,svgheight){

    let G = this.CreateClimateGraph(DomIdElement,'ClimateGraph',Type,ClimateRegionDataEvolution,svgwidth,svgheight);

    G.AddTemperatureGradient();
    // G.DrawColorBar(20,40,-15,50); //x1,x2,y1,y2

    G.DrawHisto();
    let line = G.DrawLine();
    line.attr('stroke-width',1);

  }

  static DrawClimateHumidityGraph(DomIdElement,ClimateRegionDataEvolution=[], Type='Humidity',svgwidth,svgheight){

    let G = this.CreateClimateGraph(DomIdElement,'ClimateGraph',Type,ClimateRegionDataEvolution,svgwidth,svgheight);

    G.AddHumidityGradient();
    // G.DrawColorBar(20,40,G.min*1.1,G.min*1.1+0.5); //x1,x2,y1,y2


    G.DrawHisto();
    let line = G.DrawLine();
    line.attr('stroke-width',1);

  }

  static DrawClimateEnlightmentGraph(DomIdElement,ClimateRegionDataEvolution=[], Type='Enlightment',svgwidth,svgheight){

    let G = this.CreateClimateGraph(DomIdElement,'ClimateGraph',Type,ClimateRegionDataEvolution,svgwidth,svgheight);

    // let line = G.DrawLine();
    // line.attr('stroke-width',10);
    G.DrawHisto();
  }


  //Population
  //=========

  static CreatePopulationGraph(DomIdElement,data=[],svgwidth=0,svgheight=0){

    let G = new Graph('#'+DomIdElement,'PopulationAges',svgwidth,svgheight);
    G.DataKeysAreX(true);
    G.ExpandAxises(false);
    G.FillLine(false);
    G.setTransition(false);
    G.setPoints(data);
    
    // G.AddHumidityGradient();
    // G.DrawColorBar(20,40,G.min*1.1,G.min*1.1+0.5); //x1,x2,y1,y2

    return G;
  }

  static DrawPopulationGraph(DomIdElement,data=[],svgwidth=0,svgheight=0,Type='Histo'){

    let G = this.CreatePopulationGraph(DomIdElement,data,svgwidth,svgheight);

    if(Type==='Histo'){
      G.DrawHisto();
    }
    else if(Type=='Line'){

      let line = G.DrawLine();
      line.attr('stroke-width',3);
    }



  }

}




