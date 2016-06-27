<?php

class messageStatus{
	
	var $message_ids= array();
	var $user_id;
	
	function messageStatus($message_ids)
	{
		global $clrz_user;
		$this->message_ids = $message_ids;
		$this->user_id = $clrz_user->get('ID');
	}
	
	function markAsRead()
	{
		global $wpdb;
		$i=0;
		foreach( (array) $this->message_ids as $m_id)
		{
			$query = "SELECT * FROM clrz_status_message_meta WHERE id_message='$m_id' AND cle LIKE 'is_unread' AND id_user='$this->user_id' "; 
			if( !$wpdb->get_row($query) )
			{
				$i++;
			}
			else
			{
				if($wpdb->query("DELETE FROM clrz_status_message_meta WHERE id_message='$m_id' AND cle LIKE 'is_unread' AND id_user='$this->user_id' ") === false)
				{}
				else $i++;
			}
		}
		if(count($this->message_ids) == $i)
			return true;
		else return false;		
	}
	
	function markAsUnread()
	{
		global $wpdb;
		$i=0;
		foreach( (array) $this->message_ids as $m_id)
		{
			$query = "SELECT * FROM clrz_status_message_meta WHERE id_message='$m_id' AND cle LIKE 'is_unread' AND id_user='$this->user_id' "; 
			if( !$wpdb->get_row($query) )
			{
				$datas = array('id_user' => $this->user_id, 
					'id_message' => $m_id, 
					'cle' => 'is_unread'
				);		 	
				if($wpdb->insert('clrz_status_message_meta',$datas))
					$i++;
			}
			else
			{
				$i++;
			}
		}
		if(count($this->message_ids) == $i)
			return true;
		else return false;		
	}
	
	function markAsDeleted()
	{
		global $wpdb;
		$i=0;
		foreach( (array) $this->message_ids as $m_id)
		{
			$query = "SELECT * FROM clrz_status_message_meta WHERE id_message='$m_id' AND cle LIKE 'is_not_deleted' AND id_user='$this->user_id' "; 
			if( !$wpdb->get_row($query) )
			{
				$i++;
			}
			else
			{
				if($wpdb->query("DELETE FROM clrz_status_message_meta WHERE id_message='$m_id'  AND id_user='$this->user_id' ") === false)
				{}
				else 
				{
					$query="SELECT id FROM clrz_messages WHERE id_parent='$m_id'  " ;
					$wpdb->query("DELETE FROM clrz_status_message_meta WHERE id_message IN ($query) AND id_user='$this->user_id' ");
					$i++;
				}
			}
		}
		if(count($this->message_ids) == $i)
			return true;
		else return false;		
	}
	
	static function isUnread($m_id)
	{
		global $clrz_user,$wpdb ;
		$id_user = $clrz_user->get('ID');
		$query="SELECT * FROM clrz_status_message_meta WHERE id_message='$m_id' AND id_user='$id_user' AND cle LIKE 'is_unread'  ";

		$wpdb->get_row($query);
		if( $wpdb->num_rows == 0 )
		{
			return false;
		}else return true;		
	}
	
	public static function get($message_id)
	{
		global $wpdb;
				
		//$query = "SELECT *, DATEDIFF(NOW(),date) AS date_diff FROM clrz_wall_messages WHERE wall_id = ".$user_id." AND parent_id=".$parent_id." ORDER BY date DESC";
		//$messages = $wpdb->get_results($query);
		
		return $messages;
	}

	

}

?>