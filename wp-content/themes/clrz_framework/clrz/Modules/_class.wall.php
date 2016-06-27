<?php

class wall{
	
	public static function getWallMessages($user_id,$parent_id=0)
	{
		global $wpdb;
				
		$query = "SELECT *, DATEDIFF(NOW(),date) AS date_diff FROM clrz_wall_messages WHERE wall_id = ".$user_id." AND parent_id=".$parent_id." ORDER BY date DESC";
		$messages = $wpdb->get_results($query);
		
		return $messages;
	}
	
	public static function addWallMessage($datas)
	{
		global $wpdb;
		$wpdb->insert('clrz_wall_messages',$datas);
		
		return true;
	} 
	
	public static function deleteWallMessage($id)
	{	
	
		global $wpdb;
		global $clrz_user;
		
		$query = "SELECT * FROM clrz_wall_messages WHERE id=".$id; /* [Voir] pour mettre ce filtrage plutôt dans le controller */
		$message = $wpdb->get_row($query);
		
	
		if($message->wall_id != $clrz_user->user->ID)
			return false;
			
		
		$query = "DELETE FROM clrz_wall_messages WHERE id=".$id;
		$wpdb->query($query);
		
		if($message->parent_id == 0)
		{
			$query = "DELETE FROM clrz_wall_messages WHERE parent_id=".$id;
			$wpdb->query($query);
		}
			
		
		return true;
	}
	
	public static function wallMessageExists($id)
	{
		global $wpdb;
		
		$query = "SELECT * FROM clrz_wall_messages WHERE id=".$id; /* [Voir] pour mettre ce filtrage plutôt dans le controller */
		$message = $wpdb->get_row($query);
		
	
		if($message->id != $id)
			return false;
			
		return true;
	}
	

}

?>