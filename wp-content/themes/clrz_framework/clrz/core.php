<?php

/* 	TODO 



 */


include_once( TEMPLATEPATH . '/clrz/install.php' );
include_once( TEMPLATEPATH . '/clrz/class/ez_sql_core.php' );
include_once( TEMPLATEPATH . '/clrz/class/ez_sql_mysql.php' );

require_once ABSPATH . WPINC . '/class-phpmailer.php';
require_once ABSPATH . WPINC . '/class-smtp.php';

global $clrzdb;
$clrzdb = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
$clrzdb->cache_timeout = 1;
$clrzdb->cache_dir = ABSPATH . '/wp-content/db-cache';
$clrzdb->use_disk_cache = false;

class Clrz_core {

    var $clrz_query_vars = Array('clrz_core_template', 'clrz_page');
    var $rules;
    var $rewrite_rules = Array();
    var $TEMPLATEPATH = TEMPLATEPATH;
    var $globals = array('clrz_core');
    var $IS_JSON = false;

    function Clrz_core() {
        session_start();

        if ($_SERVER['HTTP_ACCEPT'] == 'application/json') {
            $this->IS_JSON = true;
            define('IS_JSON', true);
        }



        $this->clrz_query_vars += (array) $this->get_meta('clrz_query_vars');
        $this->globals += (array) $this->get_meta('clrz_globals');

        $this->rules = (array) $this->get_meta('clrz_steps_rules');
        $this->rewrite_rules = (array) $this->get_meta('clrz_rewrite_rules');
        $this->steps_settings = (array) $this->get_meta('clrz_steps_settings');
        $this->controllers_ref = (array) $this->get_meta('clrz_controllers_ref');
        $this->includes = (array) $this->get_meta('clrz_includes');

        add_action('rewrite_rules_array', array(&$this, 'push_rules'));
        add_action('template_redirect', array(&$this, 'template_redirect'));
        add_filter('query_vars', array(&$this, 'query_vars'));
        add_action('init', array(&$this, 'init'));
    }

    function init() {


        add_filter('request', array(&$this, 'cancelRequest'));
    }

    function cancelRequest($params) {


        if (!isset($params['clrz_core_template']))
            return $params;
        $this->controller_action = $params['clrz_core_template'];
        $this->loadController();

        // ID d'un post non existant
        $params['p'] = 1;
        $params['static'] = 1;

        return $params;
    }

    function _core_flush() {
        unset($_SESSION['clrz_core_init']);
        $_SESSION['clrz_core_init'] = '';
    }

    /* building custom url */

    function _core_init() {


        // store these for cache in prod mode
        $this->update_meta('clrz_query_vars', array_unique($this->clrz_query_vars));
        $this->update_meta('clrz_steps_rules', array_unique($this->rules));
        $this->update_meta('clrz_rewrite_rules', array_unique($this->rewrite_rules));
        $this->update_meta('clrz_steps_settings', $this->steps_settings);
        $this->update_meta('clrz_controllers_ref', $this->controllers_ref);
    }

    function loadController() {
        if (is_admin())
            return;


        $this->currentController = $this->controllers_ref[$this->controller_action];
        require TEMPLATEPATH . '/clrz/controllers/' . $this->currentController . '.php';
    }

    function getController() {
        global ${$this->currentController};

        return ${$this->currentController};
    }

    function update_meta($key, $value) {

        global $wpdb;
        $value = (is_array($value)) ? (serialize($value)) : ($value);
        if (!$value) {
            $result = $wpdb->query($wpdb->prepare("UPDATE clrz_core SET meta_value = ''  WHERE meta_key = %s ", $value, $key));
            return;
        }
        if (!$this->meta_exists($key))
            $result = $wpdb->query($wpdb->prepare("INSERT INTO clrz_core ( meta_value, meta_key) VALUES ( %s, %s )", $value, $key));
        else
            $result = $wpdb->query($wpdb->prepare("UPDATE clrz_core SET meta_value = %s  WHERE meta_key = %s ", $value, $key));




        return true;
    }

    function meta_exists($key) {

        global $wpdb;
        $result = $wpdb->get_var($wpdb->prepare("SELECT meta_key FROM clrz_core WHERE meta_key = %s ", $key));

        if ($result)
            return true;
        else
            return false;
    }

