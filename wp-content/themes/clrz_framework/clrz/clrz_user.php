<?php

class Clrz_user {

    var $user;
    var $user_metas = Array('first_name', 'last_name');
    var $errors;
    var $avatars_url = '/wp-content/uploads/avatars/';
    var $avatar_sizes = array(80, 34);

    function Clrz_user($myUser='') {

        /* if($myUser == 0)
          return; */

        if (is_object($myUser))
            $this->user = $myUser;
        elseif (is_numeric($myUser))
            $this->user = get_userdata($myUser);
        elseif (!empty($myUser))
            $this->user = get_userdatabylogin($myUser);
        else
            $this->user = wp_get_current_user();


        $this->_construct();
    }

    function _construct() {
        global $clrz_Profil;
        $this->errors = $clrz_Profil;
    }

    function getmyFriends() {
        if (!$this->myFriends)
            $this->myFriends = (array) $this->getFriendsId();
        return $this->myFriends;
    }

    function getmyFutureFriends() {
        if (!$this->myFutureFriends)
            $this->myFutureFriends = (array) $this->getFriendsId(false);
        return $this->myFutureFriends;
    }

    function is_future_friend($friend_id) {

        if (in_array($friend_id, $this->getmyFutureFriends()))
            return true;
        else
            return false;
    }

    function is_friend($friend_id) {

        if (in_array($friend_id, $this->getmyFriends()))
            return true;
        else
            return false;
    }

    /* stats */

    function setLastActionTime() {
        global $wpdb;

        @$wpdb->query('UPDATE ' . $wpdb->usermeta . ' SET meta_value = "' . time() . '" WHERE meta_key = "clrz_user_lastaction" AND user_id = "' . $this->user->ID . '"');
    }

    function setProfileViews() {
        global $current_user;
        get_currentuserinfo();


        if ($current_user->ID == $this->user->ID)
            return;

        global $wpdb;

        @$wpdb->query('UPDATE ' . $wpdb->usermeta . ' SET meta_value = (meta_value+1) WHERE meta_key = "clrz_user_views" AND user_id = "' . $this->user->ID . '"');
    }

    function get($data) {

        return (isset($this->user->{$data})) ? $this->user->{$data} : '';
    }

    function getSelectData($key) {
        global $wpdb, $clrz_user;
        $query = " SELECT name FROM clrz_select_metas WHERE cle = '$key' AND value = '" . $this->get($key) . "' ";

        $res = $wpdb->get_row($query);
        return $res->name;
    }

    /* go func yourself */

    function getData() {

        return $this->user;
    }

    function getPermalink() {

        return '/membre/' . urlencode($this->user->user_login) . '/';
    }

    function showConf($conf) {
        $data = $this->get('user_conf');
        if ($data[$conf] == '1')
            return true;

        return false;
    }

    function getNumComments($return=false) {
        global $wpdb;
        $res = $wpdb->get_row('SELECT COUNT(*) AS totalComs FROM ' . $wpdb->comments . ' WHERE user_id = "' . $this->user->ID . '" GROUP BY user_id');

        $totalComs = ($res->totalComs) ? $res->totalComs : 'No';

        if ($return)
            $NumComments = $totalComs;
        else
            $NumComments = ($totalComs > 1) ? $totalComs . ' comments' : $totalComs . ' comment';


        return $NumComments;
    }

    function getLinks() {
        $titres = $this->get('links_title');

        if (!$this->get('links_title') || $titres[0] == 'titre') {
            $links_title = array(get_bloginfo('name'));
            $links_url = array(get_bloginfo('url'));
        } else {
            $links_title = $this->get('links_title');
            $links_url = $this->get('links_url');
        }
        return array_combine($links_title, $links_url);
    }

    function getNumPosts($return=false) {
        global $wpdb;
        $res = $wpdb->get_row('SELECT COUNT(*) AS totalPosts FROM ' . $wpdb->posts . ' WHERE post_author = "' . $this->user->ID . '" AND post_status = "publish" GROUP BY post_author');

        $totalPosts = ($res->totalPosts) ? $res->totalPosts : 'No';

        if ($return)
            $NumPosts = $totalPosts;
        else
            $NumPosts = ($totalComs > 1) ? $totalComs . ' Posts' : $totalPosts . ' Post';


        return $NumPosts;
    }

    function getAge() {

        return round(((time() - mysql2date('U', $this->get('birthday'))) / (3600 * 24)) / 365);
    }

    function getRegistered($format = '') {
        $format = (empty($format)) ? get_option('date_format') : $format;

        //print_r($this->user);
        return mysql2date($format, $this->user->user_registered, false);
    }

