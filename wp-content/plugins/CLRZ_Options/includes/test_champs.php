<?php


add_action('admin_init', 'update_options_client');
function update_options_client(){
    global $clrz_champs_options_clients,$clrz_groupes_options,$clrz_options_langs;
    // Création éventuelle du dossier upload
    @chmod(CLRZ_OPTIONS_UPLOADBLOGSDIR,0755);
    if(!is_dir(CLRZ_OPTIONS_UPLOADBLOGSDIR.'/clrzoptions/')) mkdir(CLRZ_OPTIONS_UPLOADBLOGSDIR.'/clrzoptions/', 0755);

    $clrz_groupes_options = apply_filters('clrz_groupes_options',$clrz_groupes_options);
    $clrz_champs_options_clients = apply_filters('clrz_champs_options_clients',$clrz_champs_options_clients);

    $clrz_test_champs_options_clients = $clrz_champs_options_clients;
    foreach($clrz_champs_options_clients as $id => $value){
        $clrz_test_champs_options_clients[$id] = $value;
        if(isset($value['lang'])){
            foreach($clrz_options_langs as $lang_id => $lang_nom){
                $valuetmp = $value;
                $valuetmp['label'] = '['.$lang_id.'] '.$valuetmp['label'];
                $clrz_test_champs_options_clients[$lang_id.'_'.$id] = $valuetmp;
            }
        }
    }


    if(isset($_POST['clrz_options_submit_plugin_admin']) && current_user_can(CLRZ_OPTIONS_CURRENT_USER_CAN_SEE)){
        $errortab = array();
        $champs_modifies = array();
        // Pour chaque champ, on effectue des tests afin de déterminer si on doit le mettre à jour
        foreach($clrz_test_champs_options_clients as $id_champ => $champ){
            $champ_ok = true;

            if(!isset($champ['typetest'])) $champ['typetest'] = '';

            // Test du contenu du fichier
            if(!in_array($champ['typetest'],array('file','image')) && (!isset($_POST[$id_champ])))
                $champ_ok = false;

            // Test du niveau de l'utilisateur
            if((!isset($champ['niveau']) && !current_user_can(CLRZ_OPTIONS_CURRENT_USER_CAN)) || (isset($champ['niveau']) && !current_user_can($champ['niveau'])))
                $champ_ok = false;

            if($champ_ok) {
                if(in_array($champ['typetest'],array('file','image'))){
                    // Si on doit supprimer le fichier
                    if(isset($_POST['delete'.$id_champ]) && $_POST['delete'.$id_champ]==1){
                        unlink(str_replace(CLRZ_OPTIONS_UPLOADBLOGSURL,CLRZ_OPTIONS_UPLOADBLOGSDIR,get_option($id_champ)));
                        $champs_modifies[] = 'Le fichier &quot;'.$champ['label'].'&quot; a été supprimé avec succès.';
                        update_option($id_champ, '');
                    }

                    if(isset($_FILES[$id_champ]) && $_FILES[$id_champ]['name'] != ''){
                        $extension_fichier = substr($_FILES[$id_champ]['name'], strrpos($_FILES[$id_champ]['name'], '.') + 1);
                        $urlfile = CLRZ_OPTIONS_UPLOADBLOGSURL.'/clrzoptions/'.$id_champ.'.'.$extension_fichier;
                        $dirfile = CLRZ_OPTIONS_UPLOADBLOGSDIR.'/clrzoptions/'.$id_champ.'.'.$extension_fichier;

                        if($champ['typetest'] == 'image'){
                            $sizeimg = getimagesize($_FILES[$id_champ]['tmp_name']);
                            if(isset($champ['sizex']) && $sizeimg[0]> $champ['sizex']){
                                $errortab[$id_champ] = 'L\'image "'.$champ['label'].'" est trop large ('.$sizeimg[0] . 'px au lieu de '.$champ['sizex'].'px )';
                                $champ_ok = false;
                            }
                            if(isset($champ['sizey']) && $sizeimg[1] > $champ['sizey']){
                                $errortab[$id_champ] = 'L\'image "'.$champ['label'].'" est trop haute ('.$sizeimg[1] . 'px au lieu de '.$champ['sizey'].'px )';
                                $champ_ok = false;
                            }

                            if(!is_array($sizeimg)){
                                $errortab[$id_champ] = 'Le fichier n\'est pas une image';
                                $champ_ok = false;
                            }
                        }

                        if($champ_ok){
                            // Suppression de l'ancien fichier
                            if(is_file($dirfile)) unlink($dirfile);
                            copy($_FILES[$id_champ]['tmp_name'], $dirfile);
                            $champs_modifies[] = 'Le fichier &quot;'.$champ['label'].'&quot; a été modifié avec succès.';
                            update_option($id_champ, $urlfile);
                        }
                    }
                }
                if($champ['typetest'] == 'select' && !array_key_exists($_POST[$id_champ],$champ['datas']))
                    $champ_ok = false;
                if($champ['typetest'] == 'tel' && !preg_match("#^0[1-9]([-. ]?[0-9]{2}){4}$#", $_POST[$id_champ]))
                    $champ_ok = false;
                if($champ['typetest'] == 'email' && (filter_var($_POST[$id_champ], FILTER_VALIDATE_EMAIL) === FALSE))
                    $champ_ok = false;
                if($champ['typetest'] == 'url' && (filter_var($_POST[$id_champ], FILTER_VALIDATE_URL) === FALSE))
                    $champ_ok = false;
                if($champ['typetest'] == 'number' && !ctype_digit($_POST[$id_champ]))
                    $champ_ok = false;
            }

            if($champ_ok && !in_array($champ['typetest'],array('file','image')) && stripslashes($_POST[$id_champ]) != get_option($id_champ)){
                $champs_modifies[] = 'Le champ &quot;'.$champ['label'].'&quot; a été modifié avec succès.';
                $valeur_champ = stripslashes($_POST[$id_champ]);
                if($champ['typetest'] == 'simpletext')
                    $valeur_champ = strip_tags($valeur_champ);
                update_option($id_champ, $valeur_champ);
            }
        }
        if(!empty($errortab)):
            $_SESSION['errorclrzoption'] = $errortab;
            add_action('my_clrz_options_client_top_error', 'error_clrz_options_client');
        endif;

        if(!empty($champs_modifies)){
            $_SESSION['successclrzoption'] = $champs_modifies;
            add_action('my_clrz_options_client_top', 'success_clrz_options_client');
        }
    }
}