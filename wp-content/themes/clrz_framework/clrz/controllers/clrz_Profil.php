<?php

class clrz_Profil Extends clrz_controller{

    
    var $fields = array(
        'edit' => array(
            'user_email' => array('label' => 'E-Mail', 'type' => 'text', 'required' => true),
            'pass1' => array('label' => 'Mot de passe', 'type' => 'password'),
            'pass2' => array('label' => 'Confirmation du mot de passe', 'type' => 'password'),
            'nom' => array('label' => 'Nom', 'type' => 'text', 'required' => true),
            'prenom' => array('label' => 'Prénom', 'type' => 'text', 'required' => true),
            'birthday' => array('label' => 'Date de naissance', 'type' => 'text', 'required' => true)
        )
    );
    
    
    var $steps = array(
        'profil' => array(3, 'loggedin', 'mon-profil'),
        'edit' => array(3, 'loggedin', 'mon-profil/modifier'),
        'avatar' => array(3, 'loggedin', 'mon-profil/avatar'),
        'friends' => array(6, 'loggedin', 'mon-profil/amis'),
        'messages' => array(0, 'loggedin', 'mon-profil/messages'),
        'viewmessage' => array(0, 'loggedin', 'mon-profil/messages/viewmessage'),
        'newmessage' => array(0, 'loggedin', 'mon-profil/messages/newmessage'),
        'favoris' => array(0, 'loggedin', 'mon-profil/favoris'),
    );

    
    function clrz_Profil() {

        parent::clrz_controller();

        global $clrz_core;

        $clrz_core->magic_rule('messages', 'action=string');
        $clrz_core->magic_rule('viewmessage', 'action=string&_message_id=2');
        $clrz_core->magic_rule('viewmessage', 'action=string');
        $clrz_core->magic_rule('viewmessage', '_message_id=2');
        $clrz_core->magic_rule('newmessage', '_username=string');
        $clrz_core->magic_rule('friends', 'view=string');
        $clrz_core->magic_rule('friends', 'action=string&_friend_id=1');
        
    }




    /******************************************************************** PARTIE PROFIL DU PROFIL ************************************************************/	

    function favorisAction() {

        $this->action_title = __('Mes Favoris', 'clrz_lang');
        
    }

    function checkProfil() {
        
        global $clrz_user;
        $user = $clrz_user;
        
        include(ABSPATH . '/wp-includes/registration.php');

        if ($this->getFormData('pass1') && $this->getFormData('pass2')) {
            if ($this->getFormData('pass1') != $this->getFormData('pass2'))
                $this->addError('pass1', __('Les mots de passe ne correspondent pas', 'clrz_lang'));

            if (strlen($this->getFormData('pass1')) < 6)
                $this->addError('pass1', __('Password must countain at least 6 characters', 'clrz_lang'));

            if (strpos(" " . $this->getFormData['pass1'], "\\"))
                $this->addError('pass1', __('Password must countain at least 6 characters', 'clrz_lang'));
        }

        if ($user->getData()->user_email != $this->getFormData('user_email')) {
            if (!is_email($this->getFormData('user_email')))
                $this->addError('user_email', __('Email invalide', 'acf'));

            if (email_exists($this->getFormData('user_email')) || username_exists($this->getFormData('user_email')))
                $this->addError('user_email', __('Cet email est introuvable', 'clrz_lang'));
        }

        $this->setFormData('birthday', $_POST['bannee'] . '-' . $_POST['bmois'] . '-' . $_POST['bjour'] . ' 00:00:00');


        foreach ($this->fields['profil'] AS $field => $info) {


            if ((!$this->getFormData($field)) && ($info['required'] == true))
                $this->addError($field, $this->fields['profil'][$field]['label'] . ' ' . __('est requis', 'is_email'));
        }

        if (!$this->getErrors())
            return true;
        else
            return false;
        
    }

    function profilAction() {

        global $clrz_user;
        $this->action_title = __('Mon Profil', 'clrz_lang');

        if (isset($clrz_user->getData()->default_password_nag))
            $this->addSuccess('default_password_nag', __("Attention, vous utilisez un mot de passe généré automatiquement", 'clrz_lang'));
        
    }