    function nicenamesOptions() {

        $selected[$this->user->display_name] = 'SELECTED=SELECTED';
        $data = '';

        if ($this->user->first_name)
            $data .= '<option value="' . $this->user->first_name . '" ' . $selected[$this->user->first_name] . '>' . $this->user->first_name . '</option>';
        if ($this->user->last_name)
            $data .= '<option value="' . $this->user->last_name . '" ' . $selected[$this->user->last_name] . '>' . $this->user->last_name . '</option>';
        if (($this->user->last_name) && ($this->user->first_name))
            $data .= '<option value="' . $this->user->first_name . ' ' . $this->user->last_name . '" ' . $selected[$this->user->first_name . ' ' . $this->user->last_name] . '>' . $this->user->first_name . ' ' . $this->user->last_name . '</option>';
        if ($this->user->nickname)
            $data .= '<option value="' . $this->user->nickname . '" ' . $selected[$this->user->nickname] . '>' . $this->user->nickname . '</option>';
        if ($this->user->user_login)
            $data .= '<option value="' . $this->user->user_login . '" ' . $selected[$this->user->user_login] . '>' . $this->user->user_login . '</option>';
        echo $data;
    }

    function getAvatar($size='64') {
        //use custom avatar upload profile OR gravatar api
        //$avatar = ($this->user->avatar) ? '<img class="avatar" alt="Avatar" src="'.$this->avatars_url.$size.'x'.$size.'_'.$this->user->avatar.'"/>' : get_avatar( $this->user->ID, $size, $default = get_bloginfo('template_directory').'/images/profil.jpg' );
        //$avatar = ($this->user->avatar) ? '<img class="avatar" alt="Avatar" src="'.$this->avatars_url.$size.'x'.$size.'_'.$this->user->avatar.'"/>' : get_avatar( $this->user->ID, $size, $default = get_bloginfo('template_directory').'/images/profile/profile'.$size.'.jpg' );
        $avatar = (isset($this->user->avatar)) ? '<img class="avatar" alt="Avatar" src="' . $this->avatars_url . $size . 'x' . $size . '_' . $this->user->avatar . '"/>' : '<img class="avatar" alt="Avatar" src="' . get_bloginfo('template_directory') . '/images/profile/profile' . $size . '.jpg"/>';


        echo $avatar;
    }

    function getAvatarURL($size='64') {
        //use custom avatar upload profile OR gravatar api
        //$avatar = ($this->user->avatar) ? '<img class="avatar" alt="Avatar" src="'.$this->avatars_url.$size.'x'.$size.'_'.$this->user->avatar.'"/>' : get_avatar( $this->user->ID, $size, $default = get_bloginfo('template_directory').'/images/profil.jpg' );
        //$avatar = ($this->user->avatar) ? '<img class="avatar" alt="Avatar" src="'.$this->avatars_url.$size.'x'.$size.'_'.$this->user->avatar.'"/>' : get_avatar( $this->user->ID, $size, $default = get_bloginfo('template_directory').'/images/profile/profile'.$size.'.jpg' );
        return (isset($this->user->avatar)) ? $this->avatars_url . $size . 'x' . $size . '_' . $this->user->avatar : get_bloginfo('template_directory') . '/images/profile/profile' . $size . '.jpg';
    }

    function checkMail($email) {

        $check = eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
        if (!$check) {
            $this->errors->addError('profile_email', __('<strong>ERROR</strong>: Please verify Email synthax', 'acf'));
            return false;
        }

        if ($user = get_user_by_email($email)) {
            if ($user->user_email == $this->user->user_email)
                return true;
            else {
                $this->errors->addError('profile_email', __('<strong>ERROR</strong>: Email already exists', 'acf'));
                return false;
            }
        }

        return true;
    }

    /*     * **  UPDATE FUNCTIONS *** */

    function updateUserData() {
        if (!$_POST)
            return;

        global $wpdb;

        //$nicename = ($_POST['user_nicename']) ? $_POST['user_nicename'] : $this->user->user_nicename;
        $nicename = ($_POST['nickname']) ? $_POST['nickname'] : $this->user->user_login;
        //$user_url = ($_POST['user_url']) ? $_POST['user_url'] : $this->user->user_url;
        $user_email = ( $this->checkMail($_POST['user_email']) ) ? $_POST['user_email'] : $this->user->user_email;
        $_POST['description'] = strip_tags($_POST['description'], '<br>');

        $wpdb->query('UPDATE ' . $wpdb->users . ' SET display_name = "' . $nicename . '",user_email = "' . $user_email . '" WHERE ID = "' . $this->user->ID . '"');

        foreach ($this->user_metas AS $data)
            update_usermeta($this->user->ID, $data, $_POST[$data]);
    }

