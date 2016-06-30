<?php include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php'; ?>

<?php
wp_footer();
echo clrz_get_template_part( 'footer', 'social_scripts' );
echo clrz_get_template_part( 'footer', 'google_analytics' );
echo clrz_get_template_part( 'footer', 'debug' );
?>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="<?php echo get_template_directory_uri() ?>/assets/js/event.js"></script>

<script src="https://use.typekit.net/dbd5dzw.js"></script>
<script>try{Typekit.load({ async: true });}catch(e){}</script>
</body>
</html>
