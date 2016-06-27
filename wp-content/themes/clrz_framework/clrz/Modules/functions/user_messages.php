


	function getMessages()
	{	
		global $wpdb;		   		
		$user_id = $this->get('ID');
		$query ='SELECT *, DATEDIFF(NOW(),date) AS date_diff FROM clrz_messages WHERE inbox_id="'.$user_id.'" ORDER BY date DESC';
		$messages = $wpdb->get_results( $query );
		return $messages;
	}

	function setUnreadMessage($message_ids)
	{
		global $wpdb;      
		$messages_str = implode(',',$message_ids); 		
		$query='UPDATE clrz_messages SET state="0" WHERE id IN ('.$messages_str.') AND inbox_id="'.$this->get('ID').'" ';
		$wpdb->query($query);
		echo $query;
		return true;
	}	

	function setReadMessage($message_ids)
	{
		global $wpdb;       
		$messages_str = implode(',',$message_ids);	
		$query= 'UPDATE clrz_messages SET state="1" WHERE id IN ('.$messages_str.') AND inbox_id="'.$this->get('ID').'"	 ';
		$wpdb->query($query);
		echo $query;
		return true;
	}		
	
	function deleteMessage($message_ids)
	{
		global $wpdb;
		$messages_str = implode(',',$message_ids);
		$result = $wpdb->query( $wpdb->prepare( 'DELETE FROM clrz_messages WHERE id IN ('.$messages_str.') AND inbox_id="'.$this->get('ID').'"  ') );
		return true;		
	}
	
	/* ---- ViewMessages  ---- */

	function getLinkViewMessages($action='set_unread')
	{
		global $clrz_core;
		$actions = array(
						'set_unread'=>'SetUnreadMessage',
						'delete'=>'DeleteMessage',
		);
		
		return $clrz_core->_getUrl('viewmessage','action='.$actions[$action]);
	}	