    function update($datas) {
        /* global $wpdb;
          $upd = array();
          foreach($datas AS $k=>$v)
          $upd[] = $k.'="'.$v.'"'; */

        //$querystring = implode(',',$upd);
        //die( 'UPDATE '.$wpdb->users.' SET '.$querystring.' WHERE ID = "'.$this->user->ID.'"');
        //$wpdb->query('UPDATE '.$wpdb->users.' SET '.$querystring.' WHERE ID = "'.$this->user->ID.'"');	

        $datas+=array('ID' => $this->user->ID);
        wp_update_user($datas);
    }

    function uploadAvatar() {


        $file = $_FILES['avatar'];

        if (!isset($file['tmp_name']))
            return;

        include_once dirname(__FILE__) . '/class/class.upload.php';

        $my_upload = new file_upload;
        $my_upload->upload_dir = ABSPATH . $this->avatars_url;
        $my_upload->extensions = array(".png", ".jpg", ".jpeg", ".gif");
        $my_upload->max_length_filename = 50;
        $my_upload->rename_file = true;
        $my_upload->the_temp_file = $file['tmp_name'];
        $my_upload->the_file = $file['name'];

        if ($my_upload->upload()) {  //		if ($my_upload->upload($new_name)) 					
            if (!class_exists(Thumbnail))
                include ABSPATH . '/wp-content/plugins/thumbTheme/class/_class.crop.php';

            $thumb = new Thumbnail(ABSPATH . $this->avatars_url . $my_upload->file_copy);

            //$thumb->crop($_GET['crop_x'],$_GET['crop_y'],$_GET['crop_w'],$_GET['crop_h']);
            $size = GetImageSize(ABSPATH . $this->avatars_url . $my_upload->file_copy);


            foreach ($this->avatar_sizes AS $size) {
                /* $thumb = new Thumbnail(ABSPATH.$this->avatars_url.$my_upload->file_copy);
                  $size = GetImageSize(ABSPATH.$this->avatars_url.$my_upload->file_copy); */
                if ($size[0] < $size[1])
                    $thumb->resize($size, 0);
                else
                    $thumb->resize(0, $size); //$thumb->resize(70,70);

                $thumb->cropFromCenter($size);
                $thumb->save(ABSPATH . $this->avatars_url . $size . 'x' . $size . '_' . $my_upload->file_copy, 100);
            }
            /* if($size[0] < $size[1])
              $thumb->resize(70,0);
              else
              $thumb->resize(0,70);//$thumb->resize(70,70);
              $thumb->cropFromCenter(70);
              $thumb->save(ABSPATH.$this->avatars_url.'70x70_'.$my_upload->file_copy,100);

              if($size[0] < $size[1])
              $thumb->resize(100,'');
              else
              $thumb->resize('',100);

              $thumb->cropFromCenter(100);
              $thumb->save(ABSPATH.$this->avatars_url.'100x100_'.$my_upload->file_copy,100);
             */
            if ($this->user->avatar) {

                unlink(ABSPATH . $this->avatars_url . $this->user->avatar);

                foreach ($this->avatar_sizes AS $size) {

                    unlink(ABSPATH . $this->avatars_url . $size . 'x' . $size . '_' . $this->user->avatar);
                }

                /* unlink(ABSPATH.$this->avatars_url.$this->user->avatar);
                  unlink(ABSPATH.$this->avatars_url.'100x100_'.$this->user->avatar);
                  unlink(ABSPATH.$this->avatars_url.'70x70_'.$this->user->avatar); */
            }

            update_usermeta($this->user->ID, 'avatar', $my_upload->file_copy);
            return true;
        }
        /* else
          $this->errors->add('profile_avatar', __('<strong>ERROR</strong>: '.$my_upload->show_error_string() )); */
    }

