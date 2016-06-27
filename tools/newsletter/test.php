<?php

$nom_mail = 'Test Mail';
$adresses_destination = array();
$adresses_destination[] = 'kevin@colorz.fr';
//$adresses_destination[] = 'colorz.kevin@gmail.com';
//$adresses_destination[] = 'colorz.kevin@outlook.fr';

// Ne pas toucher ci-dessous.
$entete = "MIME-Version: 1.0\r\n";
$entete .= "Content-type: text/html; charset=utf-8\r\n";
mail(implode(', ', $adresses_destination), $nom_mail . time(), file_get_contents('index.html'), $entete);