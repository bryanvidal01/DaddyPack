<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';
include TEMPLATEPATH . '/includes/before-header.php';
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
<head>
<?php echo clrz_get_template_part('header','head'); ?>
</head>
<body <?php body_class(); ?>>
<div class="container">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="row">
                <div class="col-sm-3">
                    <?php echo clrz_get_template_part('','social'); ?>
                </div>
                <div class="col-sm-6 text-center">
                    <a href="<?php echo get_site_url(); ?>" class="logo">
                        DaddyPack
                    </a>
                </div>
                <div class="col-sm-3">
                    <div class="button-nav">
                        <div class="barre"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
