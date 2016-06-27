<?php

/* wp page controller */

class clrz_controller {

    var $actionName;
    var $steps;
    var $action_title;

    function clrz_controller() {
        global $clrz_core;

        $clrz_core->addRules($this->steps, get_class($this));


        //	add_action( 'init', array(&$this, 'init'));
        add_action('template_redirect', array(&$this, 'dispatch'), 1);
    }

    function init() {
        session_start();
    }

    function checkURL() {
        global $clrz_core;
        $query = $_SERVER['QUERY_STRING'];
        $output = array();
        parse_str($query, $output);

        if (in_array('clrz_core_template', $output))
            array_shift($output);

        if (sizeof($output) == 0)
            return;

        $output = array_map('urlencode', $output);


        //echo key($output);
        if (in_array('clrz_' . key($output), $clrz_core->clrz_query_vars)) {

            $this->_redirect($this->get_query_var('core_template'), http_build_query($output));
        }
    }

    function dispatch() {

        header("HTTP/1.0 200 OK");

        if ($this->get_query_var('core_template'))
            $this->actionName = $this->get_query_var('core_template');

        /*         * ******************************** */
        /*         * ******************************** */
        /* ending controllers if no action */
        /*         * ******************************** */
        /*         * ******************************** */
        if (!$this->actionName)
            return;
        /*         * ******************************** */
        /*         * ******************************** */
        //print_r(array_keys($this->steps));

        if (!in_array($this->actionName, array_keys($this->steps)))
            return;


        $this->checkURL();



        $this->checkRules();

        if ($_POST)
            $this->setupData();


        if (method_exists($this, $this->actionName . 'Action'))
            call_user_func(array($this, $this->actionName . 'Action'));

        global $wp_query;
        $wp_query->is_404 = false;

        add_filter('wp_title', array(&$this, 'actionTitle'));

        if ($this->get_query_var('action')) {
            if (method_exists($this, 'do' . $this->get_query_var('action') . 'Action'))
                call_user_func(array($this, 'do' . $this->get_query_var('action') . 'Action'));
        }

        /* PostActions Controllers */
        if ($_POST) {
            if (method_exists($this, $this->actionName . 'PostAction'))
                call_user_func(array($this, $this->actionName . 'PostAction'));
        }


        if (defined('IS_JSON') && IS_JSON === true) {
            echo $this->_encodeResponse();
            die;
        }
    }

    function actionTitle($title) {

        return $this->action_title . ' ';
    }

    function setSteps($array) {
        $this->steps = array_merge((array) $this->steps, $array);
    }

    function getPageId() {
        return array_keys((array) $this->steps, $this->actionName);
    }

    function getSteps() {

        return $this->steps;
    }

    function get_query_var($var) {
        global $clrz_core;

        return $clrz_core->get_query_var($var);
    }

    function getStep($key='', $action='') {
        global $clrz_core;
        $action = ($action) ? $action : $this->actionName;
        $current_step = $clrz_core->steps_settings[$this->actionName];
        $current_step = array('actionName' => $action, 'pageId' => '' . $current_step[0] . '', 'rule' => $current_step[1], 'rewrite' => $current_step[2]);
        if (!$key)
            return $current_step;
        else
            return $current_step[$key];
        /* if($action)
          {
          $current_step = array('actionName'=>$action,'pageId'=>''.$steps[$action][0].'','rule'=>$steps[$action][1],'rewrite'=>$steps[$action][2]);
          if(!$key)
          return $current_step;
          else
          return $current_step[$key];
          }

          global $post;



          foreach($steps AS $k=>$step)
          {
          if(in_array($this->actionName,(array)$step))
          {
          $current_step = array('actionName'=>$k,'pageId'=>''.$step[0].'','rule'=>$step[1],'rewrite'=>$step[1]);
          }
          }
         */
        if (!$key)
            return $current_step;
        else
            return $current_step[$key];
    }

