<?php

add_filter('clrz_groupes_options', 'this_clrz_groupes_options');
add_filter('clrz_champs_options_clients', 'this_clrz_champs_options_clients');

function this_clrz_groupes_options($retour=array()) {
    $retour['social_networks'] = __('Réseaux sociaux','clrz_lang');
    $retour['contact_methods'] = __('Méthodes de contact','clrz_lang');
    $retour['pages_id'] = __('Correspondances de pages','clrz_lang');
    $retour['config_site'] = __('Configuration du site','clrz_lang');
    return $retour;
}

function this_clrz_champs_options_clients($retour=array()) {
    global $clrz_champs_options_clients;

    /* Réseaux sociaux */
    $retour['client_options_twitter'] = array('label' => 'URL Twitter', 'defaut' => 'http://twitter.com/Colorz', 'group' => 'social_networks');
    $retour['client_options_twitter_user'] = array('label' => '@User Twitter', 'defaut' => 'Colorz', 'group' => 'social_networks');
    $retour['client_options_facebook'] = array('label' => 'URL Facebook', 'defaut' => 'http://facebook.com/WeAreColorz', 'group' => 'social_networks');
    $retour['client_options_facebook2'] = array('label' => 'URL Facebook', 'defaut' => 'http://facebook.com/WeAreColorz', 'group' => 'social_networks');

    /* Méthodes de contact */
    $retour['client_options_telephone'] = array('label' => 'Téléphone', 'defaut' => '', 'group' => 'contact_methods');
    $retour['client_mail_contact'] = array('label' => 'Mail de contact', 'typehtml' => 'text', 'group' => 'contact_methods');

    /* Correspondances de pages */
    $retour['clrz_define_contact_pageid'] = array('label' => 'Page contact', 'typehtml' => 'page', 'group' => 'pages_id');
    $retour['clrz_define_mentionslegales_pageid'] = array('label' => 'Page mentions légales', 'typehtml' => 'page', 'group' => 'pages_id');
    $retour['clrz_define_plandusite_pageid'] = array('label' => 'Page Plan du site', "typehtml" => 'page', 'group' => 'pages_id');
    $retour['clrz_define_webservice_pageid'] = array('label' => 'Page Webservice', "typehtml" => 'page', 'group' => 'pages_id');

    /* Configuration du site */
    $retour['clrz_options_code_google_analytics'] = array( 'label' => 'Code Google Analytics', 'typehtml' =>'text','typetest' =>'text', 'defaut' => 'UA-XXXXX-X', 'group' => 'config_site','niveau'=>'upload_files');
    $retour['clrz_google_site_verification'] = array( 'label' => 'Code Google Site Verification', 'typehtml' =>'text','typetest' =>'text', 'defaut' => '', 'group' => 'config_site');

    return array_merge($retour, $clrz_champs_options_clients);
}

