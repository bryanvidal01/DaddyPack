<?php

// Initialise la recherche de langues pour le theme dans /lang
add_action( 'init', 'clrz_lang_init' );
function clrz_lang_init() {
    load_theme_textdomain( 'clrz_lang', get_template_directory() . '/lang' );
}

// Annule les pings entre liens internes
add_action( 'pre_ping', 'disable_self_ping' );
function disable_self_ping( &$links ) {
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, site_url() ) )
            unset( $links[$l] );
}

// Ajoute un lien canonical pour eviter le duplicate content des commentaires
// http://www.wprecipes.com/wordpress-hack-canonical-links-for-comments
add_action( 'wp_head', 'canonical_for_comments' );
function canonical_for_comments() {
    global $cpage, $post;
    if ( $cpage > 1 )
        echo "\n<link rel='canonical' href='" . get_permalink( $post->ID ) . "' />\n";
}

// Sur un template d'archive par date avec custom post type, on desactive la 404.
// Le calcul ne se fait pour l'instant ( WP 3.1.2 ) que sur le nombre de posts type "post" disponibles.
add_action( 'template_redirect', 'intercept_archive_custom_post', 10 );
function intercept_archive_custom_post() {
    global $wp_query;
    if ( isset( $wp_query->query_vars, $wp_query->query_vars['year'] ) && $wp_query->query_vars['year'] != 0 ) {
        $wp_query->is_404 = '';
        $wp_query->is_archive = '1';
    }
}

// Note l'id de la sidebar appelee dans une variable globale
add_action( 'get_sidebar', 'custom_global_clrz_sidebar_slug' );
function custom_global_clrz_sidebar_slug( $name ) {
    global $global_clrz_sidebar_slug;
    $global_clrz_sidebar_slug = $name;
}

// Ajoute une colonne dimensions aux medias
add_filter( 'manage_media_columns', 'wh_column' );
add_action( 'manage_media_custom_column', 'wh_value', 10, 2 );

function wh_column( $cols ) {
    $cols["dimensions"] = "Dimensions (w, h)";
    return $cols;
}
function wh_value( $column_name, $id ) {
    $meta = wp_get_attachment_metadata( $id );
    if ( isset( $meta['width'] ) )
        echo $meta['width'] . ' x ' . $meta['height'];
}



/*
// Affiche toutes les fonctions hookees
function list_hooked_functions($tag=false){
 global $wp_filter;
 if ($tag) {
  $hook[$tag]=$wp_filter[$tag];
  if (!is_array($hook[$tag])) {
  trigger_error("Nothing found for '$tag' hook", E_USER_WARNING);
  return;
  }
 }
 else {
  $hook=$wp_filter;
  ksort($hook);
 }
 echo '<pre>';
 foreach($hook as $tag => $priority){
  echo "<br />&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
  ksort($priority);
  foreach($priority as $priority => $function){
  echo $priority;
  foreach($function as $name => $properties) echo "\t$name<br />";
  }
 }
 echo '</pre>';
 return;
}
list_hooked_functions();
 */


function qtranslate_edit_taxonomies() {
    $args=array(
        'public' => true ,
        '_builtin' => false
    );
    $output = 'object'; // or objects
    $operator = 'and'; // 'and' or 'or'

    $taxonomies = get_taxonomies( $args, $output, $operator );

    if  ( $taxonomies ) {
        foreach ( $taxonomies  as $taxonomy ) {
            add_action( $taxonomy->name.'_add_form', 'qtrans_modifyTermFormFor' );
            add_action( $taxonomy->name.'_edit_form', 'qtrans_modifyTermFormFor' );
        }
    }

}
if ( function_exists( 'qtrans_useTermLib' ) )
    add_action( 'admin_init', 'qtranslate_edit_taxonomies' );


/* ----------------------------------------------------------
  Set media select to uploaded : http://wordpress.stackexchange.com/a/76213
---------------------------------------------------------- */

add_action( 'admin_footer-post-new.php', 'wputh_set_media_select_uploaded' );
add_action( 'admin_footer-post.php', 'wputh_set_media_select_uploaded' );

function wputh_set_media_select_uploaded() { ?><script>
jQuery(function($) {
    var called = 0;
    $('#wpcontent').ajaxStop(function() {
        if (0 === called) {
            $('[value="uploaded"]').attr('selected', true).parent().trigger('change');
            called = 1;
        }
    });
});
</script><?php }