    function get_meta($key) {
        global $wpdb, $clrzdb;
        $clrzdb->cache_queries = true;
        $result = $clrzdb->get_var($wpdb->prepare("SELECT meta_value FROM clrz_core WHERE meta_key = %s ", $key));
        $clrzdb->cache_queries = false;


        $result = @unserialize($result);

        return $result;
    }

    function get_query_var($var) {

        return urldecode(get_query_var('clrz_' . $var));
    }

    function query_vars($public_query_vars) {

        $_query_vars = array_merge($public_query_vars, array_unique($this->clrz_query_vars));


        return $_query_vars;
    }

    function addRules($steps, $ref='') {

        if (!is_admin())
            return;


        global $wp_rewrite;


        foreach ($steps AS $k => $step) {

            $this->controllers_ref[$k] = $ref;
            $this->rules[$k] = $step[2] . '/';
            $this->rewrite_rules[$step[2] . '/?$'] = 'index.php?clrz_core_template=' . $k;
            $this->rewrite_rules[$step[2] . '/page/?([0-9]{1,})/?$'] = 'index.php?clrz_core_template=' . $k . '&clrz_page=$matches[1]';
        }

        $this->steps_settings = array_merge($steps, $this->steps_settings);

        //print_r($this->controllers_ref);
    }

    function push_rules($rewrite_rules) {

        $rewrite_rules = array_unique($this->rewrite_rules) + $rewrite_rules;

        return $rewrite_rules;
    }

    function generate_rules($rules) {


        global $wp_rewrite;

        $rules->rules = array_unique($this->rewrite_rules) + $rules->rules;

        return $rules;
    }

    function template_redirect() {

        $clrz_core_template = get_query_var('clrz_core_template');
        $clrz_core_template_filter = (isset($this->section)) ? $this->section : '';

        foreach ($this->globals AS $class)
            global ${$class};

        if (!$clrz_core_template)
            return;

        $this->coretpl = $clrz_core_template;

        $tpl = ($clrz_core_template_filter) ? $clrz_core_template_filter : $clrz_core_template;



        include (TEMPLATEPATH . "/template-$tpl.php");

        exit;
    }

    function _getUrl($step, $params=false) {

        $url = get_bloginfo('url') . '/' . $this->rules[$step];

        if ($params) {
            parse_str($params, $out);
            $_params = '';
            foreach ($out AS $k => $v) {
                if ($v) {
                    if ($k[0] == '_')
                        $_params.=$v . '/';
                    else
                        $_params.=$k . '/' . $v . '/';
                }
            }

            $url.=$_params;
        }

        return $url;
    }

    function flush_rewrite_rules() {
        global $wp_rewrite;

        //$wp_rewrite->flush_rules();
    }

    function magic_rule($step, $params) {
        global $wp_rewrite;

        parse_str($params, $query_vars);

        $query_rule = $this->rules[$step];
        $query_string = '';
        $i = 1;
        foreach ($query_vars AS $k => $val) {
            if (is_numeric($val))
                $val = '?([0-9]{1,})';
            elseif ($val)
                $val = '([^/]+)';

            // init wp query vars	
            $this->clrz_query_vars[] = 'clrz_' . $k;


            // build rewrite rule
            if ($k[0] == '_')
                $query_rule.= $val . '/';
            else
                $query_rule.=$k . '/' . $val . '/';


            $query_string.='&clrz_' . $k . '=$matches[' . $i . ']';
            $i++;
        }

        $this->rewrite_rules[$query_rule . '?$'] = 'index.php?clrz_core_template=' . $step . $query_string;

        // deprecated
        $clrzqueryvar = (isset($this->clrz_query_vars) && is_array($this->clrz_query_vars)) ? $this->clrz_query_vars : array();
        $rewriterules = (isset($this->rewrite_rules) && is_array($this->rewrite_rules)) ? $this->rewrite_rules : array();
        $sessionquery_vars = (isset($_SESSION['clrz_core_init']['_query_vars'])) ? $_SESSION['clrz_core_init']['_query_vars'] : array();
        $sessionrewrite_rules = (isset($_SESSION['clrz_core_init']['_rewrite_rules'])) ? $_SESSION['clrz_core_init']['_rewrite_rules'] : array();
        $_SESSION['clrz_core_init']['_query_vars'] = array_unique((array) $sessionquery_vars + (array) $clrzqueryvar);
        $_SESSION['clrz_core_init']['_rewrite_rules'] = array_unique((array) $sessionrewrite_rules + (array) $rewriterules);
    }

