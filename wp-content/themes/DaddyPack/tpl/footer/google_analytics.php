<?php
$code_analytics = get_option( 'clrz_options_code_google_analytics' );
if ( !is_preview() && !CLRZ_IS_DEV_MODE && $code_analytics != '' && $code_analytics != 'UA-XXXXX-X' ) : ?><script>

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', '<?php echo $code_analytics;?>', 'auto');
ga('send', 'pageview');


</script><?php
endif;
