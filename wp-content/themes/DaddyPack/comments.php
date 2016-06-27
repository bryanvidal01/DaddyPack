<?php

/* ----------------------------------------------------------
   Config
   ------------------------------------------------------- */

$thread_comments = (get_option('thread_comments') == '1');
$afficher_trackbacks = 1;

/* ----------------------------------------------------------
   Template de commentaire
   ------------------------------------------------------- */

function show_comment($comment, $commentaires, $level=0){
	global $post;
	// Loop Comment
	include TEMPLATEPATH . '/loop-comment.php';
	// Affichage des commentaires enfants
	foreach($commentaires as $comment2) :
		if($comment2->comment_parent == $comment->comment_ID)
			show_comment($comment2,$commentaires,$level+1);
	endforeach;
}


/* ----------------------------------------------------------
   Core
   ------------------------------------------------------- */

// Prévient lors du chargement direct de la page
include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

// Si un mot de passe est requis
if (!empty($post->post_password)) :
	if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) :
		echo '<p>' . __('Cet article est prot&eacute;g&eacute; par un mot de passe.', 'clrz_lang') . '</p>';
	endif;
endif;

$comments = get_comments(array('status' => 'approve', 'order' => 'ASC', 'post_id' => get_the_ID()));
$commentaires = array();

$trackbacks = array();

foreach ($comments as $comment) {
	if (get_comment_type() == 'comment') $commentaires[] = $comment;
	else $trackbacks[] = $comment; // trackback || pingback
}

$nb_commentaires = count($commentaires);
$nb_trackbacks = count($trackbacks);

/* ----------------------------------------------------------
   Loop Commentaires
   ------------------------------------------------------- */

?><header class="header-comments" id="comments">
    <?php if ( $nb_commentaires > 0 && comments_open() ): ?>
    <a class="hcom-add" href="#comments-form"><?php echo __( 'Ajouter le votre', 'clrz_lang' ); ?></a>
    <?php endif; ?>
    <h3 class="hcom-title"><?php echo ( $nb_commentaires > 0 ? $nb_commentaires:'Aucun' ).' '.__( 'commentaire', 'clrz_lang' ).( $nb_commentaires > 1 ? 's':'' ); ?></h3>
</header><?php

if ( $nb_commentaires > 0 ) {
    echo '<div class="list-comments">';
    foreach ( $commentaires as $comment ) {
        if ( $comment->comment_parent == 0 ) {
            show_comment( $comment, $commentaires );
        }
    }
    echo '</div>';
}

/* ----------------------------------------------------------
   Comments Form
   ------------------------------------------------------- */

if(isset($_GET['thank']) && $_GET['thank'] = 'you'){
    echo '<p class="success-comment">'.__('Merci ! Votre commentaire a été posté avec succès, il peut mettre quelques instants à apparaître.','clrz_lang').'</p>';
}

if (comments_open()) { ?>
<h3>
	<span id="add_your_comment"><?php echo __('Ajouter votre commentaire', 'clrz_lang'); ?></span>
	<?php if($thread_comments): ?>
	<a id="dont_reply_to_comment" href="#">&cross;</a>
	<?php endif; ?>
</h3>
<form class="clrz_form" id="comments-form" action="<?php bloginfo('url'); ?>/wp-comments-post.php" method="post">
  <input name="redirect_to" type="hidden" value="<?php the_permalink(); ?>?thank=you" />
	<?php if($thread_comments): ?>
    <input type="hidden" id="txt_add_your_comment" value="<?php echo __('Ajouter votre commentaire', 'clrz_lang'); ?>" />
    <input type="hidden" id="txt_reply_to_comment" value="<?php echo __('R&eacute;pondre &agrave; ce commentaire', 'clrz_lang'); ?>" />
	<?php endif; ?>
    <ul class="cssc-form float-form">
    <?php if(is_user_logged_in()) : ?>
    <li class="box">
    	<span>
    		<?php echo __('Connect&eacute; en tant que ','clrz_lang'); ?>
    		<strong><?php echo $user_identity; ?></strong>.
    		<a href="<?php echo site_url(); ?>/wp-login.php?action=logout" title="<?php echo __('Se d&eacute;connecter','clrz_lang'); ?>">
    			<?php echo __('Se d&eacute;connecter','clrz_lang'); ?> &raquo;
    		</a>
    	</span>
    </li>
    <?php else : ?>
    <li class="box">
        <label for="form_comment_author"><?php echo __('Pseudo','clrz_lang'); ?> <span>*</span></label>
        <input id="form_comment_author" name="author" type="text" aria-required="true" required/>
    </li>
    <li class="box">
        <label for="form_comment_email"><?php echo __('Adresse e-mail','clrz_lang'); ?> <span>*</span></label>
        <input id="form_comment_email" name="email" type="email" aria-required="true" required />
    </li>
    <li class="box">
        <label for="form_comment_url"><?php echo __('Site Web','clrz_lang');?></label>
        <input id="form_comment_url" type="url" name="url" />
    </li>
    <?php endif; ?>
    <li class="box">
        <label for="form_comment_message"><?php echo __('Votre commentaire','clrz_lang');?> <span>*</span></label>
        <textarea id="form_comment_message" name="comment" cols="20" rows="3" aria-required="true" required></textarea>
    </li>
    <li class="box submit-box">
        <button class="cssc-button" name="Post" type="submit"><?php echo __('Envoyer le commentaire','clrz_lang');?></button>
        <input type="hidden" id="clrz_comment_parent" name="comment_parent" value="0" />
        <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
        <?php do_action('comment_form', $post->ID); ?>
    </li>
    </ul>
</form>
<?php } else { ?>
    <p><?php echo __('Les commentaires sont fermés.','clrz_lang'); ?></p>
<?php }


/* ----------------------------------------------------------
   Trackbacks
   ------------------------------------------------------- */

if($afficher_trackbacks && $nb_trackbacks > 0){
echo '<h3>'.$nb_trackbacks.' '.__('Trackback','clrz_lang').($nb_trackbacks > 1 ? 's':'').'</h3>';
foreach($trackbacks as $comment) :  ?>
    <div id="comment-<?php echo $comment->comment_ID; ?>" class="comment trackback">
         <p class="comment_meta"><?php echo __('Par','clrz_lang');  ?> <?php comment_author_link();?> <?php comment_date(); ?></p>
         <div class="comment-content"><?php comment_text(); ?></div>
    </div>
<?php endforeach;
}
