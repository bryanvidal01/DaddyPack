<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

$global_post_fav = get_user_meta($clrz_user->get('ID'),'ids_favorites');

get_header();


get_footer();