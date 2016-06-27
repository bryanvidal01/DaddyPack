<?php

/**
 * Crée un lien HTML à partir d'un identifiant de page
 * @param string    $id           Identifiant de la page visée.
 * @param boolean   $retour       Optionnel. Retourne si TRUE Affiche si FALSE
 * @param boolean   $pageactive   Optionnel. Ajoute une class="active" au lien si TRUE.
 * @return string
 */
function clrz_get_html_link( $id, $retour = true, $pageactive = false ) {
    $chaine = '<a '.( $pageactive ? 'class="current"':'' ).' href="'.get_page_link( $id ).'"><span>'.get_the_title( $id ).'</span></a>';
    if ( $retour ) {
        return $chaine;
    }
    echo $chaine;
}


/**
 * Teste si la page actuelle est un enfant de la page spécifiée
 * @param mixed    $parent        Identifiant de la page parente à tester
 * @return boolean
 */
function is_child($parent){
    global $wp_query;
    $retour = false;
    if(is_array($parent)){foreach($parent as $id){if($wp_query->post->post_parent == $id) $retour = true;}}
    else $retour = ($wp_query->post->post_parent == $parent);
    return $retour;
}

/**
 * Tronque le contenu à un certain nombre de caracteres
 * @param string    $string        Chaine à limiter
 * @param int       $width         Nombre de caractères minimum
 * @param string    $pad           Optionnel. Chaine à rajouter à la fin, type "...".
 * @return string
 */
function truncated($string, $width, $pad="...") {
    if(strlen($string) > $width) {
        $string = str_replace("\n",' ',$string);
        $string = wordwrap($string, $width);
        $string = substr($string, 0, strpos($string, "\n")). $pad;
    }
    return $string;
}

$clrz_template_name = null;
/**
 * Retourne le fichier template actuel
 * @return string
 */
function clrz_get_file_template_name() {
    global $clrz_template_name;
    //echo $clrz_template_name;
    if(is_null($clrz_template_name)){
        $clrz_backtrace = debug_backtrace ();
        //print_r($clrz_backtrace);
        foreach ( $clrz_backtrace as $called_file ) {
            foreach ( $called_file as $index ) {
                if (is_array($index) && isset($index[0]) && !is_array($index[0]) && (strstr($index[0],'/themes/') || strstr($index[0],'\themes\\') ) && !strstr($index[0],'header.php') && !strstr($index[0],'footer.php') && !strstr($index[0],'sidebar.php')  ) {
                    $template_file = $index[0]; break 2;
                }
            }
        }
        $url_parameters = explode('/themes/', basename($template_file));
        $clrz_template_name = array_pop($url_parameters);
    }
    return $clrz_template_name;
}
/**
 * Teste le fichier template actuel
 * @param string    $template_name        Nom du fichier template
 * @return boolean
 */
function clrz_is_template($template_name){
    $retour = false;
    if(is_array($template_name)){
        foreach($template_name as $a_tester){
            if(clrz_get_file_template_name() == $a_tester)
                $retour = true;
        }
    }
    else {
        if(clrz_get_file_template_name() == $template_name)
            $retour = true;
    }
    return $retour;
}

/**
 * Retourne un loop avec les arguments fournis
 * @param array     $args           Arguments WP_Query
 * @param string    $template       Template choisi
 * @param string    $flag           (Optionnel) Flags du cache
 * @param int       $duree_cache    (optionnel) Durée du cache
 * @return string
 */
function show_the_loop($args, $template='loop-short', $flag = 'loops', $duree_cache=0) {
    global $paged, $wp_query, $post;

    // Arguments par défaut
    if (empty($args) || !is_array($args))
        $args = array();
    if (empty($template) || !is_string($template))
        $template = 'loop-short';
    if (!isset($args['posts_per_page']))
        $args['posts_per_page'] = get_option('posts_per_page');
    if (!isset($args['post_type']))
        $args['post_type'] = 'post';
    // L'identifiant du cache est généré depuis arguments fournis
    $cache_id = md5(serialize($args) . $template);

    // On teste le cache
    $retour = wp_cache_get($cache_id);

    // Si le cache est invalide, on le régénère
    if (false == $retour) {
        $stl_the_query = new WP_Query($args);
        if ($stl_the_query->have_posts()) :
            // On initialise le tampon de sortie
            ob_start();
            echo '<div class="show_the_loop list-' . $template . '">';
            while ($stl_the_query->have_posts()) : $stl_the_query->the_post();
                get_template_part($template);
            endwhile;
            echo '</div>';
            // On n'inclut pas la pagination si on n'a pas d'argument paged
            if (isset($args['paged']))
                include TEMPLATEPATH . '/tpl/tpl_pagination.php';
            wp_reset_postdata();
            // On vide le tampon de sortie
            $retour .= ob_get_contents();
            // On retourne le contenu
            ob_end_clean();
        endif;
        wp_cache_set($cache_id, $retour, $flag, $duree_cache);
    }
    return $retour;
}

