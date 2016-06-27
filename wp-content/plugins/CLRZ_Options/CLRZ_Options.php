<?php
/*
Plugin Name: Clrz Options Client
Plugin URI: http://colorz.fr/#options_client
Description: Permet au client de gérer certaines options sur son site.
Author: Colorz
Version: 1.10.3
Author URI: http://colorz.fr/
*/
define('CLRZ_OPTIONS_PLUGIN_NAME', 'CLRZ Options');
define('CLRZ_OPTIONS_BASENAME_PLUGIN_FILE', plugin_basename(__FILE__));
define('CLRZ_OPTIONS_BASENAMEDIRNAME_FILE', basename(dirname(__FILE__)));
define('CLRZ_OPTIONS_PLUGIN_BASEURL', site_url().'/wp-content/plugins/'. basename(dirname(__FILE__)));
// Niveau minimal
define('CLRZ_OPTIONS_CURRENT_USER_CAN_SEE','upload_files');
// Niveau Admin
define('CLRZ_OPTIONS_CURRENT_USER_CAN','manage_options');

$uploads = wp_upload_dir();
define('CLRZ_OPTIONS_UPLOADBLOGSDIR',$uploads['basedir']);
define('CLRZ_OPTIONS_UPLOADBLOGSURL',$uploads['baseurl']);
define('CLRZ_OPTIONS_ID','clrz_options');

$clrz_options_langs = array(
    'fr' => 'Français',
    'en' => 'English',
);

if(is_admin()) {
    include dirname(__FILE__).'/includes/plugin_admin_stuff.php';
    include dirname(__FILE__).'/options_fields.php';
    include dirname(__FILE__).'/includes/formulaire.php';
    include dirname(__FILE__).'/includes/test_champs.php';
}



/*
    Changelog :
        - 1.10.3 : Fix js accordion Firefox
        - 1.10.2 : Champ de type post_type
        - 1.10.1 : Amélioration de la navigation accordion
        - 1.10 : Field type media
        - 1.9 : Accordion
        - 1.8 : Champs de type "editor" & "page" & refonte légère visuelle admin
        - 1.7.2 : Fix de sécurité sur Import / Export des données
        - 1.7.1 : Fix de sécurité sur Import / Export des données
        - 1.7 : Import / Export des données
        - 1.6.2 : Menu toolbar
        - 1.6.1.1 : Maj esthétique : input plus grands & icône sur la page d'options
        - 1.6.1 : Rangement du plugin et ajout de plusieurs constantes.
        - 1.6 : Gestion des champs multilingues
        - 1.5.7 : Amélioration des performances en regroupant les fichiers juste pour admin
        - 1.5.6 : Test "Simpletext"
        - 1.5.5 : Messages de succes, d'erreur, et debugs.
        - 1.5 : Gestion des niveaux d'utilisateurs
        - 1.4 : Gestion des uploads de fichiers
*/

