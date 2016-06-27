<meta charset="<?php bloginfo('charset'); ?>" />
<title><?php wp_title(' | '); ?></title>
<?php
echo clrz_get_template_part('header/head','metas');
wp_head();
?>

<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
<link rel="icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" type="image/ico" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/style.css" media="screen" title="no title" charset="utf-8">
<link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/images/apple-touch-icon.png" />
<meta content='width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no' name='viewport' />
<meta charset="UTF-8" />
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="chrome=1" /><![endif]-->
<!--[if lt IE 9]><script src="<?php bloginfo('template_url'); ?>/js/ie.js"></script><![endif]-->
<script>
var clrz_wp_template_url="<?php bloginfo('template_url'); ?>",
    clrz_wp_site_url="<?php echo site_url(); ?>";
</script>
