<?php

/* Ajout de boutons personnalisés dans l'admin TinyMCE */

if(is_admin()){
    add_action('init', 'CLRZ_Syntax_Buttons');
    add_action('admin_head', 'CLRZ_Syntax_Buttons_Plus');
}

function CLRZ_Syntax_Buttons_Plus(){
    echo '<input type="hidden" id="clrz-syntax-template-uri" value="'.get_template_directory_uri().'" />';
}

function CLRZ_Syntax_Buttons(){
    global $CLRZ_Syntax_Buttons;
    $CLRZ_Syntax_Buttons = new CLRZ_Syntax_Buttons();
}

class CLRZ_Syntax_Buttons
{
    function CLRZ_Syntax_Buttons(){
        if ( current_user_can('edit_posts') && current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins", array(&$this, "mce_external_plugins"));
            add_filter('mce_buttons', array(&$this, 'mce_buttons'));
        }
    }
    function mce_buttons($buttons) {
        array_push($buttons, "separator", "ClrzFormat1");
        return $buttons;
    }
    function mce_external_plugins($plugin_array) {
        $plugin_array['clrzformats']  =  get_template_directory_uri().'/js/tinymce/clrzformats.js';
        return $plugin_array;
    }
}



