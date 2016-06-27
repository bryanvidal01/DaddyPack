/*

Name : Clrz Slider
Version : 1.0.2


INIT :

new ClrzSliderClass({
	'idname' : 'slider_home', // ID du block contenant les slides
	'clicktonext': 0, // Si présent et égal à 0, un click sur une diapo ne permet plus d'atteindre la suivante. 
	'prevnext':1, // Si présent et égal à 1, une navigation prev next s'active
	'autodiapo': 3000, // Si présent et supérieur à 0, intervalle de l'autoslide
	'height' : 200 // Si présent, hauteur max du slider
});


CHANGELOG :

- 1.0.2 : Ajout du param prevnext
- 1.0.1 : Ajout de prev next


*/

var ClrzSliderClass = new Class({
    initialize: function(options){
        this.options = options;
		if(!$(this.options.idname)){return 0;}
		this.slider = $(this.options.idname);
		this.elements = $$('#'+this.options.idname+' > *');
		if(this.elements.length < 1){return 0;}
		
		this.set_options();
		this.set_elements();
		this.set_styles();
		this.set_diapo(0);
		if(this.options.autodiapo > 0){
			this.launchdiapo();
		}
    },
	set_options : function(){
		this.zindexmax=1;
		this.currentdiapo=-1;
		if(this.options.clicktonext == undefined){
			this.options.clicktonext = 1;
		}
		if(this.options.prevnext == undefined){
			this.options.prevnext = 0;
		}
		if(this.options.autodiapo == undefined || !this.options.autodiapo || this.options.autodiapo <= 50){
			this.options.autodiapo = 0;
		}
		if(!this.options.height){
			this.options.height = this.elements[0].getHeight();
		}
	},
	set_elements : function(){
		var maclass = this;
		
		/* Block Parent */
		this.sliderParent = new Element('div.wrapper_'+this.options.idname);
		this.sliderParent.wraps(this.slider);
		this.slider.addClass('isslider');
		
		/* Pagination bullet */
		this.pagi = new Element('ul');
		this.pagi.addClass('pagination');
		this.pagiel = new Array();
		for (i=0;i<this.elements.length;i++){
			this.pagiel[i] = new Element('li');
			this.pagiel[i].set('html','•');
			this.pagiel[i].setProperty('i',i);
			this.pagiel[i].inject(this.pagi);
			this.pagiel[i].addEvent('click',function(){
				le_i = parseInt(this.getProperty('i'));
				maclass.set_diapo(le_i);
			});
		}
		this.pagi.inject(this.sliderParent);
		
		/* Pagination sur image */
		if(this.options.clicktonext == 1){
			this.elements.each(function(el){
				el.addEvent('click',function(e){
					maclass.set_diapo('next');
				});
			});
		}

		/* Pagination prev next */
		if(this.options.prevnext == 1){
			this.sliderPrevArrow = new Element('span.slider_prev');
			this.sliderNextArrow = new Element('span.slider_next');

			this.sliderPrevArrow.inject(this.sliderParent);
			this.sliderNextArrow.inject(this.sliderParent);

			this.sliderPrevArrow.addEvent('click',function(e){
				maclass.set_diapo('prev');
			});


			this.sliderNextArrow.addEvent('click',function(e){
				maclass.set_diapo('next');
			});

			var stylepagi = {
				'z-index' : '999999',
				'position':'absolute'
			};
			this.sliderPrevArrow.setStyles(stylepagi);
			this.sliderNextArrow.setStyles(stylepagi);
		}

	},
	launchdiapo : function(){
		var maclass = this;
		maclass.autodiapo();
		maclass.slider.addEvent('mouseenter',function(){
			clearTimeout(maclass.timeout);
		});
		maclass.slider.addEvent('mouseleave',function(){
			maclass.autodiapo();
		});
	},
	autodiapo : function(){
		var maclass = this;
		this.timeout = setTimeout(function(){
			maclass.set_diapo('next');
			maclass.autodiapo();
		},this.options.autodiapo);
	},
	/* Styles par défaut */
	set_styles : function(){
		var maclass = this;
		this.sliderParent.setStyles({
			'-moz-user-select': 'none',  
			'-webkit-user-select': 'none',  
			'-ms-user-select': 'none',  
			'position' : 'relative',
			'overflow' : 'hidden',
			'height' : maclass.options.height
		});
		this.elements.setStyles({
			'position' : 'absolute',
			'top' : '0',
			'left' : '0',
			'z-index':'1'
		});
		this.elements.each(function(el){
			el.set('morph', {'duration': 500});
		});
		this.pagi.setStyles({
			'position' : 'absolute',
			'bottom' : '0',
			'right' : '0',
			'color' : '#fff',
			'z-index' : '999999'
		});
	},
	set_diapo : function(id_diapo){
		var maclass = this;
		/* On demande la diapo suivante */
		if(id_diapo == 'next') id_diapo = this.currentdiapo+1;
		
		/* On demande la diapo precedente */
		if(id_diapo == 'prev') id_diapo = this.currentdiapo-1;
		
		/* Si on demande la diapo total+1, on retourne 0 */
		if(id_diapo == this.elements.length) id_diapo = 0;
		
		/* Si on demande la diapo -1, on retourne total */
		if(id_diapo == -1) id_diapo = this.elements.length-1;

		/* Si la diapo est déjà en front, on annule l'action */
		if(id_diapo == this.currentdiapo) return;
				
		this.zindexmax++;
		this.currentdiapo = id_diapo;
		
		el = this.elements[id_diapo];
		this.pagiel.each(function(el){
			el.removeClass('current');
		});
		this.pagiel[id_diapo].addClass('current');
		
		el.setStyles({
			'opacity':0,
			'z-index':maclass.zindexmax
		});
		el.morph({opacity: 1});
	}
});