    function profilPostAction() {
        
        global $clrz_user;
        $user = $clrz_user;

        if ($this->checkProfil()) {
            $this->setFormData('birthday', (int) $_POST['bannee'] . '-' . (int) $_POST['bmois'] . '-' . (int) $_POST['bjour'] . ' 00:00:00');


            $datas = array('display_name' => $this->getFormData('display_name'), 'user_email' => $this->getFormData('user_email'));

            $excludeddata = array('display_name' => '', 'user_email' => '', 'pass1' => '', 'pass2' => '', 'profil_pays' => '', 'profil_attitude' => '');

            $metas = array_diff_key($this->getFormDataArray(), $excludeddata);

            $user->uploadAvatar();
            $user->setPassword();
            $user->update($datas);
            $user->updateMetas($metas);

            $this->addSuccess('default_password_nag', __("Profil édité", 'clrz_lang'));

            $this->_redirect('profil');
        }
    }

    function editAction() {
        
        $this->action_title =  __('Modifier mes infos', 'clrz_lang');

    }


    function editPostAction() {
        
        global $clrz_user;
        $user = $clrz_user;

        if ($this->checkEdit()) {
            $this->setFormData('birthday', (int) $_POST['y-birth'] . '-' . (int) $_POST['m-birth'] . '-' . (int) $_POST['d-birth'] . ' 00:00:00');

            $datas = array('user_email' => $this->getFormData('user_email'));
            $excludeddata = array('user_email' => '', 'pass1' => '', 'pass2' => '');
            $metas = array_diff_key($this->getFormDataArray(), $excludeddata);
            $user->setPassword();
            $user->update($datas);
            $user->updateMetas($metas);
            $this->addSuccess('default_password_nag', __("Profil édité", 'clrz_lang'));
            $this->_redirect('profil');
        }
        
    }

    function checkEdit() {
        
        global $clrz_user;
        $user = $clrz_user;
        include(ABSPATH . '/wp-includes/registration.php');


        if ($user->getData()->user_email != $this->getFormData('user_email')) {
            if (!is_email($this->getFormData('user_email')))
                $this->addError('user_email', __('Email invalide', 'clrz_lang'));

            if (email_exists($this->getFormData('user_email')) || username_exists($this->getFormData('user_email')))
                $this->addError('user_email', __('Cet email existe déjà', 'clrz_lang'));
        }

        if ($this->getFormData('pass1') && $this->getFormData('pass2')) {
            if ($this->getFormData('pass1') != $this->getFormData('pass2'))
                $this->addError('pass1', __('Les mots de passe ne correspondent pas', 'clrz_lang'));

            if (strlen($this->getFormData('pass1')) < 6)
                $this->addError('pass1', __('Password must countain at least 6 characters', 'clrz_lang'));

            if (strpos(" " . $this->getFormData['pass1'], "\\"))
                $this->addError('pass1', __('Password must countain at least 6 characters', 'clrz_lang'));
        }

        $this->setFormData('birthday', $_POST['y-birth'] . '-' . $_POST['m-birth'] . '-' . $_POST['d-birth'] . ' 00:00:00');


        foreach ($this->fields['edit'] AS $field => $info) {
            if ((!$this->getFormData($field)) && ($info['required'] == true))
                $this->addError($field, $this->fields['edit'][$field]['label'] . ' ' . __('est requis', 'clrz_lang'));
        }

        if (!$this->getErrors())
            return true;
        else
            return false;
        
    }

    function avatarPostAction() {
        
        global $clrz_user;
        $user = $clrz_user;

        $user->uploadAvatar();
        $this->addSuccess('default_password_nag', __("Votre avatar a été changée", 'clrz_lang'));
        $this->_redirect('profil');
        
    }



    /******************************************************************** PARTIE FRIENDS DU PROFIL ************************************************************/	

    function friendsAction() {

        $this->action_title = __('Mes Amis', 'clrz_lang');
        
    }

    function doAskfriendAction() {
        
        global $clrz_user;

        $futur_friend = new Clrz_user($this->get_query_var('_friend_id'));

        if (!$clrz_user->Askfriend($this->get_query_var('_friend_id')))
            $this->addError('Askfriend', __('Une erreur est survenue', 'clrz_lang'));
        else {
            $this->addSuccess('Askfriend', __('Votre demande a été prise en compte', 'clrz_lang'));

            if (get_user_meta($futur_friend->get('ID'), 'mail_friend') == 1)
                $clrz_user->sendMailToNewFriend($futur_friend);
        }

        $this->_redirect('friends');
        
    }

    function doConfirmfriendAction() {
        
        global $clrz_user;

        if (!$clrz_user->confirmFriend($this->get_query_var('_friend_id')))
            $this->addError('confirmFriend', __('Une erreur est survenue pendant la confirmation', 'clrz_lang'));
        else
            $this->addSuccess('confirmFriend', __('Votre demande a été confirmée', 'clrz_lang'));

        $this->_redirect('friends');
        
    }

