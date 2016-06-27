<?php

function my_plugin_clrz_options_client() {
global $clrz_champs_options_clients, $clrz_groupes_options, $clrz_options_langs;

$clrz_groupes_options = apply_filters('clrz_groupes_options',$clrz_groupes_options);
$clrz_champs_options_clients = apply_filters('clrz_champs_options_clients',$clrz_champs_options_clients);

$clrz_groupes_vides = array();
foreach($clrz_groupes_options as $id_groupe => $nom_groupe){
    $is_groupe_vide = true;
    foreach($clrz_champs_options_clients as $id_champ => $champ){
      if($id_groupe == $champ['group']){
          $is_groupe_vide = false;
      }
    }
    if($is_groupe_vide)
      $clrz_groupes_vides[] = $id_groupe;
}

  if (!current_user_can(CLRZ_OPTIONS_CURRENT_USER_CAN_SEE)){wp_die( __('You do not have sufficient permissions to access this page.') );} ?>
    <div class="wrap" id="clrz_options_page">
        <div id="icon-options-general" class="icon32"></div>
        <h2>Options du site</h2>
        <?php my_clrz_options_client_top(); ?>
        <?php my_clrz_options_client_top_error(); ?>
        <form id="clrz_options_client" action="" method="POST" ENCTYPE="multipart/form-data">
            <?php

            foreach($clrz_groupes_options as $id_group => $legend_group) {
                if(!in_array($id_group,$clrz_groupes_vides)){
                    if($id_group != '')
                        echo '<fieldset class="clrz-options-fieldset '.$id_group.'"><legend id="group-'.$id_group.'" class="legend">'.$legend_group.'</legend><div class="content-fieldset">';
                    foreach($clrz_champs_options_clients as $id_champ => $champ){
                        if(!isset($champ['typehtml'])) $champ['typehtml'] = '';
                        if(($id_group == '' && (!isset($champ['group']) || $champ['group'] == '')) || ($id_group != '' && $id_group == $champ['group'])){
                            if(!isset($champ['lang']))
                                show_champ($champ,$id_champ);
                            else {
                                echo '<ul>';
                                foreach($clrz_options_langs as $lang_id => $lang_nom){
                                    echo '<li>';
                                    $champ_temp = $champ;
                                    $champ_temp['label'] = '['.$lang_id.'] '.$champ_temp['label'];
                                    show_champ($champ_temp,$lang_id.'_'.$id_champ);
                                    echo '</li>';
                                }
                                echo '</ul>';
                            }
                        }
                    }
                    if($id_group != '')
                        echo '</div></fieldset>';
                }
            }
            ?>
            <div class="submit-clrz-options"><input name="clrz_options_submit_plugin_admin" class="button-primary" type="submit" value="Valider les modifications"/></div>
        </form>
        <hr style="clear:both;opacity:0.01"/>
    </div>
    <?php
    // Export de fields
    if(current_user_can('export')){
        $fields_export = array();
        foreach($clrz_champs_options_clients as $id_field_exp => $val){
            $fields_export[$id_field_exp] = serialize(get_option($id_field_exp));
        }
        echo '<h3>Exporter les données</h3><div style="margin-bottom:20px;"><a download="export-options-'.date('Y-m-d').'-'.preg_replace('/[^a-zA-Z0-9\s]/','',site_url()).'.txt" href="data:txt;base64,' . base64_encode(json_encode($fields_export)).'">Télécharger l’export</a></div>';
    }
    // Import de fields
    if(current_user_can('import')){
        if(!empty($_POST['import_clrz_options']) && isset($_FILES['import_clrz_option_file']) && $_FILES['import_clrz_option_file']['error'] == 0){
            $objet_import = json_decode(file_get_contents($_FILES['import_clrz_option_file']['tmp_name']));
            if(is_object($objet_import)){
                foreach($objet_import as $id_opt => $value){
                    $value_decode = unserialize($value);
                    update_option($id_opt, $value_decode);
                }
                echo '<meta http-equiv="refresh" content="1" />';
            }
        }

        ?><h3>Importer les données</h3>
        <form action="" method="POST" ENCTYPE="multipart/form-data">
            <input type="file" name="import_clrz_option_file" id="import_clrz_option_file" value="" />
            <button class="button-primary" name="import_clrz_options" value="1" type="submit">Importer</button>
        </form><?php
    }
}

