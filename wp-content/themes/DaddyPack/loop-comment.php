<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php'; ?>
<div style="margin-left:<?php echo $level*50; ?>px" id="comment-<?php echo $comment->comment_ID; ?>" <?php comment_class('level'.$level); ?>>
    <span class="comment_avatar">
        <?php echo get_avatar($comment->comment_author_email,44); ?>
    </span>
    <div class="comment_main">
        <div class="comment_meta">
            <?php echo __('Par','clrz_lang');  ?>
            <?php comment_author_link($comment->comment_ID);?>
            &bull;
            <a href="<?php echo get_permalink(); ?>#comment-<?php echo $comment->comment_ID; ?>">
                <?php comment_date('',$comment->comment_ID); ?>
            </a>
            <?php if(($level+1) < get_option('thread_comments_depth') && get_option('thread_comments') == '1') { ?>
            / <a class="clrz_click_comments_form" href="#comments-form" rel="<?php echo $comment->comment_ID; ?>">
                <?php echo __('Répondre','clrz_lang'); ?>
            </a>
            <?php } ?>
        </div>
        <div class="comment-content">
            <?php comment_text($comment->comment_ID); ?>
        </div>
    </div>
</div>