    function doBlockfriendAction() {

        global $clrz_user;

        if (!$clrz_user->blockFriend($this->get_query_var('_friend_id')))
            $this->addError('blockFriend', __('Votre demande a été prise en compte', 'clrz_lang'));
        else {
            if ($clrz_user->isBlocked($this->get_query_var('_friend_id')))
                $this->addSuccess('blockFriend', __('Votre demande a été prise en compte', 'clrz_lang'));
            else
                $this->addSuccess('blockFriend', __('Votre demande a été prise en compte', 'clrz_lang'));
        }

        $this->_redirect('friends');
        
    }

    function doDeleteFriendAction() {
        global $clrz_user;

        if (!$clrz_user->deleteFriend($this->get_query_var('_friend_id')))
            $this->addError('deleteFriend', __('Une erreur est survenue', 'clrz_lang'));
        else
            $this->addSuccess('deleteFriend', __('Votre demande a été prise en compte', 'clrz_lang'));

        $this->_redirect('friends');
        
    }

    
    
    
    /******************************************************************** PARTIE MESSAGES DU PROFIL ************************************************************/	

    
    /***************** Message *********************/
    
    function doSetReadMessageAction() {
        
        global $clrz_user;
        $i = 0;
        $tab_ids = array();
        foreach ((array) $_POST['messages'] as $p) {
            $id = explode('_', $p);
            $tab_ids[$i] = (int) $id[1];
            $i++;
        }
        $statut = new messageStatus($tab_ids);
        if (!$statut->markAsRead())
            $this->addError('setReadMessage', __('Une erreur est survenue pendant le changement du statut du message', 'clrz_lang'));
        else
            $this->addSuccess('setReadMessage', __('Statut de message changé', 'clrz_lang'));
        $this->_redirect('messages');
        
    }

    function doSetUnreadMessageAction() {
        
        global $clrz_user;
        $i = 0;
        $tab_ids = array();
        foreach ((array) $_POST['messages'] as $p) {
            $id = explode('_', $p);
            $tab_ids[$i] = (int) $id[1];
            $i++;
        }
        $statut = new messageStatus($tab_ids);
        if (!$statut->markAsUnread())
            $this->addError('setUnReadMessage', __('Erreur pendant le changement de statut de message', 'clrz_lang'));
        else
            $this->addSuccess('setUnReadMessage', __('Statut de message changé', 'clrz_lang'));
        $this->_redirect('messages');
        
    }

    function doDeleteMessageAction() {
        
        global $clrz_user;
        $i = 0;
        $tab_ids = array();
        foreach ((array) $_POST['messages'] as $p) {
            $id = explode('_', $p);
            $tab_ids[$i] = (int) $id[1];
            $i++;
        }
        $statut = new messageStatus($tab_ids);
        if (!$statut->markAsDeleted())
            $this->addError('deleteMessage', __('Erreur pendant la suppression du message', 'clrz_lang'));
        else
            $this->addSuccess('deleteMessage', __('Message effacé', 'clrz_lang'));
        $this->_redirect('messages');
        
    }

    /***************** ViewMessage *********************/

    function messagesAction() {

        $this->action_title = __('Mes messages', 'clrz_lang');
        
    }					

    function viewmessageAction() {

        $this->action_title = __('Mes messages', 'clrz_lang');
        
    }	

    function newmessageAction() {

        $this->action_title = __('Nouveau message', 'clrz_lang');
        
    }	

    //Reponse a un message prive
    function doAddMessageAction(){
        
        global $clrz_user;
        $comment = $_POST['comment'];
        $message_id = $_POST['message_id'];
        $title = $_POST['title'];
        $inbox_id = $_POST['inbox_id'];

        if (!is_user_logged_in()) {
            $this->addError('addMessageNotLoggedIn', __('Vous devez être connecté', 'clrz_lang'));
        } elseif (empty($comment)) {
            $this->addError('addMessageFieldEmpty', __('Votre message est vide', 'clrz_lang'));
        } elseif ($clrz_user->isMessageDeleted($clrz_user->get('ID'), $message_id)) {
            $this->addError('addMessageUserDelete', __('Cette conversation n\'est plus suivi par votre ami', 'clrz_lang'));
        } else {
            if (!$clrz_user->addMessage($message_id, $comment, $title, $inbox_id))
                $this->addError('addMessageError', __('Une erreur est survenue dans l\'ajout du message', 'clrz_lang'));
            else {
                $this->addSuccess('addMessage', __('Message ajouté', 'clrz_lang'));
            }
        }
        $this->_redirect('viewmessage','message_id='.$message_id);				
    }


