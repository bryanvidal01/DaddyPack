<?php

add_action('admin_menu', 'my_clrz_options_client');
function my_clrz_options_client() {
    global $clrz_champs_options_clients;
    if(current_user_can(CLRZ_OPTIONS_CURRENT_USER_CAN_SEE)){
        foreach($clrz_champs_options_clients as $id_champ => $champ){
            add_option($id_champ, $champ['defaut'], '', 'yes');
        }
        $hook = add_dashboard_page('Options', CLRZ_OPTIONS_PLUGIN_NAME, CLRZ_OPTIONS_CURRENT_USER_CAN_SEE, CLRZ_OPTIONS_ID, 'my_plugin_clrz_options_client');
        add_action("admin_head-".$hook, 'clrz_options_assets' );
    }
}

add_action('admin_bar_menu', 'add_toolbar_items', 100);
function add_toolbar_items($admin_bar){
	$admin_bar->add_menu( array(
		'id'    => 'link-clrz-options-admin-bar',
		'title' => '‣ '.CLRZ_OPTIONS_PLUGIN_NAME,
		'href'  => admin_url( 'index.php?page='.CLRZ_OPTIONS_ID ),
		'meta'  => array(
			'title' => CLRZ_OPTIONS_PLUGIN_NAME,
		),
	));
}

add_filter('plugin_action_links', 'my_clrz_options_plugin_action', 10, 2);
function my_clrz_options_plugin_action( $links, $file ) {
    if ( $file == CLRZ_OPTIONS_BASENAME_PLUGIN_FILE && current_user_can(CLRZ_OPTIONS_CURRENT_USER_CAN_SEE))
        $links[] = '<a href="' . admin_url( 'index.php?page='.CLRZ_OPTIONS_ID ) . '">'.__('Settings').'</a>';
    return $links;
}

function my_clrz_options_client_top_error(){
    do_action('my_clrz_options_client_top_error');
}

function my_clrz_options_client_top(){
    do_action('my_clrz_options_client_top');
}

function success_clrz_options_client(){
    echo "<div id='conf_message_clrz_options'>";
    if(isset($_SESSION['successclrzoption'])){
        echo '<ul>';
        foreach($_SESSION['successclrzoption'] as $error=>$val):
            echo '<li>'.$val.'</li>';
        endforeach;
        echo '</ul>';
        unset($_SESSION['successclrzoption']);
    }
    echo '</div>';
}

function error_clrz_options_client(){
    echo "<div id='errorlist'><p id='conf_message_clrz_options_error'>Des erreurs dans l'enregistrement des données :</p>";
    if(isset($_SESSION['errorclrzoption'])){
        echo '<ul>';
        foreach($_SESSION['errorclrzoption'] as $error=>$val):
            echo '<li>'.$val.'</li>';
        endforeach;
        echo '</ul>';
        unset($_SESSION['errorclrzoption']);
    }
    echo '</div>';
}

function clrz_options_assets() {

    if(isset($_GET['page']) && $_GET['page'] == CLRZ_OPTIONS_ID){
    wp_enqueue_media();
        echo "<link href='".CLRZ_OPTIONS_PLUGIN_BASEURL . '/assets/clrz-options.css'."' rel='stylesheet' type='text/css' />";
        echo "<script src='".CLRZ_OPTIONS_PLUGIN_BASEURL . '/assets/clrz-options.js'."'></script>";
    }
}
