<?php
include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php';
get_header();
?>
    <div class="cssn-lay">
        <div class="col-main" id="content">
<?php
if ( have_posts () ) {
    $nb_results = $wp_query->found_posts;
    $several = ( $nb_results<=1 ) ? '' : 's'; //pluriel
    echo '<h2><span>' . $nb_results . '</span> ' . __( 'r&eacute;sultat', 'clrz_lang' ) . $several . ' ' . __( 'pour la recherche :', 'clrz_lang' ) . ' &quot;' . get_search_query() . '&quot;</h2>';
    echo '<ul class="list-loop-short">';
    while ( have_posts() ) {
        the_post();
        echo '<li>';
        get_template_part( 'loop', 'short' );
        echo '</li>';
    }
    echo '</ul>';
    include TEMPLATEPATH.'/tpl/tpl_pagination.php';
}
else {
    echo '<p>' . sprintf( __( 'Aucun r&eacute;sultat pour &quot;%s&quot;', 'clrz_lang' ), get_search_query() ) . '</p>';
}
?>
        </div>
        <div class="col-side">
            <?php get_sidebar(); ?>
        </div>
    </div>
<?php
get_footer();
