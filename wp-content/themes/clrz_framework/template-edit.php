<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';
global $clrz_Profil, $clrz_user, $clrz_core;
get_header();

?>
<form method="post" id="registercandidateform" class="forms" action="<?php echo $clrz_core->_getUrl('edit');?>">

    <fieldset>
        <ol>

            <li class="textbox boxright">
                <label for="register_pseudo">Pseudo :</label>
                <input type="text" id="register_pseudo" name="pseudo" placeholder="Pseudo" value="<?php echo $clrz_user->get('user_login');?>" />
            </li>
            
            <li class="textbox boxleft">
                <label for="register_nom">Nom :</label>
                <input type="text" id="register_nom" name="nom" placeholder="Nom" value="<?php echo $clrz_user->get('nom');?>" />
            </li>

            <li class="textbox boxright">
                <label for="register_prenom">Pr&eacute;nom :</label>
                <input type="text" id="register_prenom" name="prenom" placeholder="Pr&eacute;nom" value="<?php echo $clrz_user->get('prenom');?>" />
            </li>

            <li class="textbox boxleft">
                <label for="register_email">Adresse email :</label>
                <input type="email" id="register_email" name="email" placeholder="Adresse email" value="<?php echo $clrz_user->get('user_email');?>" />
            </li>

            <li class="textbox boxright">
                <label for="register_password">Nouveau mot de passe :</label>
                <input type="password" id="register_password" name="pass1" placeholder="Mot de passe" value="" />
            </li>

            <li class="textbox box">
                <label for="register_confirmpassword">Confirmation du nouveau mot de passe :</label>
                <input type="password" id="register_confirmpassword" name="pass2" placeholder="Confirmation du mot de passe" value="" />
            </li>

            <li class="textbox boxright">
                <p class="label">Date de naissance :</p>
                <?php
                    $birthday = $clrz_user->get('birthday');
                    $daybirth = mysql2date('d', $birthday);
                    $monthbirth = mysql2date('m', $birthday);
                    $yearbirth = ($birthday!='') ? mysql2date('Y', $birthday) : 1975;
                ?>
                <label for="register_birth_day" style="display:none;">Jour</label>
                <select name="birth_day" id="register_birth_day">
                    <?php for($i=1;$i<=31;$i++):?>
                        <option value="<?php echo $i;?>" <?php if($i==$daybirth) echo 'selected';?>><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
                
                <label for="register_birth_month" style="display:none;">Mois</label>
                <select name="birth_month" id="register_birth_month">
                    <?php for($i=1;$i<=12;$i++):?>
                        <option value="<?php echo $i;?>" <?php if($i==$monthbirth) echo 'selected';?>><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
                
                <label for="register_birth_year" style="display:none;">Année</label>
                <select name="birth_year" id="register_birth_year">
                    <?php for($i=1900;$i<=date('Y');$i++):?>
                        <option value="<?php echo $i;?>" <?php if($i==$yearbirth) echo 'selected';?>><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
            </li>

            <li class="submit">
                <button class="btn" type="submit"><span>S'inscrire</span></button>
            </li>
        </ol>
    </fieldset>
</form>

<?php 
get_footer();