// Returns true if $string is valid UTF-8 and false otherwise.
// http://www.php.net/manual/fr/function.mb-detect-encoding.php#50087
function clrz_is_utf8($string) {

    // From http://w3.org/International/questions/qa-forms-utf-8.html
    return preg_match('%^(?:
          [\x09\x0A\x0D\x20-\x7E]            # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);

}

function clrz_send_mail($to = '', $subject='', $message='', $model='', $vars = array()) {
    require_once(ABSPATH . WPINC . '/class-phpmailer.php');
    $mail_content = $message;
    if(!empty($model) && preg_match('#^([a-z0-9_-]+)$#', $model) && file_exists(TEMPLATEPATH.'/mails/model-'.$model.'.php')) {
        ob_start();
        @include TEMPLATEPATH.'/mails/model-'.$model.'.php';
        $mail_content = ob_get_clean();
    }
    ob_start();
    @include TEMPLATEPATH.'/mails/header.php';
    echo $mail_content;
    @include TEMPLATEPATH.'/mails/footer.php';
    $body = ob_get_clean();

    $mail = new PHPMailer();
    $mail->CharSet = 'utf-8'; // Précision sur l'encodage pour phpmailer
    $mail->From = 'nepasrepondre@' . $_SERVER['HTTP_HOST'];
    $mail->FromName = get_bloginfo('name');
    $mail->Subject = $subject;

    if (false === filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $to = get_option('admin_email');
    }

    $mail->MsgHTML($body);
    $mail->AddAddress($to);

    do_action_ref_array( 'phpmailer_init', array( &$mail ) );

    $mail->send();
}

/*
  echo returnTable(array('Un', 'Deux'), array(
  array('abab', 'baba'),
  array('aba"b', 'baeba'),
  array('abatb', 'breaba')
  ));
 */

function returnHTMLTable($labels, $values) {
    $retour = '<table>';
    $th = '';
    foreach ($labels as $label)
        $th .= '<th>' . $label . '</th>';
    $retour .= '<thead>' . $th . '</thead>';
    $retour .= '<tfoot>' . $th . '</tfoot>';
    $retour .= '<tbody>';
    foreach ($values as $value) {
        $retour .= '<tr>';
        foreach ($value as $td)
            $retour .= '<td>' . $td . '</td>';
        $retour .= '</tr>';
    }
    $retour .= '</tbody>';
    $retour .= '</table>';

    return $retour;
}

function clrz_get_template_part($group_template, $file, $args = array()){
	global $wpdb,$post;
	$retour = false;
	$filename = TEMPLATEPATH.'/tpl/'.$group_template.'/'.$file.'.php';
	$cache_file = md5($filename);
	$cache_dir = ABSPATH.'/wp-content/clrz_cache/';

	$cache_valide = true;

	// Doit-on utiliser le cache ?
	if(!isset($args['expires']) || $args['expires'] == 0){
		$cache_valide = false;
	}

	// Le cache est-il valide ?
	if($cache_valide && (!file_exists($cache_dir.$cache_file))){
		$cache_valide = false;
	}

	// Le fichier de cache est-il expiré ?
	if($cache_valide && filemtime($cache_dir.$cache_file)+$args['expires'] < time()){
		$cache_valide = false;
	}

	// On recupere le fichier demandé
	ob_start();
	if($cache_valide){
		include $cache_dir.$cache_file;
	}
	else{
		if(file_exists($filename)){
			include $filename;
		}
	}
	$retour .= ob_get_contents();
	ob_end_clean();

	if(!$cache_valide && isset($args['expires']) && $args['expires'] > 0){
		$file_create = file_put_contents($cache_dir.$cache_file,$retour);
		// Si la création de cache a échoué
		if($file_create === FALSE){
		    if(!is_dir($cache_dir)){
		        mkdir($cache_dir);
		        @chmod($cache_dir,0777);
		    }
		}
	}

	return $retour;
}

// Compteur Twitter
// http://www.catswhocode.com/blog/wordpress-transients-api-practical-examples
function twitter_followers_count($screen_name = 'Colorz', $force= false, $expiration = 86400){
	$key = 'twitter_followers_count_' . $screen_name;

	// Let's see if we have a cached version
	$followers_count = get_transient($key);
	if ($followers_count !== false && !$force )
		return $followers_count;
	else
	{
		// If there's no cached version we ask Twitter
		$response = wp_remote_get("http://api.twitter.com/1/users/show.json?screen_name=".$screen_name);
		if (is_wp_error($response)) {
			// In case Twitter is down we return the last successful count
			return get_option($key);
		}
		else {
			// If everything's okay, parse the body and json_decode it
			$json = json_decode(wp_remote_retrieve_body($response));

			// If the result is a number.
			if(isset($json->followers_count) && ctype_digit($json->followers_count)){
				$count = $json->followers_count;

				// Store the result in a transient, expires after 1 day
				// Also store it as the last successful using update_option
				set_transient($key, $count, $expiration);
				update_option($key, $count);
			}
			else {
				$count = get_option($key);
				// We set the transient for 30 minutes with the old value
				// ( waiting for twitter server to be in better shape )
				set_transient($key, $count, 30*60);
			}

			return (string) $count;
		}
	}
}

function get_relative_time($timestamp){
	$time = '';
	$time_diff = current_time('timestamp') - $timestamp;
	$unit = '';
	$value = '';

	if($time_diff > 60 * 60 * 24 * 365){
		$unit = __('an','clrz_lang');
		$value = round($time_diff/(60*60*24*365));
	} elseif($time_diff > 60 * 60 * 24 * 30){
		$unit = __('mois','clrz_lang');
		$value = round($time_diff/(60*60*24*30));
	} else if($time_diff > 60 * 60 * 24 * 7){
		$unit = __('semaine','clrz_lang');
		$value = round($time_diff/(60*60*24*7));
	} else if($time_diff > 60 * 60 * 24){
		$unit = __('jour','clrz_lang');
		$value = round($time_diff/(60*60*24));
	} else if($time_diff > 60 * 60){
		$unit = __('heure','clrz_lang');
		$value = round($time_diff/(60*60));
	} else if($time_diff > 60){
		$unit = __('minute','clrz_lang');
		$value = round($time_diff/60);
	} else {
		$unit = __('seconde','clrz_lang');
		$value = $time_diff;
	}

	$final_unit = (($unit!='mois'&&$value>1)?'s':'');

	return sprintf(__("Il y a %d %s%s",'clrz_lang'), $value, $unit, $final_unit);
}

/**
 * Retourne un code d'embed pour une vidéo donnée. Indépendant de WordPress.
 * @param string    $video_url        URL de la vidéo à inclure.
 * @param int       $width            Optionnel. Largeur de la vidéo.
 * @param int       $height           Optionnel. Hauteur de la vidéo.
 * @return mixed                      Bool False si vidéo invalide. String si vidéo valide.
 */
function clrz_get_embed_code( $video_url , $width = 1060, $height = 596 ) {
    $embed_code = false;
    $parsed_url = parse_url( $video_url );

    if ( !isset( $parsed_url['host'] ) ) {
        return false;
    }
    // Youtube
    if ( in_array( $parsed_url['host'], array( 'youtube.com', 'www.youtube.com' ) ) ) {
        parse_str( $parsed_url['query'], $params );
        // If parameter v
        if ( isset( $params['v'] ) ) {
            $embed_code = '<iframe width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $params['v'] . '?rel=0" frameborder="0" allowfullscreen></iframe>';
        }
    }

    // Vimeo
    if ( in_array( $parsed_url['host'], array( 'vimeo.com', 'www.vimeo.com' ) ) ) {
        $path_parts = explode( '/', $parsed_url['path'] );
        $params_v = $path_parts[count( $path_parts )-1];
        if ( !empty( $params_v ) ) {
            $embed_code = '<iframe src="http://player.vimeo.com/video/' . $params_v . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }
    }

    // Dailymotion
    if ( in_array( $parsed_url['host'], array( 'dailymotion.com', 'www.dailymotion.com' ) ) ) {
        $path_parts = explode( '/', $parsed_url['path'] );
        $params_b = explode( '_', $path_parts[count( $path_parts )-1] );
        $params_v = $params_b[0];
        if ( !empty( $params_v ) ) {
            $embed_code = '<iframe frameborder="0" width="' . $width . '" height="' . $height . '" src="http://www.dailymotion.com/embed/video/' . $params_v . '"></iframe>';
        }
    }

    return $embed_code;
}









