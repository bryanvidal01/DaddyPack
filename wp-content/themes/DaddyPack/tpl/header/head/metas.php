<?php
$clrz_metas = array();
$thumbnail = '';

$clrz_google_site_verification = get_option( 'clrz_google_site_verification' );
if ( $clrz_google_site_verification != '' ) {
    echo '<meta name="google-site-verification" content="' . $clrz_google_site_verification . '"/>' . "\n";
}

if ( is_singular() ) {
    global $post;

    $meta_description = '';
    $meta_description = trim( strip_tags( strip_shortcodes( $post->post_content ) ) );
    $meta_description = substr( str_replace( array( "\n", "\r", "\t" ), ' ', $meta_description ), 0, 125 );

    // Metas Classiques
    $clrz_metas[] = array( 'name' => 'description', 'content' => $meta_description );
    $clrz_metas[] = array( 'name' => 'title', 'content' => strip_tags( get_the_title( $post->ID ) ) );

    if ( function_exists( 'get_post_thumb' ) ) {
        $thumbnail = get_post_thumb( 'medium', $post->ID );
    }

    // Open Graph
    $clrz_metas[] = array( 'property'=>'og:title', 'content'=> strip_tags( get_the_title() ) );
    $clrz_metas[] = array( 'property'=>'og:type', 'content'=> 'article' );
    $clrz_metas[] = array( 'property'=>'og:url', 'content'=> get_permalink() );
    $clrz_metas[] = array( 'property'=>'og:site_name', 'content'=> get_bloginfo( 'name' ) );

}

if ( is_home() ) {
    // Modifier cette image si possible.
    $thumbnail = get_bloginfo( 'template_url' ).'/screenshot.png';
    $clrz_metas[] = array( 'name' => 'description', 'content' => get_bloginfo( 'description' ) );
    $clrz_metas[] = array( 'property'=>'og:type', 'content'=> 'website' );
    $clrz_metas[] = array( 'property'=>'og:title', 'content'=> get_bloginfo( 'name' ) );
    $clrz_metas[] = array( 'property'=>'og:url', 'content'=> site_url() );
}

if ( !empty( $thumbnail ) ) {
    $clrz_metas[] = array( 'property'=>'og:image', 'content'=> $thumbnail );
    echo '<link rel="image_src" href="'.$thumbnail.'" />';
}

foreach ( $clrz_metas as $name => $attributs ) {
    $meta = '<meta';
    foreach ( $attributs as $id => $value )
        $meta .= ' '.$id.'="'.$value.'"';
    $meta .= ' />'."\n";
    echo $meta;
}