function show_champ($champ,$id_champ){
    $typehtml = empty($champ['typehtml']) ? 'text':$champ['typehtml'];
    $value = get_option($id_champ);
    $idname = ' id="' . $id_champ . '" name="' . $id_champ . '" ';

    echo '<div class="blocoption type-'.$typehtml.'">';
    // On interdit l'édition du champ par défaut, ou si l'utilisateur n'a pas le niveau pour éditer le champ
    if((!isset($champ['niveau']) && !current_user_can(CLRZ_OPTIONS_CURRENT_USER_CAN)) || (isset($champ['niveau']) && !current_user_can($champ['niveau'])))
        echo '<div class="filterdisable"></div>';

    echo '<label for="'.$id_champ.'">'.$champ['label'].' : </label>';
    switch($typehtml){
        case 'select' :
            echo '<select name="'.$id_champ.'">';
            foreach($champ['datas'] AS $k=>$v)
                echo '<option '.(($k==get_option($id_champ)) ? 'selected="selected"' : '').' value="'.$k.'">'.$v.'</option>';
            echo '</select>';
        break;
        case 'editor' :
            wp_editor(get_option($id_champ),$id_champ, array('textarea_rows' => 8));
        break;
        case 'textarea' :
            echo '<textarea id="'.$id_champ.'" name="'.$id_champ.'">'.get_option($id_champ).'</textarea>';
        break;
        case 'page':
            wp_dropdown_pages(array(
                'name' => $id_champ,
                'selected' => get_option($id_champ)
            ));
        break;
        case 'media':
            $img = '';
            $btn_label = __( 'Add a picture', 'wpuoptions' );
            if ( is_numeric( $value ) ) {
                $image = wp_get_attachment_image_src( $value, 'big' );
                if ( isset( $image[0] ) ) {
                    $img = '<img class="wpu-options-upload-preview" src="'.$image[0]. '" alt="" />';
                    $btn_label = __( 'Change this picture', 'wpuoptions' );
                }
            }

            echo '<div id="preview-'.$id_champ.'">'.$img.'</div>'.
                '<a href="#" data-for="'.$id_champ.'" class="button button-small wpuoptions_add_media">'.$btn_label.'</a>'.
                '<input type="hidden" ' . $idname . ' value="' . $value . '" />';
            break;
        case 'image' :
            echo '<input type="file" name="'.$id_champ.'" id="'.$id_champ.'"/>'
            .((isset($champ['sizex']) || isset($champ['sizey'])) ?
                '<div class="clrz_clearfix">Contrainte : '
                .(isset($champ['sizex']) ? 'largeur max : '.$champ['sizex'].'px. ':'')
                .(isset($champ['sizey']) ? 'hauteur max : '.$champ['sizey'].'px. ':'')
                .'</div>'
            :'')
            .(get_option($id_champ)!='' ?
                '<div class="clrz_clearfix">'.
                '<input type="checkbox" name="delete'.$id_champ.'" value="1" id="delete'.$id_champ.'"/>'.
                '<label for="delete'.$id_champ.'">Supprimer l\'image actuelle</label>'.
                '<a href="'.get_option($id_champ).'" target="_blank"><img src="'.get_option($id_champ).'" alt="" style="width:20px;height:20px;"/></a>'.
                '</div>' :'');
        break;
        case 'file' :
            echo '<input type="file" name="'.$id_champ.'" id="'.$id_champ.'"/>'.
            (get_option($id_champ)!='' ?
                '<div class="clrz_clearfix">'.
                '<input type="checkbox" name="delete'.$id_champ.'" value="1" id="delete'.$id_champ.'"/>'.
                '<label for="delete'.$id_champ.'">Supprimer le fichier actuel</label>'.
                '<a href="'.get_option($id_champ).'" target="_blank">'.str_replace(CLRZ_OPTIONS_UPLOADBLOGSURL,'',get_option($id_champ)).'</a>'.
                '</div>' :'');
        break;

        default :
            if ( post_type_exists( $typehtml ) ) {
                $post_type_object = get_post_type_object($typehtml);
                $label = $post_type_object->label;
                $posts = get_posts(array('post_type'=> $typehtml, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1, 'orderby' => 'title', 'order' => 'ASC'));
                echo '<select name="'. $id_champ .'" id="'.$id_champ.'">';
                    echo '<option value = "" >All '.$label.' </option>';
                    foreach ($posts as $post) {
                        echo '<option value="', $post->ID, '"', get_option($id_champ) == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
                    }
                echo '</select>';
            } else {
                echo '<input id="'.$id_champ.'" type="'.$typehtml.'" name="'.$id_champ.'" value="'.get_option($id_champ).'">';
            }
    }
    echo '</div>';
}