<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

get_header();

$member = new Clrz_user($clrz_core->get_query_var('_username'));
?>

<form action="<?php echo $clrz_core->_getUrl('member', 'action=WriteMessage&view='.$member->get('user_login')); ?>" method="POST">
    <fieldset class="fs1">
        <legend>Envoyer un message à <?php echo $member->get('display_name');?></legend>
        <ol class="cf-ol">
            <li>
                <label for="title_send_message"><span>Titre</span></label>
                <input type="text" name="title_send_message" class="single fldrequired" />
            </li>
            <li>
                <label for="message_send_message"><span>Message</span></label>
                <textarea  name="message_send_message" cols="30" rows="8" class="area"></textarea>
            </li>
        </ol>
    </fieldset>
    <p class="cf-sb">
        <input type="hidden" value="<?php echo $member->get('ID'); ?>" name="inbox_id_send_message"></input>	
        <input type="submit" name="sendmessageform" id="sendmessageform" class="sendbutton cursor" value="Submit"/>
    </p>
</form>

<?php 
get_footer();