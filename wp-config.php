<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'DaddyPack');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'root');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '! Dr6-++p>F=<3P-rR^]w7xjfJ$fB^/H)rz{<.7X^QSj8z1b~J~>=r&]6%6=d+4+');
define('SECURE_AUTH_KEY',  '}?)pZ/dx(E|4rh`-})H[G1|=hE|.ooA$-Wq3q7-IMpuxK.5;]lUAzLv>jo`(3p%.');
define('LOGGED_IN_KEY',    'yAUnxwpUc(}O|dA%(Y*GB(-jtYaM}_6RV<oxU?xP[brY]NZH{F@pp4VM.=|+9S7^');
define('NONCE_KEY',        '3-td|-_YZx+5WxZ!w1!7d:G5!a)uT6HE{%4RkO|ei-&bR{MAk>`4Aji;`t5O+xi!');
define('AUTH_SALT',        ' 7x9._1jgu`IJ&1Ys4HLAe&bvT@XSpn9O-]5+,vm5^Pf-2%~#*P`?}}?7ez ^5Q:');
define('SECURE_AUTH_SALT', 'Tb$-fm3a?hz0 +I6>:L6]t~G2n7Sec??/PKRJr6LUqtaqEAny9}hsq.U9L#p||nk');
define('LOGGED_IN_SALT',   '>+lNzeff5h!*k]o(?Y/-, ^MpR:|`m4+[k2<o+1/(-MHGze(1!-Sy0:XxeM-5*P?');
define('NONCE_SALT',       'mENxqys;Q#GW~HSUkC#C$J=a^P+O<V5(d-!_!i4S3d)%jSv-1`N1[Z5L0Rc6LDAQ');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');