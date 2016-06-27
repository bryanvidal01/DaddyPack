<?php

/*
 * Ajoute un Shortcode de Permalink
 * src = http://wptricks.net/added-permalinks-shortcode-on-wordpress/
 * [permalink id=49 text='providing text']
 * <a href="[permalink id=49]">Basic Usage</a>
 *
 */
add_shortcode('permalink', 'do_permalink');
function do_permalink($atts) {
    extract(shortcode_atts(array(
        'id' => 1,
        'text' => ""  // default value if none supplied
    ), $atts));
    return ($text) ? "<a href='".get_permalink($id)."'>$text</a>" : get_permalink($id) ;
}

/*
 * Ajout un embed de google docs
 * [embedpdf width="600px" height="500px"]http://infolab.stanford.edu/pub/papers/google.pdf[/embedpdf]
 */
add_shortcode('embedpdf', 'cwc_viewpdf');
function cwc_viewpdf($attr, $url) {
    return '<iframe src="http://docs.google.com/viewer?url=' . $url . '&embedded=true" style="width:' . $attr['width'] . '; height:' . $attr['height'] . ';" frameborder="0">' .
            '<a href="'.$url.'">T&eacute;l&eacute;chargez ce PDF</a>' .
            '</iframe>';
}

/*
 * Ajoute une gallerie d'images
 * [clrz_gallery]
 */
add_shortcode( 'clrz_gallery', 'clrz_gallery_shortcode' );
function clrz_gallery_shortcode() {
    global $post;
    $return = '';
    $images = array();
    $images_pagination = array();
    $attachments = get_posts( array(
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_status' =>'any',
            'post_parent' => $post->ID
        ) );
    ob_start();
    if ( !empty( $attachments ) ) {
        foreach ( $attachments as $attachment ) {
            $src = wp_get_attachment_image_src( $attachment->ID, 'full' );
            $src_thumb = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
            $images[] = '<li><img src="'.$src[0].'" alt="" /></li>';
            $images_pagination[] = '<li><img src="'.$src_thumb[0].'" alt="" /></li>';
        }
        echo '<div class="clrz-gallery">';
        echo '<ul class="clrz-gallery-images">' . implode( '', $images ) . '</ul>';
        echo '<ul class="clrz-gallery-pagination">' . implode( '', $images_pagination ) . '</ul>';
        echo '</div>';
    }
    $return = ob_get_contents();
    ob_end_clean();
    return $return;
}
