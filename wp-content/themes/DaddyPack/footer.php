<?php include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php'; ?>
<hr class="clearfix" />
</div><!-- .main-container -->
</div><!-- #main -->
<footer id="footer" class="centered-container">
    <div class="main-footer">
        <div class="main-footer__copyright">
            <span class="main-footer__colorz">
                <?php echo __( 'WordPress par', 'clrz_lang' ); ?>
                <a target="_blank" href="http://www.colorz.fr/" title="Colorz . Communication Interactive &amp; Cr&eacute;ative">Colorz</a>
            </span>
            <a href="<?php echo get_permalink( MENTIONSLEGALES_PAGEID ); ?>">
                <?php echo '&copy; ' . date( 'Y' ) . ' ' .get_bloginfo( 'name' ) . ' — ' . __( 'Tous droits réservés', 'clrz_lang' ); ?>
            </a>
        </div>
    </div>
</footer>
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
