<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

global $clrz_core, $clrz_user;

get_header();

$messages = $clrz_user->getMessages(0);

?>

<div class="mail-options">
    <form class="jdg-forms" action="" method="post" name="">
        <ol>
            <li class="selectbox boxleft">
                <label>S&eacute;lectionner :</label>
                <span>
                    <select id="select_cb" name="select_cb">
                        <option value="0">Tous</option>
                        <option value="1">Non lu</option>
                        <option value="2">Lu</option>
                        <option selected="selected" value="3">Aucun</option>
                    </select>
                </span>
            </li>
        </ol>
    </form>
    <ul>
        <li><a id="link_setUnReadMessage" href="<?php echo $clrz_user->getLinkMessages('set_unread');?>">Marquer comme non lu(s)</a></li>
        <li><a id="link_setReadMessage" href="<?php echo $clrz_user->getLinkMessages('set_read'); ?>">Marquer comme lu(s) </a></li>
        <li><a id="link_deleteMessage" href="<?php echo $clrz_user->getLinkMessages('delete');?>">Supprimer</a></li>
    </ul>
</div>

<form action="" method="post" id="changeMessage" name="changeMessage">
    <?php
        $i  = 0;
        if (!empty($messages)) {
            foreach ($messages as $m) {
                $is_unread = messageStatus::isUnread($m->id);
                if ($is_unread)
                    $class = 'unread';
                else
                    $class= 'read';
                $user_submit = new Clrz_user((int)$m->submit_id);
                ?>
                    <div class="message_box <?php echo $class; ?>">
                        <div class="col1">
                            <?php if ($is_unread) {
                                $state = 0;
                            }else
                                $state = 1; ?>
                            <input name="messages[]" type="checkbox" class="cb" value="message<?php echo '_' . $m->id . '_' . $state; ?>" />
                        </div>
                        <div class="col2">
                            <a href="<?php echo $user_submit->getPermalink(); ?>"> <?php echo $user_submit->getAvatar(); ?></a>
                        </div>
                        <div class="col3">
                            <p class="user">
                                <a href="<?php echo $user_submit->getPermalink(); ?>"><?php echo $user_submit->user->nickname; ?></a>
                            </p>
                            <?php if ($m->date_diff > 6) {
                                $format = 'j M Y, à H:i';
                            } elseif ($m->date_diff == 0) {
                                $format = ' à H:i';
                            } else {
                                $format = 'D, à H:i';
                            } ?>
                            <p class="metadata">
                                <?php if ($m->date_diff == 0) {
                                    echo 'Aujourd\'hui' . mysql2date($format, $m->date);
                                }else
                                    echo mysql2date($format, $m->date); ?>
                            </p>
                        </div>
                        <div class="col4">
                            <?php if ($m->id_parent == 0) {
                                $id = $m->id;
                            } else {
                                $id = $m->id_parent;
                            } ?>
                            <p><a href="<?php echo $clrz_core->_getUrl('viewmessage', 'message_id=' . $id); ?>" class="viewmessage"><?php echo stripslashes($m->title); ?></a></p>
                            <p><?php echo truncate(strip_tags(apply_filters('comment_text', stripslashes($m->message))), 85); ?></p>
                        </div>
                    </div>
                <?php
                $i++;
            }
        } else {
            echo '<p class="no-mail">'.__('Vous n\'avez aucun message.', 'clrz_lang').'</p>';
        }
    ?>
</form>

<?php 
get_footer();