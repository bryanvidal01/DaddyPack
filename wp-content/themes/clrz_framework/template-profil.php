<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';
get_header();
?>
<p>Page profil </p>

<ul>
    <li><a href="<?php echo $clrz_core->_getUrl('edit');?>">Editer mon profil</a></li>
    <li><a href="<?php echo $clrz_core->_getUrl('friends');?>">Voir mes amis</a></li>
    <li><a href="<?php echo $clrz_core->_getUrl('messages');?>">Voir mes messages</a></li>
    <li><a href="<?php echo $clrz_core->_getUrl('favoris');?>">Voir mes favoris</a></li>
</ul>
<?php



global $clrz_user;
echo $clrz_user->get('prenom').' '.$clrz_user->get('nom');
?>

<?php 
get_footer();