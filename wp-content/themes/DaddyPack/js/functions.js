function IsNumeric(valeur) {
   return (valeur - 0) == valeur && valeur.length > 0;
}

// Detect touch device http://stackoverflow.com/a/4819886
var is_touch_device = function() {
    return 'ontouchstart' in window || 'onmsgesturechange' in window;
};

function clrz_body_class() {
    bod = document.body.className.split(' ');
    var nav = navigator.userAgent.toLowerCase();

    // Detect capacities
    if (is_touch_device()) bod.push('is_touchscreen');

    // Detect FFx
    if (nav.match(/firefox/gi)) bod.push('is_firefox');

    // Detect Webkit
    if (nav.match(/AppleWebKit/gi)) bod.push('is_webkit');

    // Detect Chrome
    if (nav.match(/Chrome/gi)) bod.push('is_chrome');

    // Detect Opera
    if (nav.match(/opera/gi)) bod.push('is_opera');

    // Detect iOS
    if (nav.match(/ipod/gi) || nav.match(/iphone/gi) || nav.match(/ipad/gi)) bod.push('is_ios');
    if (nav.match(/ipod/gi)) bod.push('is_ipod');
    if (nav.match(/iphone/gi)) bod.push('is_iphone');
    if (nav.match(/ipad/gi)) bod.push('is_ipad');

    document.body.className = bod.join(' ');
}

function clrz_same_size(cible){
    var max_height = 0;
    $$(cible).each(function(el){
        var tmp_height = el.getHeight() - el.getStyle('padding-top').toInt() - el.getStyle('padding-bottom').toInt();
        max_height = Math.max(max_height,tmp_height);
    });
    $$(cible).each(function(el){
        var myEffect = new Fx.Morph(el, {
            duration: '50',
            transition: Fx.Transitions.Sine.easeOut
        });
        myEffect.start({'height':max_height});
    });
}

function clrz_comments_parents(){
    if(!$('dont_reply_to_comment') || !$('txt_add_your_comment') || !$('txt_reply_to_comment') || !$('add_your_comment')) return;
    $$('.clrz_click_comments_form').each(function(e){
        e.addEvent('click',function(){
            $('clrz_comment_parent').set('value',e.get('rel'));
            $('dont_reply_to_comment').setStyles({'display':''});
            $('add_your_comment').set('html',$('txt_reply_to_comment').getAttribute('value'));
        });
    });
    $('dont_reply_to_comment')
        .setStyles({'display':'none'})
        .addEvent('click',function(e){
            e.stop();
            this.setStyles({'display':'none'});
            $('clrz_comment_parent').set('value','');
            $('add_your_comment').set('html',$('txt_add_your_comment').getAttribute('value'));
        });


}


/* ANCRE */
function initSmoothScroll(linkancre) {
    $$(linkancre).each(function(el,i){
        el.addEvents({
            'click': function(e){
                e.stop();
                var target = el.getProperty('href');
                var divToScrollTo = target.split('#')[1];
                if($(divToScrollTo)){
                    new Fx.Scroll(window, {
                            duration: 700
                    }).toElement($(divToScrollTo));
                }
            }
        });
    });
}

function ios_fixes() {
    var user_agent = navigator.userAgent.toLowerCase();
    if (user_agent.match(/ipad/gi) || user_agent.match(/ipod/gi) || user_agent.match(/iphone/gi)) {
        $(document.body).setStyles({
            '-webkit-text-size-adjust': 'none'
        });
        setTimeout(function() {
            window.scrollTo(0, 1);
        }, 50);
    }
}

/* ----------------------------------------------------------
    Touchmove
   ------------------------------------------------------- */

function clrzSetTouchMove(el) {

    el.addEvent('touchstart', function(e) {
        // e.preventDefault();
        var etouches = e.touches || e.changedTouches,
            touch = etouches[0];

        if(etouches.length > 1)
            return;

        this.store('touchx', touch.pageX);
        this.store('touchy', touch.pageY);
        this.store('eventLaunchable', 1);
    });

    el.addEvent('touchend', function(e) {
        // e.preventDefault();
        var touch = e.touches[0] || e.changedTouches[0];
        this.store('touchx', touch.pageX);
        this.store('touchy', touch.pageY);
        this.store('eventLaunchable', 1);
    });

    el.addEvent('touchmove', function(e) {
        var _this = this,
            pas = 25,
            origX = _this.retrieve('touchx'),
            origY = _this.retrieve('touchy'),
            eventLaunchable = _this.retrieve('eventLaunchable'),
            touch = e.touches[0] || e.changedTouches[0];

        // Annuler si event non launchable
        if (eventLaunchable !== 1) {
            return;
        }

        // Top
        if (origY - pas > touch.pageY) {
            e.preventDefault();
            _this.store('eventLaunchable', 0);
            _this.fireEvent('swiftTop');
        }

        // Right
        if (origX + pas < touch.pageX) {
            e.preventDefault();
            _this.store('eventLaunchable', 0);
            _this.fireEvent('swiftRight');
        }

        // Bottom
        if (origY + pas < touch.pageY) {
            e.preventDefault();
            _this.store('eventLaunchable', 0);
            _this.fireEvent('swiftBottom');
        }

        // Left
        if (origX - pas > touch.pageX) {
            e.preventDefault();
            _this.store('eventLaunchable', 0);
            _this.fireEvent('swiftLeft');
        }

    });
}

var toggleMenuMobile = function() {
    document.body.toggleClass("menu-open");
};