    function checkRules() {

        switch ($this->getStep('rule', $this->actionName)) :
            case 'loggedin':

                if (!is_user_logged_in()) {
                    $this->addError('controllers', __('Vous devez être connecté', 'lense'));

                    $this->_redirect('register');
                    die;
                }
                break;
            case 'notloggedin':

                if (is_user_logged_in()) {

                    //$this->addError('controllers',__('You must be logged in','jdg'));
                    $this->_redirect('profil');
                    die;
                }
                break;

        endswitch;
    }

    function _getUrl($step, $params=false) {
        global $clrz_core;

        return $clrz_core->_getUrl($step, $params);
        /* $url = get_page_link($this->getStep('pageId',$step));
          if($params)
          {
          //$params = implode('&',$params);
          $url = (strpos('?',$url)!==false) ? $url.'&'.$params : $url.'?'.$params;

          }


          return $url; */
    }

    function _redirect($step, $params=false) {
        if (strpos($step, '/') !== false)
            $url = $step;
        else
            $url = $this->_getUrl($step, $params);

        if (IS_JSON === true) {

            echo $this->_encodeResponse();

            die;
            return false;
        }
        wp_redirect($url);
        die;
    }

    function setupData() {
        foreach ((array) $this->fields[$this->actionName] AS $field => $info) {
            
            $_SESSION['clrz_controller']['data'][$this->actionName][$field] = (isset($_POST[$field])) ? $this->getCleanedValue($_POST[$field]) : '';
        }
    }

    function getCleanedValue($value) {

        if (is_array(($value)))
            return $value;
        elseif (is_array(@unserialize($value)))
            return $value;
        else
            return htmlentities($value);
    }

    function setFormData($key, $value) {
        $_SESSION['clrz_controller']['data'][$this->actionName][$key] = $this->getCleanedValue($value);
    }

    function getFormDataArray() {
        return $_SESSION['clrz_controller']['data'][$this->actionName];
    }

    function getFormData($field, $action='') {
        if (empty($action))
            $action = $this->actionName;
        $returndata = (isset($_SESSION['clrz_controller']['data'][$action][$field])) ? $_SESSION['clrz_controller']['data'][$action][$field] : '';
        return ($returndata);
    }

    function unsetData($field='') {
        if ($field)
            unset($_SESSION['clrz_controller']['data'][$this->actionName][$field]);
        else
            unset($_SESSION['clrz_controller']['data'][$this->actionName]);
    }

    function addError($key, $error) {
        $_SESSION['clrz_controller']['errors'][$key] = array($key, $error);
    }

    function addSuccess($key, $error) {
        $_SESSION['clrz_controller']['success'][$key] = array($key, $error);
    }

    function getErrors() {
        return (isset($_SESSION['clrz_controller']['errors'])) ? $_SESSION['clrz_controller']['errors'] : array();
    }

    function getSuccess() {
        return (isset($_SESSION['clrz_controller']['success'])) ? $_SESSION['clrz_controller']['success'] : array();
    }

    function showMessages($slashes=false) {
        $items = '';
        $close_error = '<a class="close_error" href="#">[x]</a>';
        foreach ((array) $this->getErrors() AS $error) {
            $items.= '<li class="error">' . $error[1] . ' ' . $close_error . '</li>';
        }

        foreach ((array) $this->getSuccess() AS $success) {
            $items.= '<li class="success">' . $success[1] . ' ' . $close_error . '</li>';
            $success = 'class="clrz_success"';
        }

        if ($items == '')
            return;
        $retour = '<ul id="clrz_messages" ' . $success . '>';
        $retour.= $items;
        $retour.='</ul>';
        if ($slashes)
            echo addslashes($retour);
        else
            echo $retour;
        unset($_SESSION['clrz_controller']['errors']);
        unset($_SESSION['clrz_controller']['success']);
    }

    function _encodeResponse() {
        $errors = array();
        $successes = array();
        foreach ((array) $this->getErrors() AS $error)
            $errors[] = $error[1];
        foreach ((array) $this->getSuccess() AS $success)
            $successes[] = $success[1];
        $errors = (empty($errors)) ? '' : $errors;
        $successes = (empty($successes)) ? '' : $successes;
        $json = array('errors' => $errors, 'success' => $successes);


        unset($_SESSION['clrz_controller']['errors']);
        unset($_SESSION['clrz_controller']['success']);

        return json_encode($json);
    }

