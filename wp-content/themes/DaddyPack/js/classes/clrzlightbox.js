
var clrzLightBox = new Class({
    Implements: [Options,Events],
    options : {
        lightboxelements : [],
        islightboxmode : 0,
        maxlightboxelements : 0,
        actualelement : 0,
        prevelement : 0,
        nextelement : 0,
        elementFilter : 0,
        elementLightBoxSon : 0,
        elementLightBoxGranSon : 0,
        elementImg : 0,
        elementNext : 0,
        elementPrev : 0
    },
    initialize : function(elementscibles,options){
        this.setOptions(options);
        var maclass = this;
        this.lightboxelements = new Array();
        $$(elementscibles).each(function(el,i){
            maclass.lightboxelements[i] = el.href;
            Asset.image(el.href);
        });
        this.maxlightboxelements = this.lightboxelements.length-1;
        $$(elementscibles).addEvent('click',function(e){
            e.preventDefault();
            maclass.setDiapo(this.href);
            maclass.launchlightbox();
        });
        maclass.createLightBox();
        maclass.setEvents();
    },
    createLightBox:function(){
        var maclass = this;
        // Filter Lightbox
        maclass.elementFilter = new Element('div#lightboxfilter',{
            styles:{
                'opacity':0,
                'position':'fixed',
                'top':0,
                'right':0,
                'bottom':0,
                'left':0,
                'z-index':99999999
            }
        });
        maclass.elementFilter.inject($(document.body));
        maclass.elementFilter.set('morph',{
            duration: 100,
            transition: Fx.Transitions.Quad.easeOut
        });

        // Container Lightbox
        maclass.elementLightBoxSon = new Element('div#containerlightbox',{
            styles:{
                'opacity':0,
                'position':'absolute',
                'top':'50%',
                'left':'50%',
                'z-index':999999999
            }
        });
        maclass.elementLightBoxSon.inject($(document.body));
        maclass.elementLightBoxSon.set('morph',{
            duration: 100,
            transition: Fx.Transitions.Quad.easeOut
        });

        maclass.elementLightBoxGranSon = new Element('div#containergranson',{
            styles:{
                'position':'relative',
                'overflow':'hidden'
            }
        });
        maclass.elementLightBoxGranSon.inject(maclass.elementLightBoxSon);

        // Lien Precedent
        maclass.elementPrev = new Element('a#prevlightbox.prev',{
            'href' : '#',
            'html' : 'Precedent'
        });
        maclass.elementPrev.inject(maclass.elementLightBoxGranSon);

        // Image Lightbox
        maclass.elementImg = new Element('img#mainlightboximg',{
            'src' : '',
            'alt' : ''
        });
        maclass.elementImg.inject(maclass.elementLightBoxGranSon);
        maclass.elementImg.set('morph',{
            duration: 100,
            transition: Fx.Transitions.Quad.easeOut
        });
        // Lien Suivant
        maclass.elementNext = new Element('a#nextlightbox.next',{
            'href' : '#',
            'html' : 'Suivant'
        });
        maclass.elementNext.inject(maclass.elementLightBoxGranSon);
    },
    setEvents:function(){
        var maclass = this;
        maclass.elementFilter.addEvent('click',function(){
            maclass.killlightbox();
        });
        maclass.elementPrev.addEvent('click',function(e){
            e.preventDefault();
            maclass.setDiapo(maclass.elementPrev.href);
        });
        maclass.elementImg.addEvent('click',function(e){
            e.preventDefault();
            maclass.setDiapo(maclass.elementNext.href);
        });
        maclass.elementNext.addEvent('click',function(e){
            e.preventDefault();
            maclass.setDiapo(maclass.elementNext.href);
        });

        if ( window.addEventListener ){
            window.addEventListener("keydown", function(e){
                if(maclass.islightboxmode == 1){
                    if(e.keyCode == '37' || e.keyCode == '75'){ // Left or J
                        maclass.setDiapo(maclass.elementPrev.href);
                    } else if(e.keyCode == '39' || e.keyCode == '74') { // Right or k
                        maclass.setDiapo(maclass.elementNext.href);
                    } else if(e.keyCode == '27') { // Esc.
                        maclass.killlightbox();
                    }
                }
            },
            true);
        }
    },
    setDiapo:function(lehref){
        var maclass = this;

        this.elementImg.morph({
            'opacity' : [1,0]
        });
        (function(){
            maclass.elementImg.setAttribute('src',lehref);
            (function(){
                var topheight = 0 - $('mainlightboximg').getHeight() / 2;
                var leftwidth = 0 - $('containergranson').getWidth() / 2;
                maclass.elementLightBoxSon.setStyles({
                    'margin-left': leftwidth,
                    'margin-top': topheight
                });
            }).delay(30);

        }).delay(100);
        (function(){
            maclass.elementImg.morph({
                'opacity' : [0,1]
            });
        }).delay(250);

        maclass.actualelement = maclass.lightboxelements.indexOf(lehref);
        maclass.prevelement = (maclass.actualelement != 0) ? maclass.actualelement -1 : maclass.maxlightboxelements;
        maclass.nextelement = (maclass.actualelement != maclass.maxlightboxelements) ? maclass.actualelement + 1 : 0;
        maclass.elementPrev.set('href',maclass.lightboxelements[maclass.prevelement]);
        maclass.elementNext.set('href',maclass.lightboxelements[maclass.nextelement]);

    },
    launchlightbox:function(){
        this.islightboxmode = 1;
        this.elementLightBoxSon.setStyles({
            'display':'block'
        }).morph({
            'opacity' : [0,1]
        });
        this.elementFilter.setStyles({
            'display':'block'
        }).morph({
            'opacity' : [0,1]
        });
    },
    killlightbox:function(){
        var maclass = this;
        this.islightboxmode = 0;
        this.elementLightBoxSon.morph({
            'opacity':[1,0]
        });
        (function(){
            maclass.elementLightBoxSon.setStyles({
                'display':'none'
            })
        }).delay(300);
        this.elementFilter.morph({
            'opacity' : [1,0]
        });
        (function(){
            maclass.elementFilter.setStyles({'display':'none'})
        }).delay(300);
    }
});