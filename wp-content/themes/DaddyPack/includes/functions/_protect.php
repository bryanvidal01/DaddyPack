<?php

define('CLRZ_PROTECT_ID_ADMIN', 1);

// show admin bar only for admins and editors
if (!current_user_can('edit_posts')) {
	add_filter('show_admin_bar', '__return_false');
}

if(is_admin()){
    // On retire l'accès aux bas-niveaux d'users à WP-ADMIN
    add_action('admin_init', 'clrz_admin_init');
    function clrz_admin_init() {
        global $userdata;
        if (!current_user_can('edit_posts')) {
            wp_redirect('/');
            die;
        }
    }

    // Empeche le client de switcher de thèmes
    add_action('admin_init', 'clrz_cwc_lock_theme');
    function clrz_cwc_lock_theme() {
        global $submenu, $userdata;
        get_currentuserinfo();
        if (isset($userdata->ID) && $userdata->ID != CLRZ_PROTECT_ID_ADMIN) {
            unset($submenu['themes.php'][5]);
            unset($submenu['themes.php'][15]);
        }
    }

    // On empeche l'édition de fichiers du thème & des plugins
    add_action('init', 'clrz_init_constantes');
    function clrz_init_constantes() {
        global $userdata;
        if (isset($userdata->ID) && $userdata->ID != CLRZ_PROTECT_ID_ADMIN) {
            if (!defined('DISALLOW_FILE_EDIT'))
                define('DISALLOW_FILE_EDIT', true);
            if (!defined('DISALLOW_FILE_MODS'))
                define('DISALLOW_FILE_MODS', true);
        }
    }

    // Supprime l'update nag
    add_action('init', 'clrz_remove_update_nag');
    function clrz_remove_update_nag() {
        global $userdata;
        if (isset($userdata->ID) && $userdata->ID != CLRZ_PROTECT_ID_ADMIN) {
            add_action('init', create_function('$a', "remove_action( 'init', 'wp_version_check' );"), 2);
            add_filter('pre_option_update_core', create_function('$a', "return null;"));
        }
    }
    add_action('admin_head','clrz_remove_button_version_message');
    function clrz_remove_button_version_message() {
        global $userdata;
        if (isset($userdata->ID) && $userdata->ID != CLRZ_PROTECT_ID_ADMIN) {
            echo '<style>#wp-version-message{display:none}</style>';
        }
    }

    // Empeche le client d'accéder au menu d'update nag
    add_action('admin_menu', 'clrz_remove_submenus');
    function clrz_remove_submenus() {
        global $submenu, $userdata;
        if (isset($userdata->ID) && $userdata->ID != CLRZ_PROTECT_ID_ADMIN) {
            unset($submenu['index.php'][10]);
            remove_action( 'admin_notices', 'update_nag', 3 );
            if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'update-core.php')) {
                wp_redirect(site_url() . '/wp-admin/');
                die;
            }
        }
    }
}