    function getFields() {
        return $this->fields[$this->actionName];
    }

    function helperInput($input, $value='', $class='', $labelpos='') {
        $item = $this->fields[$this->actionName][$input];


        if ($value == 'clrz_user') {
            global $clrz_user;

            $value = $clrz_user->get($input);
        }


        $value = ($value) ? $value : stripslashes($this->getFormData($input));
        $label = '<label for="' . $input . '">' . $item['label'] . '</label>';


        $value_origin = $value;
        if ($item['type'] == 'checkbox')
            $value = ($value) ? 'CHECKED=CHECKED value="1"' : 'value="1"';
        else
            $value = 'value="' . $value . '"';

        if ($item['type'] == 'textarea')
            $elinput = '<div class="' . $class . '"><textarea name="' . $input . '" id="' . $input . '">' . $value_origin . '</textarea></div>';
        else
            $elinput='<div class="' . $class . '"><input name="' . $input . '" id="' . $input . '" type="' . $item['type'] . '"  ' . $value . '/></div>';


        if (!$labelpos)
            $labelpos = ($item['type'] == 'checkbox') ? 'after' : 'before';
        switch ($labelpos) :
            case('before') :
                echo $label . ' ' . $elinput;
                break;
            case('after') :
                echo $elinput . ' ' . $label;
                break;
            case('none') :
                echo $elinput;
                break;
        endswitch;
    }

    function getSelects($cle, $section=0) {
        global $wpdb, $clrz_user;
        $order = '';
        if ($cle == 'country' || $cle == 'pays')
            $order = ' ORDER BY name ASC';
        $query = " SELECT * FROM clrz_select_metas WHERE cle LIKE '$cle' $order";
        $selects = $wpdb->get_results($query);

        if ($section == 0)
            $selectValue = get_user_meta($clrz_user->getData()->ID, $cle);
        else
            $selectValue = $section;

        foreach ((array) $selects as $s) {
            if ($selectValue) {
                if ($s->value == $selectValue)
                    $select = ' selected="selected" ';
                else
                    $select='';
            }
            else {
                if ($s->default)
                    $select = ' selected="selected" ';
                else
                    $select='';
            }

            echo '<option ' . $select . ' value="' . $s->value . '">' . $s->name . '</option>';
        }
    }

    function checkGlobalFilesImage($maxsize=999999) {

        require_once(ABSPATH . '/wp-admin/includes/image.php');

        $inc_file = 1;
        foreach ($_FILES as $file) {

            if (!$file['tmp_name'])
                continue;
            if ($file['size'] > $maxsize)
                $this->addError($field . '_size', 'Le fichier N°' . $inc_file . ' ' . __(' ne doit pas dépasser 1MB ', 'lense'));


            if (!file_is_displayable_image($file['tmp_name']))
                $this->addError($field . '_type', 'Le fichier N°' . $inc_file . ' ' . __(' doit être une image ', 'lense'));
            $inc_file++;
        }
    }

    function checkNewsletter($mail='', $adderror = true) {
        global $wpdb, $clrz_core;
        $mail = ($this->getFormData('mail')) ? strip_tags($this->getFormData('mail')) : $mail;
        $users = $wpdb->get_row("SELECT COUNT(ID) AS count FROM newsletter WHERE email='" . $mail . "'");
        if ($users->count > 0):
            if ($adderror)
                $this->addError('emailnewsletter', 'Vous êtes déjà inscrit aux newsletters Lense');
            else
                return false;
        endif;

        if (empty($mail))
            $this->addError('emailnewsletter', 'Vous devez donner votre email');


        if (!$clrz_core->checkMail($mail))
            $this->addError('email', 'Le format de votre mail est invalide');

        if (!$this->getErrors())
            return true;
        else
            return false;
    }

}

