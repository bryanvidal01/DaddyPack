<?php
$clrz_core->flush_rewrite_rules();
class Clrz_core_admin{
	
	function Clrz_core_admin()
	{
		add_action( 'admin_menu', array(&$this, 'addMenu') );
		
		if(!isset($_GET['page']) || $_GET['page']!='manage-clrzcore')
                    return;
		
		if (!isset($_COOKIE[ini_get('session.name')]))
                    session_start();
		global $clrz_core,$clrzdb;
		
		if (isset($_GET['flush_rules'])) {
                    $clrzdb->query('TRUNCATE TABLE clrz_core');
                    
                    wp_redirect(admin_url('?page=manage-clrzcore'));
                    die;
                }
		
		
		$_SESSION['clrz_core_init']='';
		
			$clrz_core->includes=array();
	
				if( $dh = opendir( TEMPLATEPATH.'/clrz' ) ) {
					while( ( $inc_file = readdir( $dh ) ) !== false ) {
						if( substr( $inc_file, -4 ) == '.php' && $inc_file[0]!='.') {
							if(TEMPLATEPATH.'/clrz/' . $inc_file!=__FILE__ && $inc_file!='clrz_controller.php' && $inc_file!='core.php')
							{
								
								$clrz_core->includes[]=$inc_file;
								include_once( TEMPLATEPATH.'/clrz/' . $inc_file );
								
								if($inc_file[0]!='_' && $inc_file!='core.php')
									$clrz_core->globals[]=substr( $inc_file,0, -4 );
							
							}
						}
					}
				}
				
				if( $dh = opendir( TEMPLATEPATH.'/clrz/controllers' ) ) {
					while( ( $inc_file = readdir( $dh ) ) !== false ) {
						if( substr( $inc_file, -4 ) == '.php' && $inc_file[0]!='.') {
							if(TEMPLATEPATH.'/clrz/controllers/' . $inc_file!=__FILE__ || $inc_file!='clrz_controller.php')
							{
								
								include_once( TEMPLATEPATH.'/clrz/controllers/' . $inc_file );
								
								
							
							}
						}
					}
				}
				
				
				

			$clrz_core->update_meta('clrz_includes',$clrz_core->includes );
			$clrz_core->update_meta('clrz_globals',array_unique($clrz_core->globals ));
			
		
			$clrz_core->_core_init();
			
			

		
			//$clrz_core->flush_rewrite_rules();
			
			add_filter('query_vars', array(&$this, 'query_vars'));
			add_action('rewrite_rules_array', array(&$this, 'generate_rules'));
			
			add_action('init',array(&$this,'wp_init'));
		
	}
	
	function wp_init()
	{
		
		global $wp_rewrite;
   		$wp_rewrite->flush_rules();
	
		
	}
	
	function init()
	{
		
		if(!is_dir(ABSPATH.'/wp-content/db-cache'))
		mkdir(ABSPATH.'/wp-content/db-cache',0755);
		
			$this->clrz_query_vars = $this->get_meta('clrz_query_vars');
			$this->rules =  $this->get_meta('clrz_steps_rules');
			$this->rewrite_rules =  ($this->get_meta('clrz_rewrite_rules'));
			$this->includes =  $this->get_meta('clrz_includes');
			$this->globals =  $this->get_meta('clrz_globals');
			$this->controllers_ref =  $this->get_meta('clrz_controllers_ref');
			
			ksort($this->rewrite_rules);
			ksort($this->rules);
			
			$_SESSION['clrz_core_init']['_query_vars']=$this->clrz_query_vars;
		 	$_SESSION['clrz_core_init']['_rewrite_rules']=$this->rewrite_rules;
		 	$_SESSION['clrz_core_init']['_base_rules']=$this->rules;
			

		
		
		
			
	}
	
	
	function query_vars($public_query_vars)
	{
			
			$_query_vars = array_merge($public_query_vars, array_unique($this->clrz_query_vars));
			
			
		
			
		return $_query_vars;
	}

	function generate_rules($rules)
	{
		
		
		
		//print_r($rules);
		//$wp_rewrite->rules = array_merge(array_unique($this->rewrite_rules) , $wp_rewrite->rules);
		//$rules->rules=array_unique($this->rewrite_rules)+$rules->rules;
		$rwR = (isset($this->rewrite_rules) && is_array($this->rewrite_rules)) ? array_unique($this->rewrite_rules) : '';
		
                $rules = (isset($rules->rules)) ? $rwR+$rules->rules : $rwR;
		//print_r($wp_rewrite->rules);
		//print_r($rules);
		 	//$wp_rewrite->flush_rules();
		 	
		return $rules;
	}
	
	function addMenu()
	{
		
			add_menu_page ( 'Clrz core', 'Clrz core', 'level_10', 'manage-clrzcore', array (&$this, 'pageManage' ) );
		
	}
	
