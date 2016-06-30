jQuery(document).ready(function(){
    jQuery('.fixed-barre .close').click(function(){
        jQuery('.fixed-barre').addClass('hidden');
    });

    jQuery(window).scroll(function(){
        var currentScroll = jQuery(window).scrollTop();
        var headerHeight = jQuery('.container-header').height();
        if(currentScroll > headerHeight){
            jQuery('.fixed-menu').addClass('visible')
        }else{
            jQuery('.fixed-menu').removeClass('visible')
        }
    })
})
