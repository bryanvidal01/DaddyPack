<?php

/**
 * WordPress User Search class.
 *
 * @since unknown
 * @author Mark Jaquith
 */
class Clrz_User_Search {

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $results;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $search_term;

	/**
	 * Page number.
	 *
	 * @since unknown
	 * @access private
	 * @var int
	 */
	var $page;

	/**
	 * Role name that users have.
	 *
	 * @since unknown
	 * @access private
	 * @var string
	 */
	var $role;

	/**
	 * Raw page number.
	 *
	 * @since unknown
	 * @access private
	 * @var int|bool
	 */
	var $raw_page;

	/**
	 * Amount of users to display per page.
	 *
	 * @since unknown
	 * @access public
	 * @var int
	 */
	var $users_per_page = 50;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $first_user;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var int
	 */
	var $last_user;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $query_limit;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $query_sort;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $query_from_where;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var int
	 */
	var $total_users_for_query = 0;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var bool
	 */
	var $too_many_total_users = false;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $search_errors;

	/**
	 * {@internal Missing Description}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $paging_text;

	/* clrz custom query */
	
	var $custom_query_from_where =' ';
	var $custom_query_where =' ';
	var $custom_query_select =' ';
	var $custom_query_groupby = ' ';
	var $meta_search_args = array();
	var $meta_search_cond = array();
	var $query_string='';
	/**
	 * PHP4 Constructor - Sets up the object properties.
	 *
	 * @since unknown
	 *
	 * @param string $search_term Search terms string.
	 * @param int $page Optional. Page ID.
	 * @param string $role Role name.
	 * @return Clrz_User_Search
	 */
	function Clrz_User_Search ($search_term = '', $page = '', $role = '') {
		$this->search_term = $search_term;
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$this->page = (int) ( '' == $page ) ? 1 : $page;
		$this->role = $role;

		
	}

	function init()
	{
		
		$this->prepare_query();
		$this->query();
		$this->prepare_vars_for_template_usage();
		$this->do_paging();	
		
		
	}
	
	function debug()
	{
		echo $this->query_string;	
		
	}
	/**
	 * {@internal Missing Short Description}
	 *
	 * {@internal Missing Long Description}
	 *
	 * @since unknown
	 * @access public
	 */
	function prepare_query() {
		
		
		
		global $wpdb;
		$this->first_user = ($this->page - 1) * $this->users_per_page;
		$this->query_limit = $wpdb->prepare(" LIMIT %d, %d", $this->first_user, $this->users_per_page);
		$this->query_sort = (empty($this->query_sort)) ? ' ORDER BY display_name' : $this->query_sort;
		$search_sql = '';
		if ( $this->search_term ) {
			$searches = array();
			$search_sql = 'AND (';
			foreach ( array('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name') as $col )
				$searches[] = $col . " LIKE '%$this->search_term%'";
			$search_sql .= implode(' OR ', $searches);
			$search_sql .= ')';
		}
		$this->prepareMetas();
		$this->query_from_where = "FROM $wpdb->users";
		
		$this->query_from_where .= $this->custom_query_from_where;
		
		if ( $this->role )
			$this->query_from_where .= $wpdb->prepare(" INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' AND $wpdb->usermeta.meta_value LIKE %s", '%' . $this->role . '%');
		else
			$this->query_from_where .= " WHERE 1=1";
			$this->query_from_where .= $this->custom_query_where;
			
			
		$this->query_from_where .= " $search_sql";

	} 
	
	
	function custom_query($type)
	{
		global $wpdb;
			
		switch($type)
		{
			case 'active' :
				$this->add_select(",meta1.meta_value AS lastaction ");
				$this->add_from_where(" LEFT JOIN $wpdb->usermeta AS meta1 ON ($wpdb->users.ID = meta1.user_id AND meta1.meta_key = 'clrz_user_lastaction')");
				$this->query_sort = ' ORDER BY lastaction DESC ';
			break;
			case 'recent' :
				$this->add_select(",user_registered ");
				$this->query_sort = ' ORDER BY user_registered DESC ';
			break;
			case 'mostposts' :
				$this->add_select(",COUNT(*) AS totalposts ");
				$this->add_from_where(" LEFT JOIN $wpdb->posts AS meta1 ON ($wpdb->users.ID = meta1.post_author AND meta1.post_status = 'publish')");
				$this->custom_query_groupby = ' GROUP BY ID ';
				$this->query_sort = ' ORDER BY totalposts DESC ';
			break;
			case 'viewed' :
				$this->add_select(",meta1.meta_value AS viewed ");
				$this->add_from_where(" LEFT JOIN $wpdb->usermeta AS meta1 ON ($wpdb->users.ID = meta1.user_id AND meta1.meta_key = 'clrz_user_views')");
				$this->query_sort = ' ORDER BY viewed DESC ';
			break;
			case 'alpha' :
				$this->add_select(",display_name ");
				$this->query_sort = ' ORDER BY display_name ASC ';
				
			break;
			case 'random' :
				$this->add_select(",display_name ");
				$this->query_sort = ' ORDER BY RAND() ';
				
			break;

			
			
		}
		
		
			
		
	}
	
	function addMeta($array_meta)
	{
		//$array_meta =array($key,$compare,$value);
		//$this->meta_search_cond[]= $condition;
		//$this->meta_search_args[]= array($key,$compare,$value,$condition);
		$this->meta_search_args[] = $array_meta;
	}
	
