<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

get_header();
global $clrz_core;
?>
<form class="jdg-forms form-block" method="post" id="forgotpassform" action="">
    <fieldset>
        <ol>
            <li>
                <h4>Mot de passe oubli&eacute; ?</h4>
            </li>
            <li class="textbox">
                <label for="email_pass">Votre email</label>
                <span><input type="text" id="email_pass" name="email_pass" value="" /></span>
            </li>

            <li class="button-submit">
                <button class="btn cta" type="submit">Envoyer</button>
            </li>
        </ol>
    </fieldset>
</form>

<?php 
get_footer();