$(document.html).addClass('js').removeClass('no-js');

window.addEvent('domready', function() {

    // Ajouts pour iOS
    ios_fixes();

    // Classes navigateur en JS
    clrz_body_class();

    // Scroll fluide
    initSmoothScroll('a[href^="#"], a.smooth-scroll');

    // Polyfills
    $$(".fake-placeholder-me").each(function(el) {
        new FakePlaceholder(el);
    });
    $$(".fake-select-me").each(function(el) {
        new FakeSelect(el);
    });
    $$(".fake-inputbox-me").each(function(el) {
        new FakeInputBox(el);
    });

    //  Décommenter si commentaires avec enfants
    if ($('clrz_comment_parent') && $('txt_add_your_comment')) clrz_comments_parents();

    // Menu - Mobile

    $$('.toggle-menu').addEvent("click", toggleMenuMobile);

});

window.addEvent('load', function(j) {
    // clrz_same_size('.samesize1, .samesize2');
});