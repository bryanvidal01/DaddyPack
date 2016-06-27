<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php'; ?>
<article class="post loop-short" id="post-<?php the_ID(); ?>">
    <header>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="post-info">
            <?php edit_post_link('[Modifier]', ''); ?>
            <?php echo __('Post&eacute; le','clrz_lang'); ?> <time pubdate datetime="<?php the_time(DATE_W3C); ?>"><?php the_time('l j F Y, \&\a\g\r\a\v\e\; H:i') ?></time>
            <?php echo __('par','clrz_lang'); ?> <?php the_author_posts_link() ?>
            <?php if(function_exists('the_clrz_likez')) the_clrz_likez(get_the_ID(), $html = true); ?>
        </div>
    </header>
    <div class="post-content">
        <?php the_excerpt(); ?>
    </div>
    <footer>
        <p><?php comments_popup_link('aucun commentaire', '1 commentaire', '% commentaires'); ?></p>
    </footer>
</article><!-- .post -->