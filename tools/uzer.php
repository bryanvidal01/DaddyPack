<?php

// Ce fichier permet de créer un utilisateur admin
// Merci de le supprimer



// Supprimer la ligne exit pour activer le fichier
exit('/');

$new_uzer_username = 'kekevdu94';
$new_uzer_password = 'kekevdu94';
$new_uzer_email = 'kevin+'.time().'@colorz.fr';


include dirname(__FILE__) . '/wp-load.php';
require_once( ABSPATH . WPINC . '/registration.php');

if(username_exists($new_uzer_username)){
    echo 'Cet utilisateur existe deja.';
    exit();
}

$id_user = wp_create_user($new_uzer_username, $new_uzer_password, $new_uzer_email);

if(is_object($id_user)){
    echo 'Echec de la creation d\'utilisateur';
    exit();
}

global $wpdb;
$wpdb->update(
    'wp_usermeta',
    array('meta_value' => 'a:1:{s:13:"administrator";s:1:"1";}'),
    array('user_id' => $id_user, 'meta_key' => 'wp_capabilities')
);

$wpdb->update(
    'wp_usermeta',
    array('meta_value' => '10'),
    array('user_id' => $id_user, 'meta_key' => 'wp_user_level')
);

echo 'Succes de la creation d\'utilisateur. N\'oubliez pas de supprimer ce fichier.';
