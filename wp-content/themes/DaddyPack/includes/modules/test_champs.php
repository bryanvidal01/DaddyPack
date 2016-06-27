<?php
$message_retour = '';
if(!empty($_POST)){
    $a_poster = array();
    $erreurs = array();

    foreach($fields as $post_id => &$field){
        // Si le champ existe et est valide
        if(isset($_POST[$post_id])){
            $field['valeur'] = stripslashes(strip_tags(trim($_POST[$post_id])));
            if($field['test'] == 'nonvide' && $field['valeur'] == '')
                $erreurs[] = 'Le champ '.$field['nom'].' est vide';
            else {
                if($field['test'] == 'email' && false === filter_var($field['valeur'], FILTER_VALIDATE_EMAIL)){
                    $erreurs[] = 'Le champ '.$field['nom'].' n\'est pas un email';
                    $field['valeur'] = '';
                }
                else $a_poster[$post_id] = $field['valeur'];
            }

        }
        else $erreurs[] = 'Le champ '.$field['nom'].' est manquant';
    }

    if(empty($erreurs)){
        $message_retour .= $message_ok;
        success_form($fields,$a_poster);
    }
    else {
        $message_retour .= '<p><strong>Attention</strong> :<br />'.implode('<br />',$erreurs).'</p>';
    }
}