    function doSetUnreadViewMessageAction(){	
        
        global $clrz_user;

        $statut = new messageStatus(array($this->get_query_var('message_id')));
        if (!$statut->markAsUnread())
            $this->addError('setUnReadViewMessage', __('Erreur dans le changement de statut du message', 'clrz_lang'));
        else
            $this->addSuccess('setUnReadViewMessage', __('Statut du message changé', 'clrz_lang'));

        $this->_redirect('messages');	
        
    }	

    function doDeleteViewMessageAction(){
        
        global $clrz_user;
        $statut = new messageStatus(array($this->get_query_var('message_id')));

        if (!is_user_logged_in()) {
            $this->addError('deleteViewNotLoggedIn', __('Vous devez être connecté', 'clrz_lang'));
        } elseif (!$clrz_user->is_message_exists($this->get_query_var('message_id'))) {
            $this->addError('deleteViewMessageNotExist', __('Message erroné', 'clrz_lang'));
        } elseif (!$statut->markAsDeleted())
            $this->addError('deleteViewMessage', __('Erreur dans la suppression du message', 'clrz_lang'));
        else
            $this->addSuccess('deleteViewMessage', __('Message supprimé', 'clrz_lang'));
        $this->_redirect('messages');
        
    }
    
    
    
    
    
    
    
    

    /**************************************************** PARTIE MUR DU PROFIL **************************************************************/	

    function wallAction(){
        
        $this->action_title = 'Mon Mur';

        if (!is_user_logged_in())
            $this->addError('addMessageWallLogged', __('Vous devez être connecté', 'clrz_lang'));

        if ($this->getErrors())
            $this->_redirect('/');

    }

    function wallPostAction(){	
        
        if(!is_user_logged_in()) {
            $this->addError('addMessageWallLogged', __('Vous devez être connecté', 'clrz_lang'));
            $this->_redirect('wall');
        }


        if($this->getFormData('parentID')) {
            $parent_id = $this->getFormData('parentID');
            if (!(wall::wallMessageExists($parent_id)))
                $this->addError('addMessageWallDontExist', __('Message introuvable', 'clrz_lang'));
            else {
                $message = $this->getFormData('answer');
                if (empty($message))
                    $this->addError('addMessageWallEmpty', __('Votre message est vide', 'clrz_lang'));
            }
        }else {
            $parent_id = 0;
            $message = $this->getFormData('comment');
            if (empty($message))
                $this->addError('addMessageWallEmpty', __('Votre message est vide', 'clrz_lang'));
        }



        if($this->getErrors())
                return false;


            global $clrz_user;

            $datas = array(
                'submit_id' => $clrz_user->user->ID, 
                'wall_id' => $clrz_user->user->ID, 
                'parent_id' => $parent_id,
                'message' => stripslashes($message),
                'date' => date('Y-m-d H:i:s')
            );

            wall::addWallMessage($datas);

            $this->addSuccess('addMessage',__('Message ajouté', 'clrz_lang'));

    }

    function doDeleteWallMessageAction(){
        
        $message_id = $this->get_query_var('id');

        //Filtrages 

        $delete = wall::deleteWallMessage($message_id);

        if(!$delete)
                $this->addSuccess('deleteWallMessageFalse',__('Ce message ne peut pas être supprimé', 'clrz_lang'));
        else	
                $this->addSuccess('addMessage',__('Message supprimé', 'clrz_lang'));

        $this->_redirect('wall');
    }





    /************************************************* PARTIE FAVORIS DU PROFIL *********************************************************/

    function doDeleteFavoriteAction(){
        
        global $clrz_user;	

        if(!$clrz_user->deleteFavorite($this->get_query_var('id')))
            $this->addError('deleteFavoriteNotExist',__('Article introuvable parmi vos favoris', 'clrz_lang'));
        else
            $this->addSuccess('deleteFavorite',__('Favoris supprimé', 'clrz_lang'));
        $this->_redirect('favoris');			
    }		

    function doAddFavoriteAction()
    {
        
        global $clrz_user, $clrz_core;	
        $post_id = $clrz_core->get_query_var('id');

        if( !is_user_logged_in() ){
            $this->addError('addFavoriteNotLoggedIn',__('Vous devez être connecté', 'clrz_lang'));
            $this->_redirect('register');
        }
        else{
            if(!$clrz_user->addFavorite($post_id))
                $this->addError('addFavoriteError',__('Une erreur est survenu pendant l\'ajout du favori', 'clrz_lang'));
            else
                $this->addSuccess('addFavorite',__('Favori ajouté', 'clrz_lang'));
            $this->_redirect('favoris');
        }				
    }	



	

}

global $clrz_Profil;
$clrz_Profil = new clrz_Profil();

