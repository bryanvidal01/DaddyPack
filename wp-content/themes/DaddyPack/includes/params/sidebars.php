<?php

$clrz_sidebars = array(
    // Sidebar Widget par défaut.
    // Dupliquer ce code pour crééer des sidebars.
    // Ne pas la supprimer/renommer : appelée par défaut dans sidebar.php
    'clrz_defaultsidebar' => array(
        'name' => 'DefaultSideBar',
        'description' => 'Sidebar par défaut',
    ),
    /*
    'clrz_othersidebar' => array(
        'name' => 'OtherSidebar',
        'description' => 'Other Sidebar',
    ),
    */
);

/* Arguments ajoutés par défaut */
$clrz_default_sidebar_args = array(
    'before_widget' => '<li><div class="widget %2$s">',
    'after_widget' => '</div></li>',
    'before_title' => '<h3>',
    'after_title' => '</h3>'
);




/* ----------------------------------------------------------
   U CAN'T TOUCH THIS
   ------------------------------------------------------- */

// Activation des sidebars widgetisées
if ( function_exists('register_sidebar') ){
    foreach($clrz_sidebars as $id => $sidebar){
        $sidebar_add = $sidebar;
        $sidebar_add['id'] = $id;
        foreach($clrz_default_sidebar_args as $idd => $arg){
            if(!isset($sidebar_add[$idd])){
                $sidebar_add[$idd] = $arg;
            }
        }
        register_sidebar($sidebar_add);
    }
}
