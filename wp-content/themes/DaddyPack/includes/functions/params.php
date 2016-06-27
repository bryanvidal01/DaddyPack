<?php

// Creation des post-types
function clrz_launch_create_posttypes($clrz_post_types){

    foreach($clrz_post_types as $idpt => $pt){
        if(!isset($pt['pluriel'])) $pt['pluriel'] = $idpt;
        if(!isset($pt['singulier'])) $pt['singulier'] = $idpt;
        if(!isset($pt['feminin'])) $pt['feminin'] = 0;
        if(!isset($pt['supports'])) $pt['supports'] = array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes');
        if(!isset($pt['taxonomies'])) $pt['taxonomies'] = array('post_tag','category');
        $rewrite = (isset($pt['rewrite'])) ? $pt['rewrite'] : $idpt;

        $fem_single = ($pt['feminin'] ? 'e':'');
        $article_single = 'un'.$fem_single;
        $new_single = 'nouve'.($pt['feminin'] ? 'lle':'au');
        $no_single = 'aucun'.$fem_single;

        $_PT_PARAMS = array(
            'label' => ucfirst($pt['pluriel']),
            'singular_label' => ucfirst($pt['singulier']),
            'description' => '',
            'public' => true,
            'publicly_queryable' => true,
            'taxonomies' => $pt['taxonomies'],
            'menu_position' => 5,
            'show_ui' => true,
            '_builtin' => false,
            'show_in_menu' => true,
            'hierarchical' => false,
            'query_var' => true,
            'has_archive' => $rewrite,
            'rewrite' => array('slug' => $rewrite),
            'supports' => $pt['supports'],
            'labels' => array (
                'name' => ucfirst($pt['pluriel']),
                'name_admin_bar' =>  ucfirst($pt['singulier']),
                'singular_name' => '',
                'menu_name' => ucfirst($pt['pluriel']),
                'add_new' => 'Ajouter '.$article_single.' '.$pt['singulier'],
                'add_new_item' => 'Ajouter '.$article_single.' '.$new_single.' '.$pt['singulier'],
                'edit' => 'Modifier',
                'edit_item' => 'Modifier '.$article_single.' '.$pt['singulier'],
                'new_item' => ucfirst($new_single).' '.$pt['singulier'],
                'view' => 'Voir des '.$pt['pluriel'],
                'view_item' => 'Voir '.$article_single.' '.$pt['singulier'],
                'search_items' => 'Rechercher dans les '.$pt['pluriel'],
                'not_found' => ucfirst($no_single).' '.$pt['singulier'].' trouv&eacute;'.$fem_single,
                'not_found_in_trash' => ucfirst($no_single).' '.$pt['singulier'].' trouv&eacute;'.$fem_single.' dans la corbeille',
                'parent' => ucfirst($pt['pluriel']).' parent'.$fem_single.'s',
            ),
                'capability_type'=>(isset($pt['capability_type'])) ? $pt['capability_type'] : 'post',
        );

        if(isset($pt['capabilities']))
            $_PT_PARAMS['capabilities'] = $pt['capabilities'];
        if(isset($pt['menu_icon']))
            $_PT_PARAMS['menu_icon'] = $pt['menu_icon'];

        register_post_type($idpt,$_PT_PARAMS);
    }
}





