<?php
include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';
include TEMPLATEPATH . '/includes/before-header.php';
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <title><?php echo $clrz_page_title; ?></title>
        <meta name="viewport" content="width=960" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1" />
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        <link rel="alternate" type="application/rss+xml" href="<?php echo get_bloginfo('rss2_url'); ?>" title="<?php echo esc_html(get_bloginfo('name'), 1); ?> - Flux RSS des articles" />
        <link rel="alternate" type="application/rss+xml" href="<?php echo get_bloginfo('comments_rss2_url'); ?>" title="<?php echo esc_html(get_bloginfo('name'), 1); ?> - Flux RSS des commentaires" />
        <link rel="icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" type="image/ico" />
        <link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/images/apple-touch-icon.png">
        <!--[if lt IE 9]><script src="<?php bloginfo('template_url'); ?>/js/html5.js"></script><![endif]-->
        <?php if (!defined('CLRZ_MINIFY_ACTIVE')) : ?>
            <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.php" type="text/css" />
        <?php endif; ?>
        <?php include TEMPLATEPATH . '/tpl/metas/tpl_metas.php'; ?>
        <?php if (get_option('clrz_google_site_verification') != '')
            echo '<meta name="google-site-verification" content="' . get_option('clrz_google_site_verification') . '"/>' . "\n"; ?>

        <?php wp_head(); ?>
        <!--[if (gte IE 6)&(lte IE 8)]><script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/selectivizr.js?ver=1.0.2"></script><![endif]-->
    </head>

<!--[if lt IE 7 ]><body <?php  body_class('is_ie6 lt_ie7 lt_ie8 lt_ie9 lt_ie10'); ?>><![endif]-->
<!--[if IE 7 ]><body <?php     body_class('is_ie7 lt_ie8 lt_ie9 lt_ie10'); ?>><![endif]-->
<!--[if IE 8 ]><body <?php     body_class('is_ie8 lt_ie9 lt_ie10'); ?>><![endif]-->
<!--[if IE 9 ]><body <?php     body_class('is_ie9 lt_ie10'); ?>><![endif]-->
<!--[if gt IE 9]><body <?php   body_class('is_ie10'); ?>><![endif]-->
<!--[if !IE]><!--> <body <?php body_class(); ?>><!--<![endif]-->
<script>
    clrz_wp_template_url = "<?php bloginfo('template_url'); ?>";
    clrz_wp_site_url = "<?php echo site_url(); ?>";
</script>
<nav class="no-js">
    <ul>
        <li><a href="<?php bloginfo('url'); ?>" rel="home">Accueil</a></li>
        <?php if(defined('CONTACT_PAGEID')) { ?><li><a href="<?php echo get_permalink(CONTACT_PAGEID); ?>">Contact</a></li><?php } ?>
        <li><a href="#ariane-1">Fil d'ariane</a></li>
        <li><a href="#content">Contenu</a></li>
        <li><a href="#sidebar">Sidebar</a></li>
        <li><a href="#footer">Pied de page</a></li>
    </ul>
</nav>
<header role="banner">
    <h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
    <p><?php bloginfo('description'); ?></p>
    
    <ul>
    <?php 
    global $clrz_core;
    if(is_user_logged_in()): ?>
        <li><a href="<?php echo $clrz_core->_getUrl('profil');?>">Mon profil</a></li>
        <li><a href="<?php echo $clrz_core->_getUrl('logout');?>">Se déconnecter</a></li>
    <?php else: ?>
        <li><a href="<?php echo $clrz_core->_getUrl('login');?>">Connexion</a></li>
        <li><a href="<?php echo $clrz_core->_getUrl('register');?>">Inscription</a></li>
    <?php endif;?>
    </ul>
    
</header>
<div id="main">
<?php include (TEMPLATEPATH . '/tpl/tpl_ariane.php'); ?>
<?php echo $clrz_core->showMessages();?>