    function setPassword() {
        if (trim($_POST['pass1']) != "") {

            if (trim($_POST['pass2']) == "") {
                //$this->errors->addError('profile_pass1', __("<strong>ERROR</strong>:Please confirm password  ")); 
            }
            if (trim($_POST['pass2']) != trim($_POST['pass1'])) {
                //$this->errors->addError('profile_pass2', __("<strong>ERROR</strong>: Passwords don't match  ")); 
            }
            if (strpos(" " . $_POST['pass1'], "\\")) {
                //$this->errors->addError('profile_pass1', __("<strong>ERROR</strong>: Passwords don't match  ")); 
            } else {
                $updatedata = array('user_pass' => $_POST['pass1'], 'ID' => $this->user->ID);
                wp_update_user($updatedata);
                /* global $wpdb;
                  $wpdb->query('UPDATE '.$wpdb->users.' SET user_pass = "'.md5(trim(mysql_real_escape_string($_POST['pass1']))).'" WHERE ID = "'.$this->user->ID.'"');
                  //wp_clear_auth_cookie();
                  $cookie = wp_set_auth_cookie($this->user->ID, true, false);
                  //die($this->user->user_login.' '.$this->user->ID);
                  wp_signon(array('user_login'=>$this->user->user_login,'user_password'=>trim(mysql_real_escape_string($_POST['pass1'])),'remember'=>true),$cookie);
                 */
                update_usermeta($this->user->ID, 'default_password_nag', false);
            }
        }
    }

    function updateMetas($metas) {

        foreach ((array) $metas AS $meta => $value) {
            update_usermeta($this->getData()->ID, $meta, $value);
        }
    }

    function initErrors() {
        $this->errors = new WP_Error();
    }

    /*     * ** UPDATE ACTION *** */

    function saveProfile() {


        if (!is_user_logged_in())
            return;


        $this->initErrors();

        $this->updateUserData();
        $this->uploadAvatar();
        $this->setPassword();

        $_SESSION['errors'] = $this->errors->errors;

        wp_redirect('/profile/');
        die;
    }

    function getPercentProfil() {
        global $wpdb, $clrz_filleuls;
        $percent = 0;

        if ($this->getData()->avatar)
            $percent+=20;

        $fields = array('adress1' => 3, 'zipcode1' => 3, 'city1' => 4, 'phone1' => 5, 'mobile1' => 5, 'raisonsociale' => 5, 'adress2' => 3, 'zipcode2' => 3, 'city2' => 3, 'phone2' => 3, 'mobile2' => 3);
        foreach ($fields AS $field => $val)
            if ($this->getData()->{$field})
                $percent+=$val; //40%
        //check how many field are filled in

        $activite = $wpdb->get_row('SELECT * FROM ' . $wpdb->usermeta . ' WHERE meta_key LIKE "activite_%" AND user_id = ' . $this->user->ID . ' AND meta_value <> "" LIMIT 0,1');

        if ($activite)
            $percent+=20;

        if ($clrz_filleuls->getFilleuls())
            $percent+=20;

        return $percent;
    }

    function sendMailToNewFriend($futur_friend) {
        $mail = new PHPMailer();
        $body = $mail->getFile(TEMPLATEPATH . '/mails/modele-friendask.html');
        $body = eregi_replace("[\]", '', $body);
        $body = ereg_replace("@FRIEND@", $this->get('display_name'), $body);
        $body = ereg_replace("@URL_SITE@", get_option('url'), $body);
        $mail->From = get_bloginfo('admin_email');
        $mail->FromName = get_bloginfo('name');
        $mail->Subject = utf8_decode("Demande d'ami");
        $mail->MsgHTML($body);
        $mail->AddAddress($futur_friend->get('user_email'));

        $mail->send();
    }

    function sendMailMessage($member_inbox) {
        $mail = new PHPMailer();
        $body = $mail->getFile(TEMPLATEPATH . '/mails/modele-newmessage.html');
        $body = eregi_replace("[\]", '', $body);
        $body = ereg_replace("@FRIEND@", $this->get('display_name'), $body);
        $body = ereg_replace("@URL_SITE@", get_option('url'), $body);
        $mail->From = get_bloginfo('admin_email');
        $mail->FromName = get_bloginfo('name');
        $mail->Subject = utf8_decode("Nouveau message");
        $mail->MsgHTML($body);
        $mail->AddAddress($member_inbox->get('user_email'));

        $mail->send();
    }

    function sendMailWall($wall_user) {
        $mail = new PHPMailer();
        $body = $mail->getFile(TEMPLATEPATH . '/mails/modele-newmessageonwall.html');
        $body = eregi_replace("[\]", '', $body);
        $body = ereg_replace("@FRIEND@", $this->get('display_name'), $body);
        $body = ereg_replace("@URL_SITE@", get_option('url'), $body);
        $mail->From = get_bloginfo('admin_email');
        $mail->FromName = get_bloginfo('name');
        $mail->Subject = utf8_decode("Nouveau message sur votre mur");
        $mail->MsgHTML($body);
        $mail->AddAddress($wall_user->get('user_email'));

        $mail->send();
    }

