<?php
include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php';
get_header();
?>
<div class="cssn-lay">
    <div class="col-main" id="content">
<?php
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        the_title();
    }
    include TEMPLATEPATH.'/tpl/tpl_pagination.php';
}
?>
    </div>
    <div class="col-side">
        <?php get_sidebar(); ?>
    </div>
</div>
<?php
get_footer();
