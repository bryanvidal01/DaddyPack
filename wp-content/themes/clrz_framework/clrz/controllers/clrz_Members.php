<?php 

class clrz_Members Extends clrz_controller{
	
     var $steps = Array(
         'members'=>array(3,'public','membres')
     );									

    function clrz_Members(){

        parent::clrz_controller();
        global $clrz_core;
        $clrz_core->magic_rule('members','page=1');
        $clrz_core->magic_rule('members','type_order=recent');
        $clrz_core->magic_rule('members','search=name');
        $clrz_core->magic_rule('members','page=int');
        $clrz_core->magic_rule('members','search=name&page=int');
        $clrz_core->magic_rule('members','type_order=recent&search=name');
        $clrz_core->magic_rule('members','type_order=recent&search=name&page=int');
        $clrz_core->magic_rule('members','type_order=string&page=int');

    }

    function membersAction(){
        
            $this->action_title= __('Les membres', 'clrz_lang');
    }
		
}

global $clrz_Members;
$clrz_Members = new clrz_Members();

