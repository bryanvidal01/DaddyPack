<?php

// Désactive les widgets par défaut dans l'admin Wordpress
add_action('widgets_init', 'unregister_default_wp_widgets');

function unregister_default_wp_widgets() {
//    unregister_widget('WP_Widget_Categories');
//    unregister_widget('WP_Widget_Recent_Posts');
//    unregister_widget('WP_Widget_Search');
//    unregister_widget('WP_Widget_Tag_Cloud');
//    unregister_widget('WP_Widget_Meta');
//    unregister_widget('WP_Widget_Pages');
//    unregister_widget('WP_Widget_Calendar');
//    unregister_widget('WP_Widget_Archives');
//    unregister_widget('WP_Widget_Links');
//    unregister_widget('WP_Widget_Recent_Comments');
//    unregister_widget('WP_Widget_RSS');
//    unregister_widget('WP_Widget_Text');
//    unregister_widget('WP_Nav_Menu_Widget');
}

// Supprime des menus inutiles dans l'admin Wordpress
add_action('admin_menu', 'remove_menus');

function remove_menus() {
    global $menu;
    $restricted = array();
    // $restricted = array(__('Dashboard'), __('Posts'), __('Media'), __('Links'), __('Pages'), __('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'));
    end($menu);
    while (prev($menu)) {
        $value = explode(' ', $menu[key($menu)][0]);
        if (in_array($value[0] != NULL ? $value[0] : "", $restricted)) {
            unset($menu[key($menu)]);
        }
    }
}

if (!is_admin()) {
    // Supprime le script d'internationalisation : non utilisé par défaut.
    add_action('init', 'remove_l1on');
    function remove_l1on() {
        wp_deregister_script('l10n');
    }
}


// Supprime la version de Wordpress affichée dans le head
remove_action('wp_head', 'wp_generator');


// Suppression du Style Inline pour le Widget "Recent Comments"
add_action('widgets_init', 'clrz_remove_recent_comments_style');

function clrz_remove_recent_comments_style() {
    global $wp_widget_factory;
    if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments']))
        remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}

// N'active le RSS que pour les post-types post
function fb_disable_feed() {
    global $post;
    if (!is_object($post) || !isset($post->post_type) || !in_array($post->post_type, array('post'))) {
        wp_die('No feed available, please visit our <a href="' . site_url() . '">homepage</a>!');
    }
}

add_action('do_feed', 'fb_disable_feed', 1);
add_action('do_feed_rdf', 'fb_disable_feed', 1);
add_action('do_feed_rss', 'fb_disable_feed', 1);
add_action('do_feed_rss2', 'fb_disable_feed', 1);
add_action('do_feed_atom', 'fb_disable_feed', 1);

// Suppression du remplacement de caractères
// tels que les quotes dans les contenus.
/*
remove_filter('category_description', 'wptexturize');
remove_filter('list_cats', 'wptexturize');
remove_filter('comment_author', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_filter('single_post_title', 'wptexturize');
remove_filter('the_title', 'wptexturize');
remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
*/

// Disable auto update
// see http://codex.wordpress.org/Disabling_Automatic_Background_Updates#All_Updates
function clrz_automatic_updater_disabled() {
    return true;
}

add_filter( 'automatic_updater_disabled', 'clrz_automatic_updater_disabled' );

// Enable the WP Link Manager (blogroll), since WordPress 3.5
// add_filter( 'pre_option_link_manager_enabled', '__return_true' );