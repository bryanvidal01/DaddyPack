<?php

/*
 * FONCTIONNALITES
 *
 */

// Ajoute le nombre de membres dans le Widget Dashboard "Right Now"
add_action( 'right_now_content_table_end', 'clrz_dashboard_wps_user_count');
function clrz_dashboard_wps_user_count() {
    global $wpdb;
    $users = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users"); ?>
<table>
    <tbody>
       <tr class="first">
          <td class="first b b_pages"><a href="users.php"><?php echo $users; ?></a></td>
          <td class="t pages"><a href="users.php"><?php echo __('Users'); ?></a></td>
       </tr>
     </tbody>
</table>
<?php
}

/*
 * MENAGE
 *
 */

// Suppression des Widgets inutiles dans le Dashboard
add_action('wp_dashboard_setup', 'clrz_remove_dashboard_widgets');
function clrz_remove_dashboard_widgets() {
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}


/*
 *  ESTHETIQUE
 *
 */

// Change le logo du login
add_action('login_head', 'clrz_custom_login_logo');
function clrz_custom_login_logo() {
    echo '<style type="text/css">
        .login h1 a { background-image:url('.get_bloginfo('template_directory').'/images/admin/custom-login-logo.png) !important;background-size:contain!important;width:100%; }
    </style>';
}

// Change login page logo URL
add_filter("login_headerurl", "clrz_custom_login_link");
function clrz_custom_login_link($url) {
    return site_url();
}

// Change login page logo txt
add_filter("login_headertitle", "clrz_custom_login_title");
function clrz_custom_login_title($message) {
    return get_bloginfo('name');
}

