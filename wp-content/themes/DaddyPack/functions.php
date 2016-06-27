<?php include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php';

// Prevent bad stuff with magic quotes
if ( !is_admin() && get_magic_quotes_gpc() ) {
    $_POST      = array_map( 'stripslashes_deep', $_POST );
    $_GET       = array_map( 'stripslashes_deep', $_GET );
    $_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
    $_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
}

// Detect development server
$is_dev_mode = false;
if ( isset( $_SERVER['HTTP_HOST'] ) ) {
    $host = $_SERVER['HTTP_HOST'];
    $is_dev_mode = ( strpos( $host, 'feed.colorz.fr' ) !== FALSE ) || ( strpos( $host, 'box.colorz.fr' ) !== FALSE );
}
define( 'CLRZ_IS_DEV_MODE', $is_dev_mode );

// Automatic RSS feed links
// add_theme_support( 'automatic-feed-links' );

// WP Editor CSS
add_editor_style( 'css/c-post-content.css' );

// JPG Quality
define( 'CLRZ_JPEG_QUALITY', 90 );

// PAGES constants
define( 'ABOUT_PAGEID', get_option( 'clrz_define_about_pageid' ) );
define( 'CONTACT_PAGEID', get_option( 'clrz_define_contact_pageid' ) );
define( 'MENTIONSLEGALES_PAGEID', get_option( 'clrz_define_mentionslegales_pageid' ) );
define( 'PLANDUSITE_PAGEID', get_option( 'clrz_define_plandusite_pageid' ) );
define( 'WEBSERVICE_PAGEID', get_option( 'clrz_define_webservice_pageid' ) );

// CATEGORIES constants
// define('INFOS_CATID',3);

add_theme_support( 'post-thumbnails' );
add_image_size( '1140x530', 1140, 530, true ); // Hard Crop Mode

include TEMPLATEPATH.'/includes/includes.php';
