<?php
// Cette page empêche le chargement direct d'un fichier du thème.
// Auquel cas, une 404 est affichée.
//
// by Jacques
if (!defined('ABSPATH')) {
    define('CLRZ_CHECK_IN_WP','lapin');
    header('HTTP/1.1 404 Not Found');
    include dirname(__FILE__).'/../../../wp-load.php';
    include dirname(__FILE__).'/404.php';
    exit;
}