    function sendMailToAdmin($object, $message, $email) {
        global $clrz_Profil;
        $objectName = $clrz_Profil->getSelect('contact_objet', $object)->name;
        $mail = new PHPMailer();
        $body = $mail->getFile(TEMPLATEPATH . '/mails/modele-contact.html');
        $body = eregi_replace("[\]", '', $body);
        $body = ereg_replace("@MESSAGE@", $message, $body);

        $mail->From = trim($email);
        $mail->FromName = trim($email);
        $mail->Subject = utf8_decode($objectName);
        $mail->MsgHTML($body);
        $mail->AddAddress(get_bloginfo('admin_email'));

        $mail->send();
    }

    /*     * ******************************************************************************** */
    /*     * ******************************** buddy func ************************************ */
    /*     * ******************************************************************************** */

    function getLinkFriend($action='add') {
        global $clrz_core;
        $actions = array(
            'add' => 'AskFriend',
            'remove' => 'DeleteFriend',
            'confirm' => 'Confirmfriend',
            'block' => 'Blockfriend',
        );

        //return get_bloginfo('url').'/profil/friends/?action='.$actions[$action].'&friend_id='.$this->get('ID');	
        return $clrz_core->_getUrl('friends', 'action=' . $actions[$action] . '&_friend_id=' . $this->get('ID'));
    }

    function askFriend($friend_id) {
        global $wpdb;

        if (!$friend_id)
            return false;
        if ($friend_id == $this->get('ID'))
            return false;

        if ($this->friendship_exists($friend_id))
            return false;


        $result = $wpdb->query($wpdb->prepare("INSERT INTO clrz_friends ( initiator_user_id, friend_user_id, is_confirmed, is_limited, date_created ) VALUES ( %d, %d, %d, %d, FROM_UNIXTIME(%d) )", $this->get('ID'), $friend_id, 0, 0, time()));
        return true;
    }

    function deleteFriend($friend_id) {
        global $wpdb;



        if ($friend_id == $this->get('ID'))
            return false;

        if (!$this->friendship_exists($friend_id))
            return false;


        $result = $wpdb->query($wpdb->prepare("DELETE FROM clrz_friends WHERE id = %d", $this->getFriendShipID($friend_id)));
        return true;
    }

    function confirmFriend($friend_id) {
        global $wpdb;

        if (!$this->friendship_exists($friend_id))
            return false;
        if ($this->friendship_exists($friend_id, true))
            return false;
        $result = $wpdb->query($wpdb->prepare("UPDATE clrz_friends SET  is_confirmed = %d, is_limited = %d, date_created = FROM_UNIXTIME(%d)  WHERE id = %d", 1, 0, time(), $this->getFriendShipID($friend_id)));

        return true;
    }

    function blockFriend($friend_id) {
        global $wpdb;

        if (!$this->friendship_exists($friend_id))
            return false;


        $blocked = ($this->isBlocked($friend_id)) ? 0 : 1;

        $result = $wpdb->query($wpdb->prepare("UPDATE clrz_friends_block SET is_blocked = %d, date_created=FROM_UNIXTIME(%d)  WHERE friendship_id = %d AND user_id = %d", $blocked, time(), $this->getFriendShipID($friend_id), $this->get('ID')));
        if (!$wpdb->rows_affected)
            $result = $wpdb->query($wpdb->prepare("INSERT INTO clrz_friends_block ( friendship_id, user_id,  is_blocked, date_created ) VALUES ( %d, %d, %d, FROM_UNIXTIME(%d) )", $this->getFriendShipID($friend_id), $this->get('ID'), $blocked, time()));
        return true;
    }

    function isBlocked($friend_id) {
        global $wpdb;
        $is_blocked = $wpdb->get_var($wpdb->prepare("SELECT is_blocked FROM clrz_friends_block  WHERE friendship_id = %d AND user_id = %d", $this->getFriendShipID($friend_id), $this->get('ID')));

        return $is_blocked;
    }

    function getFriendShipID($friend_id) {


        return $this->friendship_exists($friend_id);
    }

    function friendship_exists($friend_id, $status='all') {
        if (!$friend_id)
            return false;


        global $wpdb;


        $confirmed = '';
        if ($status === 'all')
            $confirmed = '';
        else if ($status === true)
            $confirmed = 'AND is_confirmed = 1';
        else
            $confirmed = 'AND is_confirmed = 0';



        return $wpdb->get_var($wpdb->prepare("SELECT id FROM clrz_friends WHERE ( initiator_user_id = %d AND friend_user_id = %d ) OR ( initiator_user_id = %d AND friend_user_id = %d ) $confirmed", $this->get('ID'), $friend_id, $friend_id, $this->get('ID')));
    }

