<?php

add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'video',
    array(
      'labels' => array(
        'name' => __( 'Vidéo' ),
        'singular_name' => __( 'Vidéos' )
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon'   => 'dashicons-video-alt',
      'supports' => array('title')
    )
  );
}
