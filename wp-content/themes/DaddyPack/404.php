<?php
include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php';

// Si la 404 se produit sur une demande de fichier statique, on ne sert pas une page complete.
// Src : http://www.binarymoon.co.uk/2011/04/optimizing-wordpress-404s/
$fileExtension = '';
if ( !empty( $_SERVER['REQUEST_URI'] ) ) {
    $fileExtension = strtolower( pathinfo( $_SERVER['REQUEST_URI'], PATHINFO_EXTENSION ) );
    if ( in_array( $fileExtension, array( 'css', 'txt', 'jpg', 'gif', 'rar', 'zip', 'png', 'bmp', 'tar', 'doc', 'xml', 'js' ) ) ) {
        exit( __( 'Erreur, ce fichier n\'existe pas', 'clrz_lang' ) );
    }
}

// Check des mots clefs contenus dans l'url, et lancement d'une recherche sur ce terme.
$requete_recherche = str_replace( array( '?', '/', '=', '-' ), ' ', strip_tags( $_SERVER['REQUEST_URI'] ) );
get_header();
?>
<div class="cssn-lay">
    <div class="col-main" id="content">
        <h2><?php echo __( 'Page non trouv&eacute;e', 'clrz_lang' ); ?></h2>
<?php
if ( !empty( $requete_recherche ) ) {
    query_posts( array(
            'posts_per_page' => '5',
            'post_type' => 'post',
            's' => $requete_recherche
        ) );
    if ( have_posts() ) {
        echo '<p>' . sprintf( __( 'Voici quelques r&eacute;sultats de recherche pour "%s":', 'clrz_lang' ), $requete_recherche ) . '</p>';
        echo '<ul class="list-loop-short">';
        while ( have_posts() ) {
            the_post();
            echo '<li>';
            get_template_part( 'loop', 'short' );
            echo '</li>';
        }
        echo '</ul>';
    }
    wp_reset_query();
}
?>
    </div>
    <div class="col-side">
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer();
