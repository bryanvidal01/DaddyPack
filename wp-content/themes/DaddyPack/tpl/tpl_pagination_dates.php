<?php
$precedent_w = '';
$precedent = '';
$suivant = '';
$contenu_mois = '';
$date_page = get_the_date( '/Y/m/' );
$year_page = get_the_date( 'Y' );
$myrows = $wpdb->get_results( "
    SELECT post_date, post_type
    FROM $wpdb->posts
    WHERE post_status = 'publish' AND post_type = 'post'
" );
$mois = array();
$years = array();
$months_page = array();

foreach ( $myrows as $row ) {
    $timestamp = strtotime( $row->post_date );
    $mois_unique = date( '/Y/m/', $timestamp );
    if ( !array_key_exists( $mois_unique, $mois ) ) {
        $annee = date( 'Y', $timestamp );
        $years[$annee] = $annee;
        if ( $year_page == $annee ) {
            $num_mois = date( 'm', $timestamp );
            $months_page[$num_mois] = array(
                'm' => $mois_unique,
                'link' => get_month_link( $annee, $num_mois ),
                'name' => date_i18n( 'F', $timestamp ),
            );

        }
        $mois[$mois_unique] = array( $annee, date( 'm', $timestamp ) );
    }
}

$contenu_more .= '<div class="menu-archives">';
if ( !empty( $years ) ) {
    krsort( $years );
    $contenu_more .= '<ul class="menu-archives--years">';
    foreach ( $years as $year ) {
        $contenu_more .= '<li '.( $year_page == $year ? 'class="current"' :'' ).'><a href="'.get_year_link( $year ).'">'.$year.'</a></li>';
    }
    $contenu_more .= '</ul>';
}
if ( !empty( $months_page ) ) {
    $contenu_more .= '<ul class="menu-archives--months">';
    foreach ( $months_page as $num => $month ) {
        $contenu_more .= '<li '.( $date_page == $month['m'] && is_month() ? 'class="current"' :'' ).'><a href="'.$month['link'].'">'.$month['name'].'</a></li>';
    }
    $contenu_more .= '</ul>';
}
$contenu_more .= '</div>';
