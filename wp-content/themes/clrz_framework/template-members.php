<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

$types_search = array(
    'active' => 'Les plus actifs',
    'recent' => 'Les plus récents',
);

global $member, $clrz_user,$current_user, $clrz_core, $wpdb;
$paged = ($clrz_core->get_query_var('page')) ? $clrz_core->get_query_var('page') : 1;
$search = ($clrz_core->get_query_var('search')) ? mysql_real_escape_string($clrz_core->get_query_var('search')) : '';
$search = ($search=='Rechercher') ? '' : $search;

$user_search = new Clrz_User_Search($search, $paged, '');
$user_search->users_per_page = 10;

$type_search = 'recent';
if(array_key_exists(get_query_var('clrz_type_order'), $types_search))
    $type_search = stripslashes(strip_tags(get_query_var('clrz_type_order')));

$user_search->custom_query($type_search);
$user_search->init();
$nb_resultats = $user_search->get_results();
if(empty($nb_resultats))
    wp_redirect($clrz_core->_getUrl('members'),302);

get_header();
?>

<div class="sort-members">
	<label for="sort-members-view">Trier par</label>
	<select id="sort-members-view" onchange="window.location=this.value;">
		<?php foreach($types_search as $type => $nom) :?>
		<option value="<?php echo $clrz_core->_getUrl('members').'type_order/'.$type.'/'.($search != '' ? 'search/'.$search.'/':''); ?>"<?php echo ($types_search[$type_search] == $nom) ? 'selected="selected"' : ''; ?>>
			<?php echo $nom; ?>
		</option>
		<?php endforeach; ?>
	</select>
</div>

<div class="sort-members">
    <form action="" method="get">
        <div>
            <input type="text" name="search" value="<?php echo $search; ?>" />
            <input type="submit" value="" />
        </div>
    </form>
</div>
<h3><?php echo sprintf(__('%s membres','clrz_lang'),$user_search->total_users_for_query); ?></h3>
<ul class="large-memberlist">
<?php
foreach($user_search->get_results() AS $userID) : 
$user = new Clrz_user($userID); ?>
<li>
    <a href="<?php echo $user->getPermalink(); ?>" class="illu" title="<?php echo __('Profil de ','clrz_lang').' '.$user->get('display_name'); ?>">
        <img width="72" height="72" src="<?php echo $user->getAvatarURL('72'); ?>" alt="<?php echo htmlentities($user->get('display_name')); ?>" />
        <div class="miniprofil">
            <h3><?php echo $user->get('display_name'); ?></h3>
            <span><?php echo __('Voir son profil','clrz_lang'); ?></span>
        </div>
    </a>
</li>
<?php endforeach; ?>
</ul>
<?php include TEMPLATEPATH . '/tpl/tpl_pagination.php'; ?>

<?php 
get_footer();

