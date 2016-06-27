<?php


class clrz_Props Extends clrz_controller{

	var $fields = array('proposer'=> array(
					'texte' => array('label' => 'texte', 'type' => 'textarea','required' => true),
					'categories' => array('label' => 'categories', 'type' => 'hidden','required' => true),
					'cgu' => array('label' => "J'accepte les conditions d'utilisations du DARD", 'type' => 'checkbox','required' => true),




		));

        var $steps = Array(
						'quisoutient'=>array(10,'public','qui-soutient'),
		 				'soutenir'=>array(10,'loggedin','webservice/soutenir'),
                                                'proposer'=>array(10,'loggedin','proposer'),
												'moderer'=>array(10,'loggedin','webservice/moderer'),
                                                'userscripts'=>array(10,'public','webservice/scripts.js'),
                                                'userheader'=>array(10,'public','webservice/userheader')
					);

        function clrz_Props()
	{

		parent::clrz_controller();

		global $clrz_core;
                
                $clrz_core->magic_rule('quisoutient','_slug=string&page=int');
				$clrz_core->magic_rule('quisoutient','_slug=string');
				$clrz_core->magic_rule('soutenir','_slug=string');
				$clrz_core->magic_rule('moderer','_slug=string');


        }

        function userheaderAction()
        {
            if(!is_user_logged_in())
                die;


        }
        function userscriptsAction()
        {

           

             /*$messages =$this->_encodeResponse(); sitemessages = <?php echo $messages;?>;
              */
           
            ?>
            sitemessageshtml = "<?php echo $this->showMessages(true);?>";
            current_user_logged_in = false;
            <?php
             if(!is_user_logged_in())
                die();
            global $clrz_user;
            $friendlist = json_encode($clrz_user->myFriends);
            $futureFriends = json_encode($clrz_user->myFutureFriends);
            $soutien =  array_map(array($this,'_returnval'),$clrz_user->getSoutientList());
            $soutienListe = json_encode($soutien);
            ?>
            current_user_logged_in = true;
            friendList =  <?php echo $friendlist;?>;
            futurefriendList =  <?php echo $friendlist;?>;
            soutient = <?php echo $soutienListe;?>;
           


            <?php
                die;




        }
		
		
		function modererAction()
        {
			
             if(!current_user_can('depublierpost')){
				$this->addError('moderer', 'Vous n\'avez pas les droits pour modérer une proposition');
			 }else{
				// traitement
				$this->addSuccess('moderation', 'La proposition a bien été ajoutée en modération');
				global $wpdb;
				$wpdb->query('UPDATE '.$wpdb->posts.' SET post_status = "draft" WHERE post_name = "'.$this->get_query_var('_slug').'" LIMIT 1');
			 }
			 
			 
			 
			 wp_redirect(wp_get_referer());

        }

        function proposerAction()
        {

             $this->action_title = 'Rédigez votre proposition';

        }


        function proposerPostAction()
        {

                if($this->checkProposer())
                {
                    global $wpdb,$clrz_user;
                     $this->addSuccess('proposer',__('Merci votre proposition à bien été enregistrée','jdg'));

                     $res = $wpdb->get_row('SELECT COUNT(*) AS total FROM '.$wpdb->posts.' WHERE post_section=1 AND post_type="post"');
                     $count = ($res->total) ? $res->total : 1;

                    $my_post = array();
                    $my_post['post_title'] = 'Proposition '.$count;
                    $my_post['post_content'] = substr(stripslashes(strip_tags(($this->getFormData('texte')))),0,340);
                    $my_post['post_status'] = 'publish';
                    $my_post['post_type'] = 'post';
                    $my_post['post_category'] = '1';
                    //$my_post['comment_status'] = 'closed';
                    $my_post['post_author'] = $clrz_user->get('ID');
                    $my_post['post_category'] = array(CAT_ANNONCES);

                    
                    

                    $postID = wp_insert_post($my_post);
                    $wpdb->query('UPDATE '.$wpdb->posts.' SET post_section = "1" WHERE ID = "'.$postID.'" LIMIT 1');

                   
                    $categories = explode(',',$this->getFormData('categories'));
                    $categories[] = 'propositions';
                  

                    $_cat = array();
                    foreach($categories AS $cat)
                    {
                        if(is_term($cat,'category'))
                           $_cat[] = $cat;

                    }
                 
                    wp_set_post_terms($postID,$_cat,'category');
                    $this->unsetData();
                    wp_redirect(get_permalink($postID));
                }

        }


        function checkProposer()
        {

             foreach($this->fields['proposer'] AS $field=>$info)
		{


			if( (!$this->getFormData($field,'proposer')) && ($info['required'] == true) )
				$this->addError($field, $this->fields['proposer'][$field]['label'].' '.__('is required','jdg'));
		}

                    

                if(!$this->getErrors())
		{

			return true;
		}
		else
			return false;



        }

        function soutenirAction()
        {

                global $clrz_user;

                 query_posts('name='.$this->get_query_var('_slug'));


                if(!have_posts())
                    $this->_redirect('/');

                the_post();
                $this->props_ID = get_the_ID();

                if($clrz_user->get('ID')==get_the_author_ID())
                         $this->addError('soutien', __('Vos ne pouvez pas soutenir votre propre proposition'));
                else if($clrz_user->soutenir($this->props_ID))
                        $this->addSuccess('soutien', __('Merci votre soutien à été enregistré'));
                 else
                        $this->addError('soutien', __('Votre soutien a déjà été enregistré'));
                
                wp_redirect(wp_get_referer());

        }


        function quisoutientAction()
        {
            
          
            query_posts('name='.$this->get_query_var('_slug'));
            
            
            if(!have_posts())
                $this->_redirect('/');

            the_post();
            $this->props_ID = get_the_ID();
          
            
            $auteur = new Clrz_user(get_the_author_ID());
            
            $this->action_title =  'Qui soutient la '.get_the_title().' de '.$auteur->get('display_name');
            wp_reset_query();


        }

        function _returnval($ar)
        {
            return $ar[0];


        }

        function getPartisans()
        {

            global $wpdb;
            
            $res = $wpdb->get_results('SELECT user_ID FROM clrz_soutient WHERE post_ID = "'.$this->props_ID.'"',ARRAY_N);
            if($res)
            $retour =  array_map(array($this,'_returnval'),$res);
            return $retour;
        }

}
global $clrz_Props;
$clrz_Props = new clrz_Props();

?>