    /* tpl func */

    function load_template($_template_file) {

        global $posts, $post, $wp_did_header, $wp_did_template_redirect, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

        foreach ($this->globals AS $class)
            global ${$class};

        if (is_array($wp_query->query_vars))
            extract($wp_query->query_vars, EXTR_SKIP);

        include $_template_file;
    }

    function get_sidebar($name = null) {

        foreach ($this->globals AS $class)
            global ${$class};

        $templates = array();

        if (isset($name))
            $templates[] = "sidebar-{$name}.php";

        $templates[] = "sidebar.php";

        if (!locate_template($templates))
            $this->load_template(get_theme_root() . '/default/sidebar.php');
        else
            $this->load_template(locate_template($templates));
    }

    /*  TODO encryption method for SSO webservice WPMU */

    function _encrypt($text) {

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $key = "jnze5q1�:zefozHNZEc1a8";
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv));
    }

    function _decrypt($text) {

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $key = "jnze5q1�:zefozHNZEc1a8";
        //I used trim to remove trailing spaces
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, $iv));
    }

    function getSelect($name, $value) {
        global $wpdb;
        $query = " SELECT * FROM acf_selects WHERE name LIKE '$name' AND value='$value' ";
        $select = $wpdb->get_row($query);
        return $select;
    }

    function showMessages() {
        $items = '';
        if (!isset($_SESSION['clrz_controller']['errors']))
            $_SESSION['clrz_controller']['errors'] = array();
        foreach ((array) $_SESSION['clrz_controller']['errors'] AS $error) {
            $items.= '<li class="error">' . $error[1] . '</li>';
        }

        if (!isset($_SESSION['clrz_controller']['success']))
            $_SESSION['clrz_controller']['success'] = array();
        foreach ((array) $_SESSION['clrz_controller']['success'] AS $success) {
            $items.= '<li class="success">' . $success[1] . '</li>';
        }
        if ($items != '') {
            echo'<ul id="clrz_messages">';
            echo $items;
            echo'</ul>';

            unset($_SESSION['clrz_controller']['errors']);
            unset($_SESSION['clrz_controller']['success']);
        }
    }
    
    
    function sendmail($modele='inscription', $donnees=array(), $sujet='', $destinataires=array(), $frommail='', $fromname=''){

        if($frommail=='')
            $frommail = get_bloginfo('admin_email');
        if($fromname=='')
            $fromname = get_bloginfo('name');
        $mail             = new PHPMailer();
        $body             = file_get_contents(TEMPLATEPATH.'/mails/header.html');
        $body             .= file_get_contents(TEMPLATEPATH.'/mails/modele-'.$modele.'.html');
        $body             .= file_get_contents(TEMPLATEPATH.'/mails/footer.html');
        $body             = eregi_replace("[\]",'',$body);
        $body             = ereg_replace("@URL_SITE@",get_option('home'),$body);
        $body             = ereg_replace("@IMAGELOGO@",get_bloginfo('template_url').'/images/logo.gif',$body);
        $body             = ereg_replace("@NOM_SITE@",get_bloginfo('name'),$body);
        $body             = ereg_replace("@TEMPLATEURL@",get_bloginfo('template_url'),$body);

        foreach($donnees as $donnee=>$val):
            $body         = ereg_replace("@".strtoupper($donnee)."@",utf8_decode($val),$body);
        endforeach;
        $mail->From       = $frommail;
        $mail->FromName   = $fromname;
        $mail->Subject    = stripslashes(utf8_decode('['.get_option('blogname').'] '.$sujet));
        $mail->MsgHTML($body);
        foreach($destinataires as $destinataire):
            $mail->AddAddress($destinataire);
        endforeach;
        
		do_action_ref_array( 'phpmailer_init', array( &$mail ) );
		
        if($mail->send())
            return true;
        else
            return false;

    }
    
    function checkmail(){
        
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
                return false;
            }
        }
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }
    

}

global $clrz_core;
$clrz_core = new Clrz_core();


include( TEMPLATEPATH . '/clrz/class/clrz_controller.php');



if (!is_admin())
    if($clrz_core->includes)
        foreach ($clrz_core->includes AS $files)
            if($files!='')
                include( TEMPLATEPATH . '/clrz/' . $files );




if (is_admin()) {
    include( TEMPLATEPATH . '/clrz/class/core_admin.php' );
    return;
}