// Creation des taxos
function clrz_launch_create_taxonomies($clrz_taxonomies){
    foreach($clrz_taxonomies as $slug => $args){

        if(!isset($args['hierarchical']) || !is_bool($args['hierarchical']))
            $args['hierarchical'] = true;
        if(!isset($args['feminin']) || !is_bool($args['hierarchical']))
            $args['feminin'] = true;
        if(empty($args['post_types']))
            $args['post_types'] = array('post','page');
        if(is_string($args['post_types']))
            $args['post_types'] = array($args['post_types']);

        $un_fem = 'un'.($args['feminin'] ? 'e':'').' '.$args['name'];

        register_taxonomy($slug,$args['post_types'], array(
            'hierarchical' => $args['hierarchical'],
            'labels' => array(
            'name' => _x( $args['pluriel'], 'taxonomy general name' ),
            'singular_name' => _x( $args['name'], 'taxonomy singular name' ),
            'search_items' =>  ( 'Search '.$args['pluriel'] ),
            'all_items' => ( 'All '.$args['pluriel'] ),
            'parent_item' => ( 'Parent '.$args['name'] ),
            'parent_item_colon' => ( 'Parent '.$args['name'].':' ),
            'edit_item' => ( 'Modifier '.$un_fem ),
            'update_item' => ( 'Mettre &agrave; jour '.$un_fem ),
            'add_new_item' => ( 'Ajouter '.$un_fem ),
            'new_item_name' => ( 'Nouve'.($args['feminin'] ? 'lle':'au').' nom de '.$args['name'].' ' ),
            'menu_name' => ( $args['pluriel'] ),
        ),
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => $slug ),
        ));
    }

    $default_taxonomies = array( 'category' => array(), 'post_tag' => array() );
    $all_taxonomies = array_merge( $clrz_taxonomies, $default_taxonomies );

    foreach ($all_taxonomies as $slug => $args) {
        add_action($slug . '_edit_form_fields', 'extra_edit_tax_fields', 10, 2);
        add_action($slug . '_add_form_fields', 'extra_add_tax_fields', 10, 2);
        add_action('edited_' . $slug, 'save_extra_taxonomy_fields', 10, 2);
        add_action('create_' . $slug, 'save_extra_taxonomy_fields', 10, 2);
    }
}

// Save extra taxonomy fields callback function.
function save_extra_taxonomy_fields( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "clrz_meta_taxonomy_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option( "clrz_meta_taxonomy_$t_id", $term_meta );
    }
}

// Edit taxonomy page
function extra_edit_tax_fields($tag) {
    // Check for existing taxonomy meta for term ID.
    $t_id = $tag->term_id;
    $term_meta = get_option( "clrz_meta_taxonomy_$t_id" );
    global $clrz_taxonomies_extra_fields;
    foreach($clrz_taxonomies_extra_fields as $id_field => $field_values) {
        if(!isset($field_values['taxonomies'])) $field_values['taxonomies'] = array();
        if(!isset($field_values['type'])) $field_values['type'] = 'text';
        if(in_array($tag->taxonomy,$field_values['taxonomies'])){ ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="clrz_field_<?php echo $id_field; ?>"><?php echo $field_values['name']; ?></label>
                </th>
                <td>
                    <?php switch ($field_values['type']) {
                        case 'textarea':
                            ?><textarea name="term_meta[<?php echo $id_field; ?>]" id="clrz_field_<?php echo $id_field; ?>"><?php echo esc_attr( $term_meta[$id_field] ) ? esc_attr( $term_meta[$id_field] ) : ''; ?></textarea><?php
                        break;
                        default:
                            ?><input type="text" name="term_meta[<?php echo $id_field; ?>]" id="clrz_field_<?php echo $id_field; ?>" value="<?php echo esc_attr( $term_meta[$id_field] ) ? esc_attr( $term_meta[$id_field] ) : ''; ?>" /><?php
                        break;
                    } ?>
                    <?php echo (isset($field_values['description']) ? '<p class="description">'.$field_values['description'].'</p>' : ''); ?>
                </td>
            </tr>
        <?php }
    }
}


// Add taxonomy page
function extra_add_tax_fields( $tag ) {
    // Check for existing taxonomy meta for term ID.
    global $clrz_taxonomies_extra_fields;
    foreach($clrz_taxonomies_extra_fields as $id_field => $field_values) {
        if(!isset($field_values['taxonomies'])) $field_values['taxonomies'] = array();
        if(!isset($field_values['type'])) $field_values['type'] = 'text';
        if(in_array($tag,$field_values['taxonomies'])){ ?>
            <div class="form-field">
                <label for="clrz_field_<?php echo $id_field; ?>"><?php echo $field_values['name']; ?></label>
                <?php switch ($field_values['type']) {
                    case 'textarea':
                        ?><textarea name="term_meta[<?php echo $id_field; ?>]" id="clrz_field_<?php echo $id_field; ?>"></textarea><?php
                        break;

                    default:
                        ?><input type="text" name="term_meta[<?php echo $id_field; ?>]" id="clrz_field_<?php echo $id_field; ?>" value="" /><?php
                        break;
                } ?>
                <?php echo (isset($field_values['description']) ? '<p class="description">'.$field_values['description'].'</p>' : ''); ?>
            </div>
        <?php }
    }
}

