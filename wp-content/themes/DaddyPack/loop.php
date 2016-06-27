<?php
include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';
$posttags = get_the_tags();
$postcategories = wp_get_post_categories(get_the_ID());
?>
<article class="post" id="post-<?php the_ID(); ?>">
    <header>
        <h2><?php the_title(); ?></h2>
        <div class="post-info">
            <?php echo __('Post&eacute; le','clrz_lang'); ?> <time pubdate datetime="<?php the_time(DATE_W3C); ?>"><?php the_time('l j F Y, \&\a\g\r\a\v\e\; H:i') ?></time>
            <?php echo __('par','clrz_lang'); ?> <?php the_author() ?>
        </div>
    </header>
    <div class="post-content">
        <?php the_content(); ?>
        <?php edit_post_link('Modifier cet article', '<p>', '</p>'); ?>
    </div>
    <footer>
        <?php if(!empty($posttags)) : ?>
        <p><strong><?php echo __('Tags','clrz_lang'); ?></strong> : <?php the_tags('',', '); ?></p>
        <?php endif; ?>
        <?php if(!empty($postcategories)): ?>
        <p><strong><?php echo __('Catégories','clrz_lang'); ?></strong> : <?php the_category(' ',', '); ?></p>
        <?php endif; ?>
    </footer>
</article><!-- .post -->