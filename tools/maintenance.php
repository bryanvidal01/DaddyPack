<?php
/*
$ip_whitelist = array('80.12.81.16');
if(!in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)){
    include dirname(__FILE__).'/maintenance.php';
    die;
}
*/

define('NOM_MAINTENANCE_SITE','Skeletorz');
$retry_after = 2700;
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: '. $retry_after);
header('X-Powered-By:');
 ?><!DOCTYPE HTML>
<html lang="fr-FR">
    <head>
    <meta charset="UTF-8" />
    <title><?php echo NOM_MAINTENANCE_SITE; ?> — Maintenance</title>
    <style>
html {
    position: relative;
    height: 100%;
}

body {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 600px;
    height: 100px;
    margin-top: -50px;
    margin-left: -300px;
    text-align: center;
    font: 16px/1.5 sans-serif;
}
    </style>
    </head>
    <body>
        <p>Nous sommes désolés, mais le site <strong><?php echo NOM_MAINTENANCE_SITE; ?></strong> est en maintenance.<br />Veuillez réessayer d'ici <?php echo round($retry_after/60); ?> minutes.</p>
    </body>
</html>