	function pageManage()
	{
		global $wpdb;
		//print_r($wp_rewrite);
		
//	$res = $wpdb->get_results('SELECT * FROM '. $wpdb->posts.' WHERE post_category ="1" LIMIT 0,10');
	
	//print_r($res); 
		?>	
		<div class="wrap">
			<div id="icon-tools" class="icon32"><br /></div>
		<h2>Clrz core</h2>
		<h3>query vars</h3>
		<a href="<?php echo admin_url('?page=manage-clrzcore&flush_rules');?>">Flush rules</a>
		<hr/>
		<script>
		jQuery(document).ready(function () {

			jQuery('a.delete_conf').click(function(e){
			jQuery(this).parent().remove();
			
			});
			
			
			
			});

		
		
		
		</script>
		<style>
		.item:hover input{background:#999;color:#fff}
		</style>
		<form method="post">
				<?php
				
				if(isset($_POST['clrz_rules_k']))
				{
					
					$clrz_rules = array_combine($_POST['clrz_rules_k'],$_POST['clrz_rules']);	
					$this->update_meta('clrz_steps_rules',$clrz_rules);
				}
				if(isset($_POST['clrz_query_var']))
				{
						
					$this->update_meta('clrz_query_vars',$_POST['clrz_query_var']);
				}
				
				if(isset($_POST['clrz_rewrite_rules_k']))
				{
					
					$clrz_rewrite_rules = array_combine($_POST['clrz_rewrite_rules_k'],$_POST['clrz_rewrite_rules_v']);	
					$this->update_meta('clrz_rewrite_rules',$clrz_rewrite_rules);
				}
				$this->init();
				
				
				foreach($this->clrz_query_vars AS $query_var)
				{
				echo'<div class="item"><input type="text" name="clrz_query_var[]" value="'.$query_var.'"/><a class="delete_conf">[x]</a></div>';
					
				}
				
				?>
				<input type="submit"/>
		</form>
		<hr/>
		<h3>controllers Refs</h3>
		
				<?php
				
				foreach($this->controllers_ref AS $k=>$v)
				{
				echo'<div class="item"><input type="text" style="width:300px;" value="'.$k.'"/><input type="text" style="width:300px;" value="'.$v.'"/></div>';
					
				}
				
				?>
				
		
		<hr/>
		<h3>Includes</h3>
		<form method="post">
				<?php
				
				foreach($this->includes AS $k)
				{
				echo'<div class="item"><input type="text" style="width:300px;" name="clrz_includes[]" value="'.$k.'"/></div>';
					
				}
				
				?>
				<input type="submit"/>
		</form>
		<hr/>
		<h3>Globals</h3>
		<form method="post">
				<?php
				
				foreach($this->globals AS $k)
				{
				echo'<div class="item"><input type="text" style="width:300px;" name="clrz_globals[]" value="'.$k.'"/></div>';
					
				}
				
				?>
				<input type="submit"/>
		</form>
		<hr/>
		<h3>Controllers</h3>
		<form method="post">
				<?php
				
				foreach($this->rules AS $k=>$v)
				{
				echo'<div class="item"><input type="text" style="width:300px;" name="clrz_rules_k[]" value="'.$k.'"/><input type="text" style="width:300px;" name="clrz_rules[]" value="'.$v.'"/><a class="delete_conf">[x]</a></div>';
					
				}
				
				?>
				<input type="submit"/>
		</form>
		<hr/>
		<h3>rewrite_rules</h3>
		<form method="post">
				<?php
				
				foreach($this->rewrite_rules AS $k=>$v)
				{
				echo'<div class="item"><input type="text" style="width:500px;" name="clrz_rewrite_rules_k[]" value="'.$k.'"/><input type="text" style="width:500px;" name="clrz_rewrite_rules_v[]" value="'.$v.'"/><a class="delete_conf">[x]</a></div>';
					
				}
				
				?>
		<input type="submit"/>
		</form>		
		
		</div>
		<?php
	}
	
	
	function update_meta($key,$value)
	{
		
		global $wpdb;
		$value = (is_array($value)) ? (serialize($value)) : ($value);
		
		if(!$this->get_meta($key))
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO clrz_core ( meta_value, meta_key) VALUES ( %s, %s )",$value,$key ) );
		else	
			$result = $wpdb->query( $wpdb->prepare( "UPDATE clrz_core SET meta_value = %s  WHERE meta_key = %s ", $value,$key ) );
			
				
			

		return true;
		
	}
	
	function get_meta($key)
	{
		global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM clrz_core WHERE meta_key = %s ", $key ) );
		$result = @unserialize($result);
			
			return $result;	
		
	}
	
	
}
new Clrz_core_admin();
?>