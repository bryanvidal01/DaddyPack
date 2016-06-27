var CLRZMachonnerie = new Class({
	initialize : function(opt){
		this.opt = opt;
		this.setOptions();
		this.setElements();
	},
	setOptions : function(){
		if(!this.opt.nbcol || this.opt.nbcol < 2){
			this.opt.nbcol = 2;
		}
	},
	setElements : function(){
		var mthis = this, opt = this.opt;
		// On masque la réserve de briques
		opt.briques.setStyles({
			'position':'absolute',
			'top':'-9999px',
			'left':'-9999px'
		});
		
		// On monte les colonnes sur les fondations
		this.basemur = new Element('ul');
		this.basemur.addClass('clrz_mach_basemur').setStyles({'overflow':'hidden'});
		this.colonnes = [];
		for(i=0;i<this.opt.nbcol;i++){
			this.colonnes[i] = new Element('li');
			this.colonnes[i].addClass('clrz_mach_colonne').setStyles({'float':'left'});
			this.colonnes[i].inject(this.basemur);
		}
		
		this.basemur.inject(opt.fondations);
	},
	poseBrique : function(){
		var mthis = this, opt = this.opt;
		var briques = opt.briques.getChildren();
		if(!briques[0]) return;
		
		// On prend la premiere brique du tas
		var brique = briques[0];
		
		// On récupère la colonne la plus petite
		var petite_colonne = 0;
		var petite_hauteur = 1000000000;
		var hauteur_col_tmp = 0;
		for(i=0;i<opt.nbcol;i++){
			hauteur_col_tmp = this.colonnes[i].getHeight();
			if(hauteur_col_tmp < petite_hauteur){
				petite_colonne = this.colonnes[i];
				petite_hauteur = hauteur_col_tmp;
			}
		}
		
		// On pose la brique
		brique_morph = new Fx.Morph(brique,{duration: 500, transition: 'quad:out'});
		brique.setStyles({'opacity':0}).inject(petite_colonne);
		
		// On affiche la brique
		brique_morph.start({'opacity':1});
		
		// On passe à la suivante
		(function(){
			mthis.poseBrique();
		}).delay(200);
	}
});
/*
var CLRZMaconnerie = null;
window.addEvent('domready',function(){
	CLRZMaconnerie = new CLRZMachonnerie({
		'nbcol':8,
		'briques':$('briques'),
		'fondations':$('fondations'),
	});
});

window.addEvent('load',function(){
	CLRZMaconnerie.poseBrique();
});
*/