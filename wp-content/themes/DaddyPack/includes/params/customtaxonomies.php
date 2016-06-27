<?php

add_action( 'init', 'clrz_create_taxonomies', 0 );
function clrz_create_taxonomies() {
    $clrz_taxonomies = array(
        // 'genre' => array(
        //     'name' => 'Genre',
        //     'pluriel' => 'Genres',
        //     'feminin' => 0,
        //     'hierarchical' => true,
        //     'post_types' => array( 'post', 'page' ),
        // ),
    );
    clrz_launch_create_taxonomies( $clrz_taxonomies );

}

$clrz_taxonomies_extra_fields = array(
    // 'seo_title' => array(
    //  'name' => 'SEO Title',
    //  'description' => 'enter seo title',
    //  'taxonomies' => array('genre')
    // ),
    // 'seo_description' => array(
    //  'name' => 'SEO Description',
    //  'description' => 'enter seo description',
    //  'type' => 'textarea',
    //  'taxonomies' => array('genre')
    // ),
);
