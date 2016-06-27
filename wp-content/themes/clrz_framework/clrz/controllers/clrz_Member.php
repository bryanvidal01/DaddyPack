<?php
//load_plugin_textdomain('clrz_registration');

class clrz_Member Extends clrz_controller{
	
    var $fields = array(
        'member'=> array(
            'answer' => array('label' => '', 'type' => 'text'),
            'parentID' => array('label' => '', 'type' => 'hidden'),
            'title_send_message' => array('label' => 'title', 'type' => 'text'),
            'message_send_message' => array('label' => 'message', 'type' => 'textarea'),
            'inbox_id_send_message' => array('label' => 'id_inbox', 'type' => 'hidden'),	

        ),
        'memberfavorites'=> array(

        )
    );


    var $steps = Array(
        'member'=>array(10,'public','membre'),
        'memberfavorites'=>array(0,'public','membre/favorites')
    );	

    var $section;				


    function clrz_Member(){
        
        parent::clrz_controller();
        global $clrz_core;
        $clrz_core->magic_rule('member','action=string&view=string');
        $clrz_core->magic_rule('member','_view=username');
        $clrz_core->magic_rule('member','_view=username&page=int');
        $clrz_core->magic_rule('member','_view=username&section=friends');
        $clrz_core->magic_rule('member','_view=username&section=friends&page=1');
        $clrz_core->magic_rule('memberfavorites','_view=username');
        
    }


    function memberAction(){
        
        global $member,$clrz_core,$wpdb;		

        if(is_numeric($this->get_query_var('_view'))){
            $mylink = $wpdb->get_row("SELECT * FROM wp_users WHERE user_nicename = '".strip_tags($this->get_query_var('_view'))."'");
            $member = new Clrz_user($mylink->ID);
        }else{
            $member = new Clrz_user($this->get_query_var('_view'));
        }
        
        $this->action_title =  'Profil de '.$member->get('display_name');	

        if($this->get_query_var('section')=='friends'){
            $clrz_core->section='memberfriends';
        }
    }

    function memberfavoritesAction(){

        if(!$this->get_query_var('_view'))
            $this->_redirect('/');

        global $member,$clrz_core;		
        $member = new Clrz_user($this->get_query_var('_view'));

        $this->action_title =  __('Les favoris de').' '.$member->get('display_name');	


    }	

    function updateTemplate($section){
        
        return $this->section;	
        
    }

    function doWriteMessageAction(){ // Ecrire un message privé à un membre
        
        global $clrz_user;
        $member_submit = new Clrz_user();

        $title = $_POST['title_send_message'];
        $message = $_POST['message_send_message'];
        $inbox_id = $_POST['inbox_id_send_message'];
        $submit_id = $member_submit->get('ID');

        $member_inbox = new Clrz_user($inbox_id);
        if( !is_user_logged_in() ){
                $this->addError('sendMessageNotLoggedIn',__('Vous n\'êtes pas connecté', 'clrz_lang'));
        }		
        elseif($submit_id == $inbox_id){
                $this->addError('sendMessageSelf',__("Vous ne pouvez pas vous envoyer un message", 'clrz_lang'));
        }
        elseif(empty($title) || empty($message) ){
                $this->addError('sendMessageFieldEmpty',__('Votre message est vide', 'clrz_lang'));
        } 
        elseif(!get_userdata($inbox_id)){
                $this->addError('sendMessageUserNotExist',__("Ce membre n\'existe pas", 'clrz_lang'));
        }
        else{
            global $wpdb;
            $datas = array('submit_id' => $submit_id, 
                'inbox_id' => $inbox_id, 
                'date' => date('Y-m-d H:i:s'),
                'title' => $title,
                'message' => $message,
                'state' => 0,
                'id_parent' => 0
            );		 	
            $wpdb->insert('clrz_messages',$datas);
            $m_id = $wpdb->insert_id;

            $datas = array('id_user' => $inbox_id, 
                'id_message' => $m_id, 
                'cle' => 'is_not_deleted'
            );			 	
            $wpdb->insert('clrz_status_message_meta',$datas);			 

            $datas = array('id_user' => $submit_id, 
                'id_message' => $m_id, 
                'cle' => 'is_not_deleted'
            );			 	
            $wpdb->insert('clrz_status_message_meta',$datas);	

            $datas = array('id_user' => $inbox_id, 
                'id_message' => $m_id, 
                'cle' => 'is_unread'
            );			 	
            $wpdb->insert('clrz_status_message_meta',$datas);				


            $this->addSuccess('sendMessage',__('Message envoyé', 'clrz_lang'));


            if(get_user_meta($member_inbox->get('ID'),'mail_message') == 1)
                $clrz_user->sendMailMessage($member_inbox);	

        }
        $this->_redirect('member','_view='.$this->get_query_var('_view'));
    }

    function doWriteWallMessageAction(){

        if(!is_user_logged_in()){	
            $this->addError('addMessageWallLogged',__('Il faut être conecté pour lire ses messages', 'clrz_lang'));
            $this->_redirect('wall');
        }


        if(!empty($_POST['parentID'])){ /* Ajout d'un message */
            $parent_ID = $_POST['parentID'];
            if(!(wall::wallMessageExists($parent_ID)))
                $this->addError('addMessageWallDontExist',__("Message introuvable", 'clrz_lang'));
            else
            {
                $message = $_POST['answer']; // Form Data: Problème de POST dans le dispatch
                if(empty($message))
                    $this->addError('addMessageWallEmpty',__('Votre message est vide', 'clrz_lang'));
            }
        }

        else{ /* Ajout d'une réponse */
            $parent_ID = 0;

            $message = $_POST['comment']; // Form Data: Problème de POST dans le dispatch
            if(empty($message))
                $this->addError('addMessageWallEmpty',__('Votre message est vide', 'clrz_lang'));
        }



        if($this->getErrors())
                return false;


        global $clrz_user;
        $wall_user = new Clrz_user($this->get_query_var('_view'));

        $datas = array(
            'submit_id' => $clrz_user->user->ID, 
            'wall_id' => $wall_user->user->ID, 
            'parent_id' => $parent_ID,
            'message' => stripslashes($message),
            'date' => date('Y-m-d H:i:s')
        );

        wall::addWallMessage($datas);

        // envoi mail
        if(get_user_meta($wall_user->get('ID'),'mail_wall') == 1)
            $clrz_user->sendMailWall($wall_user);	

        $this->addSuccess('addMessage',__('Message ajouté', 'clrz_lang'));
        $this->_redirect('member','_view='.$this->get_query_var('_view'));
    }

	
	

}

global $clrz_Member;
$clrz_Member = new clrz_Member();