    function getFriendsId($status=true, $toString=false) {

        global $wpdb;
        $fids = array();
        $user_id = $this->get('ID');
        if ($status === 'pending') {
            $oc_sql = $wpdb->prepare("AND is_confirmed = 0");
            $friend_sql = $wpdb->prepare(" WHERE initiator_user_id = %d", $user_id);
        } elseif ($status === false) {
            $oc_sql = $wpdb->prepare("AND is_confirmed = 0");
            $friend_sql = $wpdb->prepare(" WHERE friend_user_id = %d", $user_id);
        } else {
            $oc_sql = $wpdb->prepare("AND is_confirmed = 1");
            $friend_sql = $wpdb->prepare(" WHERE (initiator_user_id = %d OR friend_user_id = %d)", $user_id, $user_id);
        }

        //echo "SELECT friend_user_id, initiator_user_id FROM clrz_friends $friend_sql $oc_sql ORDER BY date_created DESC";
        $friends = $wpdb->get_results($wpdb->prepare("SELECT friend_user_id, initiator_user_id FROM clrz_friends $friend_sql $oc_sql ORDER BY date_created DESC"));

        for ($i = 0; $i < count($friends); $i++) {
            if (isset($assoc_arr))
                $fids[] = array('user_id' => ( $friends[$i]->friend_user_id == $user_id ) ? $friends[$i]->initiator_user_id : $friends[$i]->friend_user_id);
            else
                $fids[] = ( $friends[$i]->friend_user_id == $user_id ) ? $friends[$i]->initiator_user_id : $friends[$i]->friend_user_id;
        }

        if ($toString == true)
            $fids = implode(',', $fids);

        if (sizeof($fids) == 1 && $fids[0] == 0)
            $fids = array();

        return $fids;
    }

    function getFriendWaitingCount() {

        return count($this->getFriendsId(false));
    }

    /*     * ******************************************************************************** */
    /*     * ******************************* message func *********************************** */
    /*     * ******************************************************************************** */

    /* ---- Messages  ---- */

    function getLinkMessages($action='set_unread') {
        global $clrz_core;
        $actions = array(
            'set_unread' => 'SetUnreadMessage',
            'set_read' => 'SetReadMessage',
            'delete' => 'DeleteMessage',
        );

        return $clrz_core->_getUrl('messages', 'action=' . $actions[$action]);
    }

    function getInbox($id_parent='') {
        $messages = array();
        $and = ' AND id_parent="' . $id_parent . '" ';

        global $wpdb;
        $user_id = $this->get('ID');
        $query = "SELECT DISTINCT(id_message) FROM clrz_status_message_meta WHERE cle='is_not_deleted' AND id_user ='$user_id' ";
        $ids = $wpdb->get_results($query);

        if ($ids) {
            $tab = array();
            foreach ($ids as $id) {
                array_push($tab, $id->id_message);
            }
            $ids_str = implode(',', $tab);
            $query = "SELECT *, DATEDIFF(NOW(),date) AS date_diff FROM clrz_messages WHERE 1=1 AND ( inbox_id='$user_id' ) AND id IN ($ids_str) " . $and . " ORDER BY date DESC";
        }


        //die($query);
        $messages = $wpdb->get_results($query);
        return $messages;
    }

    function getMessages($id_parent='') {
        $and = ' AND id_parent="' . $id_parent . '" ';

        global $wpdb;
        $user_id = $this->get('ID');
        $query = "SELECT DISTINCT(id_message) FROM clrz_status_message_meta WHERE cle='is_not_deleted' AND id_user ='$user_id' ";

        $ids = $wpdb->get_results($query);

        if ($ids) {
            $tab = array();
            foreach ($ids as $id) {
                array_push($tab, $id->id_message);
            }
            $ids_str = implode(',', $tab);
        }else
            $ids_str = '';
        
        $noresult = ($ids_str=='') ? true : false;
        $messages = array();
        if(!$noresult){
            $query = "SELECT *, DATEDIFF(NOW(),date) AS date_diff FROM clrz_messages WHERE 1=1 AND ( inbox_id='$user_id' OR submit_id='$user_id' ) AND id IN ($ids_str) " . $and . " ORDER BY date ";
            //die($query);
            $messages = $wpdb->get_results($query);
        }
        return $messages;
    }

