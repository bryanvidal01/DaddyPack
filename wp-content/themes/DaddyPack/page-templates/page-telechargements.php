<?php /* Template Name: Telechargements */
include dirname(__FILE__) . '/../includes/clrz-check-in-wp.php';
get_header();
?>
<div class="cssn-lay">
    <div class="col-main" id="content">
        <?php 
        if (have_posts ()) : while (have_posts ()) : the_post();
        get_template_part( 'loop' );
        $attachments = get_children(array(
            'post_parent' => get_the_ID(),
            'orderby' => 'date',
            'post_status' => 'inherit',
            'post_type' => 'attachment'
            ));
        if (!empty($attachments)){
            echo '<ul class="liste-telechargements">';
            foreach ( $attachments as $id => $attachment ) { ?>
            <li class="telechargement file-<?php echo $attachment->ID; ?> file-<?php echo $attachment->post_name ?>">
                <h3><?php echo $attachment->post_title; ?></h3>
                <p>
                    <?php echo $attachment->post_content; ?> -
                    <a href="<?php echo $attachment->guid; ?>" title="<?php echo __('Fichier', 'clrz_lang'); ?> <?php echo $attachment->post_mime_type; ?>"><?php echo __('T&eacute;l&eacute;charger ce fichier', 'clrz_lang'); ?></a>
                </p>
            </li>
            <?php }
            echo '</ul>';
        }
        endwhile; endif;
        ?>
    </div>
    <div class="col-side">
        <?php get_sidebar(); ?>
    </div>
</div>
<?php
get_footer();