	function prepareMetas()
	{
		global $wpdb;
		$i=0;
		
		if($this->meta_search_args)
		//$this->add_where('AND ');
                $length = sizeof($this->meta_search_args);
		foreach	($this->meta_search_args AS $args)
		{
			
			$this->add_select(",meta$i.meta_value AS ".$args[0]." ");
			$this->add_from_where(" LEFT JOIN $wpdb->usermeta AS meta$i ON ($wpdb->users.ID = meta$i.user_id AND meta$i.meta_key = \"".mysql_real_escape_string($args[0])."\")");
			 $this->add_where(' '.$args[3].' ');
                        $this->add_where("meta$i.meta_value ".$args[1]." \"".mysql_real_escape_string($args[2])."\"");
			
                       
			
			$i++;
		}
		
		
	}
	
	function userid_in($string='')
	{

            
		global $wpdb;
		if($string)
		$this->add_where('AND ');
		if(is_array($string))
		$string=implode(',',$string);
			$this->add_where($wpdb->users.'.ID IN ('.$string.')');
		
	}
	
	function add_from_where($string)
	{
		
		$this->custom_query_from_where .= $string;
		
	}
	
	function add_where($string)
	{
		
		$this->custom_query_where .= $string;
		
	}
	
		
	function add_select($string)
	{
		
		$this->custom_query_select .= $string;
		
	}

	/**
	 * {@internal Missing Short Description}
	 *
	 * {@internal Missing Long Description}
	 *
	 * @since unknown
	 * @access public
	 */
	function query() {
		global $wpdb;
		//echo 'SELECT '.$wpdb->users.'.ID ' . $this->custom_query_select . $this->query_from_where . $this->custom_query_groupby . $this->query_sort .  $this->query_limit;
		
		$this->query_string = 			'SELECT SQL_CALC_FOUND_ROWS '.$wpdb->users.'.ID '.$this->custom_query_select . $this->query_from_where .' AND user_login NOT LIKE "unverified__%" AND user_login <> "admin" '. $this->custom_query_groupby . $this->query_sort . $this->query_limit;
		
	//	echo $this->query_string;
		
		$this->results = $wpdb->get_col('SELECT SQL_CALC_FOUND_ROWS '.$wpdb->users.'.ID '.$this->custom_query_select . $this->query_from_where .' AND user_login NOT LIKE "unverified__%" AND user_login <> "admin"  '. $this->custom_query_groupby . $this->query_sort . $this->query_limit);
		$query = $wpdb->get_row('SELECT FOUND_ROWS() AS total');
		$this->total_users_for_query = $query->total;
		/*if ( $this->results )
			$this->total_users_for_query = $wpdb->get_var('SELECT COUNT('.$wpdb->users.'.ID) ' . $this->custom_query_select . $this->query_from_where . $this->custom_query_groupby); // no limit
		else
			$this->search_errors = new WP_Error('no_matching_users_found', __('No matching users were found!'));*/
			
			
	}

	/**
	 * {@internal Missing Short Description}
	 *
	 * {@internal Missing Long Description}
	 *
	 * @since unknown
	 * @access public
	 */
	function prepare_vars_for_template_usage() {
		$this->search_term = stripslashes($this->search_term); // done with DB, from now on we want slashes gone
	}

	/**
	 * {@internal Missing Short Description}
	 *
	 * {@internal Missing Long Description}
	 *
	 * @since unknown
	 * @access public
	 */
	function do_paging() {
		if ( $this->total_users_for_query > $this->users_per_page ) { // have to page the results
			$args = array();
			if( ! empty($this->search_term) )
				$args['usersearch'] = urlencode($this->search_term);
			if( ! empty($this->role) )
				$args['role'] = urlencode($this->role);

			$this->paging_text = paginate_links( array(
				'total' => ceil($this->total_users_for_query / $this->users_per_page),
				'current' => $this->page,
				'base' => 'users.php?%_%',
				'format' => 'userspage=%#%',
				'add_args' => $args
			) );
			if ( $this->paging_text ) {
				$this->paging_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'acf' ) . '</span>%s',
					number_format_i18n( ( $this->page - 1 ) * $this->users_per_page + 1 ),
					number_format_i18n( min( $this->page * $this->users_per_page, $this->total_users_for_query ) ),
					number_format_i18n( $this->total_users_for_query ),
					$this->paging_text
				);
			}
		}
	}

	/**
	 * {@internal Missing Short Description}
	 *
	 * {@internal Missing Long Description}
	 *
	 * @since unknown
	 * @access public
	 *
	 * @return unknown
	 */
	function get_results() {
		return (array) $this->results;
	}

	/**
	 * Displaying paging text.
	 *
	 * @see do_paging() Builds paging text.
	 *
	 * @since unknown
	 * @access public
	 */
	function page_links() {
		echo $this->paging_text;
	}

	/**
	 * Whether paging is enabled.
	 *
	 * @see do_paging() Builds paging text.
	 *
	 * @since unknown
	 * @access public
	 *
	 * @return bool
	 */
	function results_are_paged() {
		if ( $this->paging_text )
			return true;
		return false;
	}

	/**
	 * Whether there are search terms.
	 *
	 * @since unknown
	 * @access public
	 *
	 * @return bool
	 */
	function is_search() {
		if ( $this->search_term )
			return true;
		return false;
	}
}
?>