    function getNbMessages($id_parent='') {
        $and = ' AND id_parent="' . $id_parent . '" ';

        global $wpdb;
        $user_id = $this->get('ID');
        $query = "SELECT DISTINCT(id_message) FROM clrz_status_message_meta WHERE id_user='$user_id' AND cle='is_unread'  ";

        $ids = $wpdb->get_results($query);

        if ($ids) {
            $tab = array();
            foreach ($ids as $id) {
                array_push($tab, $id->id_message);
            }
            $ids_str = implode(',', $tab);
        }else
            return 0;
        $query = "SELECT COUNT(id) AS nb FROM clrz_messages WHERE 1=1 AND id IN ($ids_str)  ";

        $messages = $wpdb->get_row($query);
        return $messages->nb;
    }

    function setUnreadMessage($message_ids) {
        global $wpdb;
        $messages_str = implode(',', $message_ids);
        $query = 'UPDATE clrz_messages SET state="0" WHERE id IN (' . $messages_str . ') AND inbox_id="' . $this->get('ID') . '" ';
        if ($wpdb->query($query) === false)
            return false;
        else
            return true;
    }

    function setReadMessage($message_ids) {
        global $wpdb;
        $messages_str = implode(',', $message_ids);
        $query = 'UPDATE clrz_messages SET state="1" WHERE id IN (' . $messages_str . ') AND inbox_id="' . $this->get('ID') . '"	 ';
        if ($wpdb->query($query) === false)
            return false;
        else
            return true;
    }

    function deleteMessage($message_ids) {
        global $wpdb;
        $messages_str = implode(',', $message_ids);
        $res = $wpdb->get_results('SELECT * FROM clrz_messages WHERE id IN (' . $messages_str . ') AND inbox_id="' . $this->get('ID') . '"  ');
        if ($wpdb->query('DELETE FROM clrz_messages WHERE id IN (' . $messages_str . ') AND inbox_id="' . $this->get('ID') . '"  ') === false)
            return false;

        $tab = array();
        foreach ((array) $res as $r) {
            array_push($tab, $r->id);
        }
        $tab_str = implode(',', $tab);
        $query = 'DELETE FROM clrz_messages WHERE id_parent IN (' . $tab_str . ') AND inbox_id="' . $this->get('ID') . '" ';
        if ($wpdb->query($query) === false)
            return false;
        else
            return true;
    }

    /* ---- ViewMessages  ---- */

    function getLinkViewMessage($action='set_unread', $message_id=0) {
        global $clrz_core;
        $actions = array(
            'set_unread' => 'SetUnreadViewMessage',
            'delete' => 'DeleteViewMessage',
            'add' => 'AddMessage',
        );
        if (!$actions[$action])
            return false;
        return $clrz_core->_getUrl('viewmessage', 'action=' . $actions[$action] . '&message_id=' . $message_id);
    }

    function getMessage($message_id) {
        global $wpdb;
        $user_id = $this->get('ID');
        $query = 'SELECT *, DATEDIFF(NOW(),date) AS date_diff FROM clrz_messages WHERE id="' . $message_id . '"  AND ( inbox_id="' . $user_id . '" OR submit_id="' . $user_id . '" ) ';
        //die($query);
        $message = $wpdb->get_row($query);
        return $message;
    }

    function is_message_exists($message_id, $parent=0) {
        global $wpdb;
        if ($parent == 0) {
            $and = ' AND id_parent="0" ';
        }
        else
            $and= '';

        $user_id = $this->get('ID');
        $query = "SELECT DISTINCT(id_message) FROM clrz_status_message_meta WHERE cle='is_not_deleted'  ";

        $ids = $wpdb->get_results($query);

        if ($ids) {
            $tab = array();
            foreach ($ids as $id) {
                array_push($tab, $id->id_message);
            }
            $ids_str = implode(',', $tab);
        }else
            $ids_str = '';


        $query = "SELECT * FROM clrz_messages WHERE 1=1 AND ( inbox_id='$user_id' OR submit_id='$user_id' ) AND id IN ($ids_str) " . $and . " ORDER BY date ";

        if (!$wpdb->get_results($query))
            return false;
        else
            return true;
    }

    function deleteViewMessage($message_id) {
        global $wpdb;
        $res = $wpdb->get_row('SELECT * FROM clrz_messages WHERE id ="' . $message_id . '" AND inbox_id="' . $this->get('ID') . '" ');
        $result = $wpdb->query($wpdb->prepare('DELETE FROM clrz_messages WHERE id ="' . $message_id . '" AND inbox_id="' . $this->get('ID') . '"  '));

        $query = 'DELETE FROM clrz_messages WHERE id_parent = "' . $res->id . '"  AND inbox_id="' . $this->get('ID') . '" ';

        if ($wpdb->query($query) === false)
            return false;
        else
            return true;
    }

