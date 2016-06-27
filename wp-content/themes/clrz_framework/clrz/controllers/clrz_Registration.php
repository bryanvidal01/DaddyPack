<?php
//load_plugin_textdomain('clrz_registration');

class clrz_Registration Extends clrz_controller {

    var $fields = array(
        'register' => array(
            'pseudo' => array('label' => 'Pseudo', 'type' => 'text', 'required' => true),
            'email' => array('label' => 'E-Mail', 'type' => 'text', 'required' => true),
            'pass1' => array('label' => 'Mot de passe', 'type' => 'password', 'required' => true),
            'pass2' => array('label' => 'Confirmation du mot de passe', 'type' => 'password', 'required' => true),
            'nom' => array('label' => 'Nom', 'type' => 'text', 'required' => true),
            'prenom' => array('label' => 'Prénom', 'type' => 'text', 'required' => true),
            'birthday' => array('label' => 'Date de naissance', 'type' => 'text', 'required' => true),
            'conditions' => array('label' => 'Conditions', 'type' => 'checkbox', 'required' => true)
        ),
        'login' => array(
            'log' => array('label' => 'Identifiant', 'type' => 'text'),
            'pwd' => array('label' => 'Mot de passe', 'type' => 'password'),
            'rememberme' => array('label' => 'Se souvenir de moi', 'type' => 'checkbox'),
        ),
        'forgetPassword' => array(
            'email_pass' => array('label' => 'Votre email', 'type' => 'text', 'required' => true)
        ),
        'changePassword' => array(
            'newpass' => array('label' => 'Saisie nouveau mot de passe', 'type' => 'password'),
            'newpass2' => array('label' => 'Confirmation nouveau mot de passe', 'type' => 'password')
        )
    );
    
    var $steps = Array(
        'register' => array(10, 'notloggedin', 'inscription'),
        'registerconfirm' => array(10, 'loggedin', 'inscription/confirmation'),
        'logout' => array(654, 'loggedin', 'deconnexion'),
        'login' => array(654, 'notloggedin', 'connexion'),
        'forgetPassword' => array(54, 'notloggedin', 'connexion/mot-de-passe-oublie'),
        'changePassword' => array(54, 'notloggedin', 'connexion/nouveau-mot-de-passe'),
    );

    function clrz_Registration() {

        parent::clrz_controller();
        global $clrz_core;

        $clrz_core->magic_rule('changePassword', 'key=string&login=string');
    }


    function loginAction() {
        global $clrz_core;

        if ($clrz_core->get_query_var('key') != '' && $clrz_core->get_query_var('login') != '') {

            if ($this->reset_password($clrz_core->get_query_var('key'), $clrz_core->get_query_var('login'))) {
                $this->addSuccess('password_send', __('Votre mot de passe a été changé', 'clrz_lang'));
                $this->unsetData();
                $this->_redirect('login');
            }
            return;
        }
    }

    function loginPostAction() {

        global $clrz_core,$wpdb;

        $res = $wpdb->get_row('SELECT user_login FROM ' . $wpdb->users . ' WHERE user_email = "' . $this->getFormData('log') . '" LIMIT 0,1');
        if (!$res) {
            $this->addError('invalid_username', __('Votre email est invalide', 'clrz_lang'));
            $this->_redirect('login');die;
        }
        $_POST['log'] = $res->user_login;
        $retour = wp_signon();
        if ($retour->errors['invalid_username'])
            $this->addError('invalid_username', __('Votre email est invalide', 'clrz_lang'));
        if ($retour->errors['incorrect_password'])
            $this->addError('incorrect_password', __('Votre mot de passe est invalide', 'clrz_lang'));

        if (!$this->getErrors()) {
            $this->unsetData();
            $user = new Clrz_user($retour);
            $this->_redirect('profil');
            die;
        }

        $this->_redirect('login');
    }

