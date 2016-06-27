jQuery(document).ready(function(){
    jQuery('.block-video').click(function(){
        var el = jQuery(this);
        var id = el.data('video');


        if(!el.hasClass('isPlayed')){
            jQuery(el).append("<iframe width='854' height='480' src='https://www.youtube.com/embed/"+ id +"?rel=0&autoplay=1&color=white&iv_load_policy=3&modestbranding=1&showinfo=0' frameborder='0' allowfullscreen></iframe>");
            el.addClass('isPlayed');
        }
    })
})
