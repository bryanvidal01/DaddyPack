<?php
/* ----------------------------------------------------------
  Uploads
---------------------------------------------------------- */

/* Filenames
-------------------------- */

add_filter('sanitize_file_name', 'remove_accents' );
add_filter('sanitize_file_name', 'strtolower' );

/* JPG Quality
-------------------------- */

add_filter('jpeg_quality', 'clrz_jpeg_quality_callback');
function clrz_jpeg_quality_callback($arg) {
    if (!defined(CLRZ_JPEG_QUALITY))
        define('CLRZ_JPEG_QUALITY', 90);
    return (int) CLRZ_JPEG_QUALITY;
}

/* ----------------------------------------------------------
  Content
---------------------------------------------------------- */

// fix invalid links
add_filter( 'the_content', 'clrz_fix_content_href' );
function clrz_fix_content_href( $content ) {
    $content = str_replace( 'href="www.', 'href="http://www.', $content );
    $content = str_replace( 'href="http//.', 'href="http://', $content );
    return $content;
}

/* ----------------------------------------------------------
  Body Classes
---------------------------------------------------------- */

add_filter( 'body_class', 'clrz_add_bodyclass' );
function clrz_add_bodyclass( $classes ) {
    global $post, $q_config;
    if ( is_page() ) {
        $classes[] = 'clrzwp-ispage-'.$post->post_name;
    }
    if ( is_single() ) {
        $categories = wp_get_post_categories( $post->ID, array( 'fields' => 'slugs' ) );
        foreach ( $categories as $category ) {
            $classes[] = 'clrzwp-incat-'.$category;
        }
    }
    if ( isset( $q_config['language'] ) ) {
        $classes[] = 'clrzwp-haslang-'.$q_config['language'];
    }
    return $classes;
}

/* ----------------------------------------------------------
  Page title
---------------------------------------------------------- */

add_filter( 'wp_title', 'clrz_custom_title', 10, 2 );
function clrz_custom_title( $title, $sep ) {
    $clrz_page_title = $title;
    $separator = $sep;
    if ( is_home() ) {
        $clrz_page_title = get_bloginfo( 'name' );
        if ( get_bloginfo( 'description' ) != '' ) {
            $clrz_page_title .= $separator.get_bloginfo( 'description' );
        }
    }
    else if ( is_search() ) {
            $clrz_page_title = sprintf( __( 'Résultats de recherche pour "%s"', 'clrz_lang' ), get_search_query() ).'"'.$separator.get_bloginfo( 'name' );
        }
    else if ( is_404() ) {
            $clrz_page_title = __( 'Page non trouvée', 'clrz_lang' ).$separator.get_bloginfo( 'name' );
        }
    else if ( is_single() || is_page() ) {
            $clrz_page_title = single_post_title( '', FALSE ).$separator.get_bloginfo( 'name' );
        }
    else if ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();
            if ( !isset( $term->taxonomy ) ) {
                break;
            }
            $terms_taxonomy = get_taxonomy( $term->taxonomy );
            if ( !isset( $terms_taxonomy->labels->singular_name ) ) {
                break;
            }
            $clrz_page_title = ucfirst($terms_taxonomy->labels->singular_name).' : '.single_term_title( '', false ).$separator.get_bloginfo( 'name' );
        }
    else if (is_post_type_archive()) {
            $clrz_page_title = post_type_archive_title('', 0).$separator.get_bloginfo( 'name' );
        }
    else if ( is_year() ) {
            $clrz_page_title = get_the_time( 'Y' ).$separator.get_bloginfo( 'name' );
        }
    else if ( is_month() ) {
            $clrz_page_title = ucfirst( get_the_time( __( 'F Y', 'clrz_lang' ) ) ).$separator.get_bloginfo( 'name' );
        }
    else if ( is_day() ) {
            $clrz_page_title = ucfirst( get_the_time( __( 'j F Y', 'clrz_lang' ) ) ).$separator.get_bloginfo( 'name' );
        }
    else if ( is_tax() ) {
            $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
            $term_meta = get_option( "clrz_meta_taxonomy_".$term->term_id );
            if ( $term_meta['seo_title'] ) {
                return $term_meta['seo_title'];}
        }
    return $clrz_page_title;
}

/* ----------------------------------------------------------
  Various
---------------------------------------------------------- */

// New contact methods for user
add_filter('user_contactmethods', 'add_extra_contactmethod', 10, 1);
function add_extra_contactmethod( $contactmethods ) {
    // Add new ones
    $contactmethods['clrz_facebook'] = 'Facebook';
    $contactmethods['clrz_twitter'] = 'Twitter';

    // remove unwanted
    unset($contactmethods['aim']);
    unset($contactmethods['jabber']);
    unset($contactmethods['yim']);

    return $contactmethods;
}

// SEO TAXONOMIES
add_filter("pre_option_blogdescription", 'clrz_custom_description');
function clrz_custom_description() {
    if (is_tax()) {
        $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        $term_meta = get_option( "clrz_meta_taxonomy_".$term->term_id );
        if ($term_meta['seo_description'])
            return $term_meta['seo_description'];
    }
    return false;
}

// traduction des taxonomies
if (function_exists('qtrans_useTermLib')) {
    add_filter('get_term', 'qtrans_useTermLib');
}
