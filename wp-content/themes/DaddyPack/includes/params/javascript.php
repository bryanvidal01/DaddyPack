<?php

// Chargement des JS
add_action( 'wp_enqueue_scripts', 'clrz_script_init' );

function clrz_script_init() {
    if ( !is_admin() ) {
        $js_dir = get_template_directory_uri() . '/js';
        // Frameworks
        wp_enqueue_script( 'mootools-core', $js_dir . '/framework/mootools-core.js', '', '1.4.5', false );
        wp_enqueue_script( 'mootools-more', $js_dir . '/framework/mootools-more-1.4.0.1.js', '', '1.4.0.1', false );

        $js_mooplug_dir = $js_dir . '/plugins/mootools';
        // Plugins
        wp_enqueue_script( 'fake-select', $js_mooplug_dir . '/fake-select/fake-select.js', '', '1.4.1', false );
        wp_enqueue_script( 'fake-placeholder', $js_mooplug_dir . '/fake-placeholder/fake-placeholder.js', '', '1.1', false );
        wp_enqueue_script( 'fake-inputbox', $js_mooplug_dir . '/fake-inputbox/fake-inputbox.js', '', '1.0', false );

        // Events
        wp_enqueue_script( 'functions', $js_dir . '/functions.js', '', '1.0', false );
        wp_enqueue_script( 'events', $js_dir . '/events.js', '', '1.0', false );
    }
}
