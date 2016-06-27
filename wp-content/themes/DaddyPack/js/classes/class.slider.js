var clrzSlider = new Class({
	Implements: [Options,Events],
	options:{ 
		perpage:1,
		setpage:0,
		containerwidth:0,
		container:'slide_container',
		items:'.slideshow #box2 span p',
		contentslider:'contentslider',
		btnnext:'div.slideshow .buttons #next1',
		btnprev:'div.slideshow .buttons #prev1',
		layout:'horizontal',
		windowscroll:1,
		autostart:false,
		duration:600,
		bordertransition:'back:in',
		marge:0,
		srolling:false
		},
	
	
	initialize: function(options)
	{
		
		this.setOptions(options);
		this.wscroll = new Fx.Scroll(window);
		if(!$(this.options.container))
			return;
	
		if(this.options.layout=='horizontal')
		{
			this.prop = 'width';
			this.moove = 'margin-left';
		}
		else
		{
			this.prop = 'height';
			this.moove = 'margin-top';
		}
		
		
		this.ulwidth = 0;
		this.items = $$(this.options.items);
		this.container = $(this.options.container);
		this.contentslider = $(this.options.contentslider);
		
		
		this.is_under = 0;	
		this.ulid = [];	
		var maclass = this;
		this.contentslider.set('morph',{duration:this.options.duration,transition: Fx.Transitions.Quad.easeOut,wait:false,onComplete:function(e){  maclass.setDefaultFx(); }});
		/*this.fxlist = new Fx.Morph(this.contentslider,{duration:this.options.duration,transition: Fx.Transitions.Quint.easeInOut,wait:false});*/
		
		
		var maclass = this;	
		
		
		this.items.each(function(el,i)
		{
			if(maclass.options.layout=='horizontal')
				maclass.ulwidth = maclass.ulwidth+el.getWidth()+maclass.options.marge;
			else
				maclass.ulwidth = maclass.ulwidth+el.getHeight()+maclass.options.marge;
				
				
			maclass.ulid[i]=el.getProperty('id');
			
			
		
		});	
		
		this.itemwidth = (this.ulwidth/this.items.length);
		var containerW;
			if(this.options.containerwidth)
				containerW=this.options.containerwidth;
			else
				containerW=this.itemwidth*this.options.perpage;
				
		var obj	= {};
		obj[this.prop] = containerW;
		obj['overflow']='hidden';
		
		this.container.setStyles(obj);
		this.containerwidth = (this.itemwidth*this.options.perpage);
		this.contentslider.setStyle(this.prop,this.containerwidth);
		this.maxpage = Math.ceil(this.items.length/this.options.perpage)-1;
		
		
		
		/*console.log(this.maxpage);
		console.log(this.items.length);
		console.log(this.options.perpage);
		console.log('-----------');*/
		this.contentslider.setStyle(this.prop,(this.ulwidth));	
		this.initevents();
		
		if(this.maxpage<1)
                    {
                        $$(this.options.btnnext).hide();
                        $$(this.options.btnprev).hide();
                    }
		this.fireEvent('initialize');


		this.zgoto(this.options.setpage);
		if(this.options.autostart)
		this.start();
		 	
		
	},
	
	setDefaultFx:function()
	{
	var maclass = this;	
        this.contentslider.get('morph').removeEvents('complete');
	this.contentslider.set('morph',{transition: Fx.Transitions.Quad.easeOut,wait:false,duration:maclass.options.duration,onComplete:function(e){   maclass.fireEvent('zgotocomplete'); }});	
		
	},
	
	initevents:function()
	{
		
		var maclass = this;
		this.container.addEvents({mouseenter:function(){maclass.is_under=1},mouseleave:function(){maclass.is_under=0;},mousewheel:function(event){ if(!maclass.options.srolling) return; event.stop(); if(event.wheel<0) maclass.scrollmenext(); else maclass.scrollmeprev(); }});	
		
		$$(this.options.btnnext).addEvent('click',function(e){
		
		new Event(e).stop();
		
		
		maclass.scrollmenext();
		});
		
		$$(this.options.btnprev).addEvent('click',function(e){
		
		new Event(e).stop();
		
		
		maclass.scrollmeprev();
		});	
		
		
		
	},
	
	scrollmenext:function()
	{
		var maclass = this;
                
		if(this.setpage>=this.maxpage){
                    
                        
			this.contentslider.set('morph',{transition: maclass.options.bordertransition,wait:false,duration:(maclass.options.duration*1.3)});	

			this.setpage=0;
			this.zgoto();
		}else{
                   
			this.setpage=this.setpage+1;
			this.zgoto();
		}
		
		if(this.windowscroll==1)
			this.wscroll.toElement(this.container);	
		
	},	
	
	scrollmeprev:function()
	{
		var maclass = this;
		if(this.setpage<=0){
			this.setpage=this.maxpage;
                        
			this.contentslider.set('morph',{transition: maclass.options.bordertransition,wait:false,duration:(maclass.options.duration*1.3)});	
			this.zgoto();
		

		}else{
			
			this.setpage=this.setpage-1;
			this.zgoto();
		}
		if(this.windowscroll==1)
			this.wscroll.toElement(this.container);	
		
	},
	
	zgoto:function(page)
	{
		
		var maclass = this;
		if($chk(page))
		this.setpage = page;
		var obj = {};	
		
		obj[this.moove] =-(this.setpage*this.containerwidth);
		
		this.contentslider.morph(obj);
		this.fireEvent('zgoto');
		
	
	},
	

	
	
	
	
	loadactivepage:function(item)
	{
		$(item).addClass('current');
		 this.setpage =  Math.floor(ulid.indexOf(item)/perpage);
		
		this.zgoto();
	},

	
	
	
	autostart:function()
	{
		
		if(!this.is_under)
			this.scrollmenext();
		
	},
	
	start:function()
	{

                if(this.autostart)
                    this.autostart.periodical(5000,this);
		
		
	}
		
		
	
	
});