    function isMessageDeleted($user_id, $parent_id) {
        global $wpdb;

        $query = "SELECT id FROM clrz_status_message_meta WHERE id_message='$parent_id' AND id_user <> '$user_id' AND cle = 'is_not_deleted'";
        //echo $query;
        $res = $wpdb->get_row($query);

        if (!$res->id)
            return true;
        else
            return false;
    }

    function addMessage($message_id, $new_message, $title, $inbox_id) {
        global $wpdb;
        $user_id = $this->get('ID');
        if ($inbox_id == $user_id) {
            $query = "SELECT inbox_id FROM clrz_messages WHERE id=$message_id";
            $inbox_id = $wpdb->get_row($query)->inbox_id;
        }



        $query = 'INSERT INTO clrz_messages VALUES(NULL,"' . $user_id . '","' . $inbox_id . '",NOW(),"' . $title . '","' . $new_message . '","0","' . $message_id . '") ';
        if ($wpdb->query($query) === false)
            return false;
        else {
            $m_id = $wpdb->insert_id;

            $datas = array('id_user' => $inbox_id,
                'id_message' => $m_id,
                'cle' => 'is_not_deleted'
            );
            $wpdb->insert('clrz_status_message_meta', $datas);

            $datas = array('id_user' => $user_id,
                'id_message' => $m_id,
                'cle' => 'is_not_deleted'
            );
            $wpdb->insert('clrz_status_message_meta', $datas);

            $datas = array('id_user' => $inbox_id,
                'id_message' => $message_id,
                'cle' => 'is_unread'
            );
            $wpdb->insert('clrz_status_message_meta', $datas);

            $member_inbox = new Clrz_user($inbox_id);

            if (get_user_meta($member_inbox->get('ID'), 'mail_message') == 1)
                $this->sendMailMessage($member_inbox);


            return true;
        }
    }

    function setUnreadViewMessage($message_id) {
        global $wpdb;
        $query = 'UPDATE clrz_messages SET state="0" WHERE id="' . $message_id . '" AND inbox_id="' . $this->get('ID') . '" ';
        $wpdb->query($query);

        $query = 'SELECT id FROM clrz_messages WHERE id_parent="' . $message_id . '" ';
        $messages = $wpdb->get_results($query);
        $tab = array();
        foreach ($messages as $m) {
            array_push($tab, $m->id);
        }
        $tab_str = implode(',', $tab);

        $query = ' UPDATE clrz_messages SET state="0" WHERE id IN(' . $tab_str . ') ';
        $wpdb->query($query);

        return true;
    }

    /*     * ******************************************************************************** */
    /*     * ********************************* fav func ************************************* */
    /*     * ******************************************************************************** */

    function getLinkFavorite($action='add', $post_id=0) {
        global $clrz_core;
        $actions = array(
            'delete' => 'DeleteFavorite',
            'add' => 'AddFavorite',
        );

        if (!$actions[$action])
            return false;
        return $clrz_core->_getUrl('favoris', 'action=' . $actions[$action] . '&id=' . $post_id);
    }

    function addFavorite($post_id) {
        global $wpdb;
        $array_ids_favorites = array();
        $user_id = $this->get('ID');

        $ids_favorites = (array) get_user_meta($user_id, 'ids_favorites');
        $ids_favorites['blog-' . SITEID] = (!is_array($ids_favorites['blog-' . SITEID])) ? array() : $ids_favorites['blog-' . SITEID];
        array_push($ids_favorites['blog-' . SITEID], $post_id);
        $ids_favorites['blog-' . SITEID] = array_unique($ids_favorites['blog-' . SITEID]);

        if (update_usermeta($user_id, 'ids_favorites', $ids_favorites))
            return true;
        else
            return false;
    }

    function deleteFavorite($post_id) {
        global $wpdb;
        $user_id = $this->get('ID');
        $ids_favorites = get_user_meta($user_id, 'ids_favorites');
        $ids_favorites['blog-' . SITEID] = (!is_array($ids_favorites['blog-' . SITEID])) ? array() : $ids_favorites['blog-' . SITEID];
        //$unser  = unserialize($ids_favorites);


        unset($ids_favorites['blog-' . SITEID][array_search($post_id, $ids_favorites['blog-' . SITEID])]);
        $ids_favorites = serialize($ids_favorites);



        if (!update_usermeta($user_id, 'ids_favorites', $ids_favorites))
            return false;
        else
            return true;
    }

}

global $clrz_user;
$clrz_user = new Clrz_user();