    function forgetPasswordPostAction() {
        if ($this->getFormData('email_pass') != '') {
            if (is_email($this->getFormData('email_pass'))) {
                if ($this->retrieve_password($this->getFormData('email_pass'))) {
                    $this->addSuccess('confirm_email', __('Un email vous a été envoyé', 'clrz_lang'));
                    $this->unsetData();
                    $this->_redirect('login');
                    return;
                } else {
                    $this->addError('confirm_email', __('Erreur dans l\'envoi du mail', 'clrz_lang'));
                    $this->unsetData();
                    $this->_redirect('login');
                    return;
                }
            } else {
                $this->addError('invalid_email', __('Votre email est invalide', 'clrz_lang'));
                $this->_redirect('login');
            }
            return;
        }
    }

    function retrieve_password($email) {
        global $wpdb, $clrz_core;
        $user_data = get_user_by_email(trim($email));
        if (empty($user_data)) {
            $this->addError('invalid_email', __('Mot de passe invalide', 'clrz_lang'));
            return false;
        }
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;

        $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
        if (empty($key)) {
            $key = wp_generate_password(20, false);
            $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
        }

        $argsmail = array(
            'keyurl' => $clrz_core->_getUrl('changePassword', 'key=' . $key . '&login=' . rawurlencode($user_login)),
            'mail' => $user_email = $user_data->user_email
        );
        $clrz_core->sendmail('passwordask', $argsmail, __('Mot de passe oublié', 'clrz_lang'), array($email));
        return true;
    }


    function logoutAction() {
        wp_logout();
        $this->action_title = __('Déconnexion', 'clrz_lang');
        $this->_redirect(get_bloginfo('url'));
        die;
    }
    
    function checkRegister() {

        global $wpdb, $clrz_core;

//        include(ABSPATH . '/wp-includes/registration.php');
        
        $daybirth = strip_tags($_POST['birth_day']);
        $monthbirth = strip_tags($_POST['birth_month']);
        $yearbirth = strip_tags($_POST['birth_year']);
        $this->setFormData('birthday',$yearbirth.'-'.$monthbirth.'-'.$daybirth.' 00:00:00');
        if(!is_numeric($daybirth) || !is_numeric($monthbirth) || !is_numeric($yearbirth) || !checkdate($monthbirth, $daybirth, $yearbirth) || $yearbirth>date('Y'))
            $this->addError('cantbirth', __('Date de naissance invalide', 'clrz_lang'));
            
        $tabmanquant = array();
        foreach ($this->fields['register'] AS $field => $info):
            if ((!$this->getFormData($field, 'register')) && $info['required'] == true)
                $tabmanquant[] = $this->fields['register'][$field]['label'];
        endforeach;
        if (sizeof($tabmanquant) > 0):
            $varmanquant = implode('", "', $tabmanquant);
            $this->addError('forgetinput', __('Merci d\'informer le(s) champs ', 'clrz_lang') . '"' . $varmanquant . '"');
        endif;

        /* mots de passe pas pareils */
        if($this->getFormData('pass1','register')!=$this->getFormData('pass2','register'))
            $this->addError('pass1',__('Les mots de passe ne correspondent pas', 'clrz_lang'));
        
        /* mot de passe pas assez long */
        if ($this->getFormData('pass1', 'register') != '' && strlen($this->getFormData('pass1', 'register')) < 6)
            $this->addError('pass1', __('Le mot de passe doit contenir au minimum 6 caractères', 'clrz_lang'));
        
        /* format email */
        if($this->getFormData('email','register')!='' && !is_email($this->getFormData('email','register')))
            $this->addError('email',__('Le format de votre email est invalide', 'clrz_lang'));

        /* email deja existant */
        if (email_exists($this->getFormData('email', 'register')))
            $this->addError('email', __('Cet email existe déjà', 'clrz_lang'));
        
        /* pseudo deja existant */
        if (username_exists($this->getFormData('email', 'register')))
            $this->addError('email', __('Cet email existe déjà', 'clrz_lang'));


        if (!$this->getErrors())
            return true;
        else
            return false;
    }


