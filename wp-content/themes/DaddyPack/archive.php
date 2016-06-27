<?php
include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php';
get_header();

$noresults = false;
$contenu_more = '';
$titre_page = '';

if ( is_day() ) {
    $titre_page = __( 'Archives', 'clrz_lang' ) . ' - ' . get_the_date( 'j F Y' );
} elseif ( is_month() ) {
    $titre_page = __( 'Archives', 'clrz_lang' ) . ' - ' . get_the_date( 'F Y' );
} elseif ( is_year() ) {
    $titre_page = __( 'Archives', 'clrz_lang' ) . ' - ' . get_the_date( 'Y' );
} elseif ( is_tag() ) {
    $titre_page = __( 'Tag', 'clrz_lang' ) . ' - ' . single_cat_title( "", false );
} elseif ( is_author() ) {
    $auteur = get_user_by( 'id', $author );
    $titre_page = __( 'Auteur', 'clrz_lang' ) . ' - ' . $auteur->display_name;
} elseif ( is_category() ) {
    $titre_page = __( 'Categorie', 'clrz_lang' ) . ' - ' . single_cat_title( "", false );
} else {
    $noresults = true;
    $titre_page = __( 'Erreur', 'clrz_lang' );
}

if ( is_day() || is_month() || is_year() ) {
    include TEMPLATEPATH . '/tpl/tpl_pagination_dates.php';
}

$args = array(
    'posts_per_page' => get_option( 'posts_per_page' ),
    'post_type' => 'post',
    'paged' => $paged,
);

if ( is_tag() ) {
    $args['tag'] = $tag;
}

if ( is_category() ) {
    $args['cat'] = $cat;
}

if ( is_day() || is_month() || is_year() ) {
    $args['year'] = get_the_date( 'Y' );
    if ( is_month() || is_day() ) {
        $args['monthnum'] = get_the_date( 'm' );
        if ( is_day() ) {
            $args['day'] = get_the_date( 'd' );
        }
    }
}

if ( is_author() ) {
    $args['author'] = $author;
}


query_posts( $args );

?>
    <div class="cssn-lay">
        <div class="col-main" id="content">
<?php
echo '<h2>' . $titre_page . '</h2>';
echo $contenu_more;
if ( have_posts() && !$noresults ) {
    echo '<ul class="list-loop-short">';
    while ( have_posts() ) {
        the_post();
        echo '<li>';
        get_template_part( 'loop', 'short' );
        echo '</li>';
    }
    echo '</ul>';
    include TEMPLATEPATH . '/tpl/tpl_pagination.php';
} else {
    echo '<p>'.__( 'Erreur, aucun r&eacute;sultat pour cette requ&ecirc;te.', 'clrz_lang' ).'</p>';
}
wp_reset_query();
?>
        </div>
        <div class="col-side">
            <?php get_sidebar(); ?>
        </div>
    </div>
<?php
get_footer();
