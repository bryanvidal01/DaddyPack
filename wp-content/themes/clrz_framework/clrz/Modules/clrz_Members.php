<?php 

class clrz_Members Extends clrz_controller{
	
	 var $steps = Array(
		 'members'=>array(3,'public','members')
		 
	 
	 );									

	function clrz_Members()
	{
		
		parent::clrz_controller();
		global $clrz_core;
		$clrz_core->magic_rule('members','page=1');
      $clrz_core->magic_rule('members','order=recent');
      $clrz_core->magic_rule('members','search=name');
      $clrz_core->magic_rule('members','order=recent&search=name');

	}
	
	function membersAction()
	{
		
		$this->action_title='Les membres';
		
	}
		
}

global $clrz_Members;
$clrz_Members = new clrz_Members();

?>