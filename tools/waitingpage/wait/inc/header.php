<?php
include DIRDIR.'inc/config.php';
@session_start();
if (empty($_SESSION['email_newsletter'])) {
    $_SESSION['email_newsletter'] = '';
}

$file_csv = DIRDIR . 'data/mails.csv';

$retour_content = '';

if (isset($_POST['email_newsletter'])) {
    $erreurs = array();

    if (empty($_POST['email_newsletter']) || !filter_var($_POST['email_newsletter'], FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'Votre e-mail est invalide';
    }
    if (!isset($_POST['url_newsletter']) || $_POST['url_newsletter'] != 'http://www.colorz.fr/') {
        $erreurs[] = 'Vous avez touché au champ anti-spam.';
    }

    if (!file_exists($file_csv)) {
        @chmod(DIRDIR . 'data/', 0755);
        file_put_contents($file_csv, '');
    }

    $liste_mails = array();
    $liste_mails_file = file_get_contents($file_csv);
    $liste_mails_arr = explode("\n", $liste_mails_file);
    foreach ($liste_mails_arr as $mail) {
        $mailtmp = explode(';', $mail);
        if (isset($mailtmp[0]))
            $liste_mails[] = $mailtmp[0];
    }

    if ($_POST['email_newsletter'] == $_SESSION['email_newsletter'] || in_array($_POST['email_newsletter'], $liste_mails)) {
        $erreurs[] = 'Vous êtes déjà inscrit(e) !';
    }


    if (empty($erreurs)) {
        $_SESSION['email_newsletter'] = $_POST['email_newsletter'];
        $retour_content = '<p>Merci de votre inscription, et à bientôt !</p>';
        @chmod(DIRDIR . 'data/', 0755);
        @file_put_contents($file_csv, $_POST['email_newsletter'] . ';' . date(DATE_W3C) . ';' . "\n", FILE_APPEND | LOCK_EX);
    } else {
        $retour_content = '<p><strong>Attention :</strong><br /> ' . implode($erreurs, '<br />') . '</p>';
    }
}