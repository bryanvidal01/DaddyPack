<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';
global $member, $clrz_user,$current_user, $clrz_core, $wpdb;

get_header();

//if(!$member->user) { 
//    $this->_redirect(get_bloginfo('url'));
//}
?>
<h2><?php echo $member->get('display_name') ?></h2>
<ul>
    <?php if($clrz_user->get('ID')!=$member->get('ID')): ?>
        <li><a href="<?php echo $clrz_core->_getUrl('newmessage','_username='.$member->get('user_login'));?>">Lui envoyer un mail</a></li>
        <li><a href="<?php echo $member->getLinkFriend('add');?>">Ajouter à mes amis</a></li>
    <?php endif;?>
</ul>

<?php 
get_footer();

