<?php
define('DIRDIR', dirname(__FILE__) . '/wait/');
include DIRDIR . 'inc/header.php';
?><!DOCTYPE HTML>
<html lang="fr-FR">
    <head>
        <title><?php echo $c['nom'] . ' - ' . $c['slogan']; ?></title>
        <meta charset="UTF-8" />
        <meta name="description" content="<?php echo $c['nom'] . ' - ' . strip_tags($c['longslogan']); ?>" />
        <link rel="stylesheet" type="text/css" href="wait/assets/css/cssc-default.css" />
        <link rel="stylesheet" type="text/css" href="wait/assets/css/style.css" />
    </head>
    <!--[if lt IE 9 ]><body class="is_ie8 lt_ie9 lt_ie10"><![endif]-->
    <!--[if IE 9 ]><body class="is_ie9 lt_ie10"><![endif]-->
    <!--[if gt IE 9]><body class="maclass is_ie10"><![endif]-->
    <!--[if !IE]><!--> <body><!--<![endif]-->
        <div class="main-container">
            <h1><img src="wait/assets/img/logo.png" alt="<?php echo $c['nom']; ?>" /></h1>
            <p>
                <strong><?php echo $c['nom']; ?></strong>, <?php echo $c['longslogan']; ?>
            </p>
            <form action="" method="post">
                <?php
                if (!empty($retour_content)) {
                    echo $retour_content;
                } else {
                    ?>
                    <p>Inscrivez-vous à la newsletter afin d'être averti(e) de la sortie du site <strong><?php echo $c['nom']; ?></strong></p>
                    <div>
                        <label for="email_newsletter">Votre email :</label>
                        <input name="email_newsletter" id="email_newsletter" type="email" required />
                        <button type="submit">M'inscrire</button>
                    </div>
                    <div class="hide-me">
                        <label for="url_newsletter">Ne pas toucher</label>
                        <input name="url_newsletter" id="url_newsletter" type="url" value="http://www.colorz.fr/" />
                    </div>
                <?php } ?>
            </form>
            <div class="share">
                <?php if (isset($c['mail']) && !empty($c['mail'])) { ?>
                    <a target="_blank" class="mail" href="mailto:<?php echo $c['mail']; ?>">Nous contacter par mail</a>
                <?php } ?>
                <?php if (isset($c['twitter']) && !empty($c['twitter'])) { ?>
                    <a target="_blank" class="twitter" href="http://twitter.com/<?php echo $c['twitter']; ?>">Suivre @<?php echo $c['twitter']; ?> sur Twitter</a>
                <?php } ?>
                <?php if (isset($c['facebook']) && !empty($c['facebook'])) { ?>
                    <a target="_blank" class="facebook" href="<?php echo $c['facebook']; ?>"><?php echo $c['nom']; ?> sur Facebook</a>
                <?php } ?>
            </div>
        </div>
        <div class="footer">
            <small class="copy">
                <strong><?php echo $c['nom']; ?></strong>
                copyright
                <?php echo date('Y'); ?>
            </small>
            <a target="_blank" href="http://www.colorz.fr" class="colorz">Site par Colorz</a>
        </div>
    </body>
</html>