    function registerPostAction() {
        global $wpdb, $clrz_core, $clrz_Registration;
  
        if ($this->checkRegister()) {
die('cool');
            $new_id = wp_create_user($this->getFormData('email', 'register'), $this->getFormData('pass1', 'register'), $this->getFormData('email', 'register'));
            
            if(is_object($new_id)){
                $this->addError('cantreg', __('Une erreur s\'est produite durant votre inscription', 'clrz_lang') );
            }else{
                $res = $wpdb->get_row('SELECT user_login FROM ' . $wpdb->users . ' WHERE ID = "' . $new_id . '" LIMIT 0,1');
                $user = wp_signon(array('user_login' => $res->login, 'user_password' => $this->getFormData('pass1', 'register'), 'remember' => true));
                if (!is_wp_error($user)) {
                    $this->addSuccess('user_created', __('Votre compte a été créé', 'clrz_lang'));
                    $_SESSION['confirmregister'] = '1';
                    $member = new Clrz_user($user);
                
                    $argsmail = array(
                        'email' => $this->getFormData('email', 'register'),
                        'name' => $this->getFormData('prenom', 'register'),
                        'mdp' => $this->getFormData('pass1', 'register'),
                    );
                    $clrz_core->sendmail('inscription', $argsmail, __('Inscription', 'clrz_lang'), array($this->getFormData('email', 'register')));

                    /* enregistrement des metas */
                    $customfield = array_slice($this->fields['register'], 3);
                    foreach ($customfield AS $k => $v) {
                        if ($k != 'pass1' && $k != 'pass2' && $k != 'email')
                            $save_usermeta[$k] = $this->getFormData($k, 'register');
                    }
                    $member->updateMetas($save_usermeta);

                    $this->unsetData();
                    $this->_redirect($clrz_core->_getUrl('profil'));
                    die;
                }
            }
        }
        
    }

    function registerAction() {

        $this->action_title = __('Cr&eacute;er un compte', 'clrz_lang');
        
    }

    function changePasswordAction() {
        
        $this->checkHash();
        
    }

    function changePasswordPostAction() {
        
        global $clrz_core;
        if ($this->checkChangePassword() && $this->checkHash()) {
            $user_data = get_user_by('login', $clrz_core->get_query_var('login'));

            wp_set_password($this->getFormData('newpass', 'changePassword'), $user_data->ID);

            $this->sendMailChangePassword($user_data);
            $this->addSuccess('password_change', __('Mot de passe envoyé', 'clrz_lang'));
            $this->_redirect('login');die;
        }
        
    }

    function checkChangePassword() {
        
        foreach ((array) $this->fields['changePassword'] AS $field => $info) {
            if (!$this->getFormData($field, 'changePassword'))
                $this->addError($field, $this->fields['changePassword'][$field]['label'] . ' ' . __('est requis', 'clrz_lang'));
        }
        if ($this->getFormData('newpass', 'changePassword') != $this->getFormData('newpass2', 'changePassword'))
            $this->addError('newpass_match', __('Les mots de passe ne correspondent pas', 'clrz_lang'));

        if (($this->getFormData('newpass', 'changePassword')) && (strlen($this->getFormData('newpass', 'changePassword')) < 6))
            $this->addError('newpass_min', __('Le mot de passe doit contenir au minimum 6 caractères', 'clrz_lang'));

        if (!$this->getErrors())
            return true;
        else
            return false;
        
    }
    
    function checkHash() {
        
        global $wpdb, $clrz_core;

        $key = $clrz_core->get_query_var('key');
        $login = $clrz_core->get_query_var('login');
        $key = preg_replace('/[^a-z0-9]/i', '', $key);

        // clé invalide
        if (empty($key) || !is_string($key))
            return $this->addError('invalid_key', __('Clé invalide', 'clrz_lang'));

        // login invalide
        if (empty($login) || !is_string($login))
            return new $this->addError('invalid_key', __('Clé invalide', 'clrz_lang'));

        // login inexisant
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));
        if (empty($user))
            return $this->addError('invalid_key', __('Clé invalide', 'clrz_lang'));

        if (!$this->getErrors())
            return true;
        else
            return false;
        
    }

}

global $clrz_Registration;
$clrz_Registration = new clrz_Registration();
