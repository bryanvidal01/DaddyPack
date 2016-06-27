<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

get_header();
global $clrz_core;

?>
<form class="jdg-forms form-block" method="post" id="loginform" action="">
    <fieldset>
        <ol>
            <li>
                <h4>D&eacute;j&agrave; inscrit ?</h4>
            </li>

            <li class="textbox boxleft">
                <label for="log">Identifiant</label>
                <span><input type="text" id="log" name="log" value="" /></span>
            </li>

            <li class="textbox boxright">
                <label for="pwd">Mot de passe</label>
                <span><input type="password" id="pwd" name="pwd" value="" title="password" /></span>
                <a href="<?php echo $clrz_core->_getUrl('forgetPassword');?>">Mot de passe oublié</a>
            </li>

            <li class="checkbox">
                <input type="checkbox" id="rememberme" name="rememberme" value="1" />
                <label for="rememberme">Se souvenir de moi</label>
            </li>
            <li class="button-submit">
                <button class="btn cta" type="submit">Connexion</button>
            </li>
        </ol>
    </fieldset>
</form>

<?php 
get_footer();