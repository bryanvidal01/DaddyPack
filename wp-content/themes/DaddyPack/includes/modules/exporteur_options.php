<?php

if(strstr(site_url(), 'feed.colorz.fr')==false){
    if(file_exists(TEMPLATEPATH.'/includes/params/options_fields.php'))
        include(TEMPLATEPATH.'/includes/params/options_fields.php');
}else{
	add_action( 'update_option', 'clrz_export_options', 1, 3 );
//    add_action('acf/update_field', 'clrz_export_options');
//    add_action('acf/delete_field', 'clrz_export_options');
//    add_action('acf/register_fields', 'clrz_export_options');
}



function clrz_export_options($option, $old_value, $value){

    if(!function_exists("ot_settings_id") || $option!=ot_settings_id())
        return;

    $filepath = TEMPLATEPATH.'/includes/params/options_fields.php';
    if(!file_exists($filepath))
        $handle = fopen($filepath, 'w') or die('Cannot open file:  '.$my_file);

    $content              = '';
    $build_settings       = '';
    $contextual_help      = '';
    $sections             = '';
    $settings             = '';
    $option_tree_settings = get_option( ot_settings_id(), array() );

    // Domain string helper
    function ot_I18n_string( $string ) {
      if ( ! empty( $string ) && isset( $_POST['domain'] ) && ! empty( $_POST['domain'] ) ) {
        $domain = str_replace( ' ', '-', trim( $_POST['domain'] ) );
        return "__( '$string', '$domain' )";
      }
      return "'$string'";
    }

    /* build contextual help content */
    if ( isset( $option_tree_settings['contextual_help']['content'] ) ) {
      $help = '';
      foreach( $option_tree_settings['contextual_help']['content'] as $value ) {
        $_id = isset( $value['id'] ) ? $value['id'] : '';
        $_title = ot_I18n_string( isset( $value['title'] ) ? str_replace( "'", "\'", $value['title'] ) : '' );
        $_content = ot_I18n_string( isset( $value['content'] ) ? html_entity_decode(  str_replace( "'", "\'", $value['content'] ) ) : '' );
        $help.= "
        array(
          'id'        => '$_id',
          'title'     => $_title,
          'content'   => $_content
        ),";
      }
      $help = substr_replace( $help, '' , -1 );
      $contextual_help = "
      'content'       => array( $help
      ),";
    }

    /* build contextual help sidebar */
    if ( isset( $option_tree_settings['contextual_help']['sidebar'] ) ) {
      $contextual_help.= "
      'sidebar'       => " . ot_I18n_string( html_entity_decode(  str_replace( "'", "\'", $option_tree_settings['contextual_help']['sidebar'] ) ) );
    }

    /* check that $contexual_help has a value and add to $build_settings */
    if ( '' != $contextual_help ) {
      $build_settings.= "
    'contextual_help' => array( $contextual_help
    ),";
    }

    /* build sections */
    if ( isset( $option_tree_settings['sections'] ) ) {
      foreach( $option_tree_settings['sections'] as $value ) {
        $_id = isset( $value['id'] ) ? $value['id'] : '';
        $_title = ot_I18n_string( isset( $value['title'] ) ? str_replace( "'", "\'", $value['title'] ) : '' );
        $sections.= "
      array(
        'id'          => '$_id',
        'title'       => $_title
      ),";
      }
      $sections = substr_replace( $sections, '' , -1 );
    }

    /* check that $sections has a value and add to $build_settings */
    if ( '' != $sections ) {
      $build_settings.= "
    'sections'        => array( $sections
    )";
    }

    /* build settings */
    if ( isset( $option_tree_settings['settings'] ) ) {
      foreach( $option_tree_settings['settings'] as $value ) {
        $_id = isset( $value['id'] ) ? $value['id'] : '';
        $_label = ot_I18n_string( isset( $value['label'] ) ? str_replace( "'", "\'", $value['label'] ) : '' );
        $_desc = ot_I18n_string( isset( $value['desc'] ) ? str_replace( "'", "\'", $value['desc'] ) : '' );
        $_std = isset( $value['std'] ) ? str_replace( "'", "\'", $value['std'] ) : '';
        $_type = isset( $value['type'] ) ? $value['type'] : '';
        $_section = isset( $value['section'] ) ? $value['section'] : '';
        $_rows = isset( $value['rows'] ) ? $value['rows'] : '';
        $_post_type = isset( $value['post_type'] ) ? $value['post_type'] : '';
        $_taxonomy = isset( $value['taxonomy'] ) ? $value['taxonomy'] : '';
        $_min_max_step = isset( $value['min_max_step'] ) ? $value['min_max_step'] : '';
        $_class = isset( $value['class'] ) ? $value['class'] : '';
        $_condition = isset( $value['condition'] ) ? $value['condition'] : '';
        $_operator = isset( $value['operator'] ) ? $value['operator'] : '';

        $choices = '';
        if ( isset( $value['choices'] ) && ! empty( $value['choices'] ) ) {
          foreach( $value['choices'] as $choice ) {
            $_choice_value = isset( $choice['value'] ) ? str_replace( "'", "\'", $choice['value'] ) : '';
            $_choice_label = ot_I18n_string( isset( $choice['label'] ) ? str_replace( "'", "\'", $choice['label'] ) : '' );
            $_choice_src = isset( $choice['src'] ) ? str_replace( "'", "\'", $choice['src'] ) : '';
            $choices.= "
          array(
            'value'       => '$_choice_value',
            'label'       => $_choice_label,
            'src'         => '$_choice_src'
          ),";
          }
          $choices = substr_replace( $choices, '' , -1 );
          $choices = ",
        'choices'     => array( $choices
        )";
        }

        $std = "'$_std'";
        if ( is_array( $_std ) ) {
          $std_array = array();
          foreach( $_std as $_sk => $_sv ) {
            $std_array[] = "'$_sk' => '$_sv'";
          }
          $std = 'array(
' . implode( ",\n", $std_array ) . '
          )';
        }

        $setting_settings = '';
        if ( isset( $value['settings'] ) && ! empty( $value['settings'] ) ) {
          foreach( $value['settings'] as $setting ) {
            $_setting_id = isset( $setting['id'] ) ? $setting['id'] : '';
            $_setting_label = ot_I18n_string( isset( $setting['label'] ) ? str_replace( "'", "\'", $setting['label'] ) : '' );
            $_setting_desc = ot_I18n_string( isset( $setting['desc'] ) ? str_replace( "'", "\'", $setting['desc'] ) : '' );
            $_setting_std = isset( $setting['std'] ) ? $setting['std'] : '';
            $_setting_type = isset( $setting['type'] ) ? $setting['type'] : '';
            $_setting_rows = isset( $setting['rows'] ) ? $setting['rows'] : '';
            $_setting_post_type = isset( $setting['post_type'] ) ? $setting['post_type'] : '';
            $_setting_taxonomy = isset( $setting['taxonomy'] ) ? $setting['taxonomy'] : '';
            $_setting_min_max_step = isset( $setting['min_max_step'] ) ? $setting['min_max_step'] : '';
            $_setting_class = isset( $setting['class'] ) ? $setting['class'] : '';
            $_setting_condition = isset( $setting['condition'] ) ? $setting['condition'] : '';
            $_setting_operator = isset( $setting['operator'] ) ? $setting['operator'] : '';

            $setting_choices = '';
            if ( isset( $setting['choices'] ) && ! empty( $setting['choices'] ) ) {
              foreach( $setting['choices'] as $setting_choice ) {
                $_setting_choice_value = isset( $setting_choice['value'] ) ? $setting_choice['value'] : '';
                $_setting_choice_label = ot_I18n_string( isset( $setting_choice['label'] ) ? str_replace( "'", "\'", $setting_choice['label'] ) : '' );
                $_setting_choice_src = isset( $setting_choice['src'] ) ? str_replace( "'", "\'", $setting_choice['src'] ) : '';
                $setting_choices.= "
              array(
                'value'       => '$_setting_choice_value',
                'label'       => $_setting_choice_label,
                'src'         => '$_setting_choice_src'
              ),";
              }
              $setting_choices = substr_replace( $setting_choices, '' , -1 );
              $setting_choices = ",
            'choices'     => array( $setting_choices
            )";
            }

            $setting_std = "'$_setting_std'";
            if ( is_array( $_setting_std ) ) {
              $setting_std_array = array();
              foreach( $_setting_std as $_ssk => $_ssv ) {
                $setting_std_array[] = "'$_ssk' => '$_ssv'";
              }
              $setting_std = 'array(
' . implode( ",\n", $setting_std_array ) . '
              )';
            }

            $setting_settings.= "
          array(
            'id'          => '$_setting_id',
            'label'       => $_setting_label,
            'desc'        => $_setting_desc,
            'std'         => $setting_std,
            'type'        => '$_setting_type',
            'rows'        => '$_setting_rows',
            'post_type'   => '$_setting_post_type',
            'taxonomy'    => '$_setting_taxonomy',
            'min_max_step'=> '$_setting_min_max_step',
            'class'       => '$_setting_class',
            'condition'   => '$_setting_condition',
            'operator'    => '$_setting_operator'$setting_choices
          ),";
          }
          $setting_settings = substr_replace( $setting_settings, '' , -1 );
          $setting_settings = ",
        'settings'    => array( $setting_settings
        )";
        }

        $settings.= "
      array(
        'id'          => '$_id',
        'label'       => $_label,
        'desc'        => $_desc,
        'std'         => $std,
        'type'        => '$_type',
        'section'     => '$_section',
        'rows'        => '$_rows',
        'post_type'   => '$_post_type',
        'taxonomy'    => '$_taxonomy',
        'min_max_step'=> '$_min_max_step',
        'class'       => '$_class',
        'condition'   => '$_condition',
        'operator'    => '$_operator'$choices$setting_settings
      ),";
      }
      $settings = substr_replace( $settings, '' , -1 );
    }

    /* check that $sections has a value and add to $build_settings */
    if ( '' != $settings ) {
      $build_settings.= ",
    'settings'        => array( $settings
    )";
    }

    $content.= "<?php
/**
 * Initialize the custom theme options.
 */
add_action( 'admin_init', 'custom_theme_options' );

/**
 * Build the custom settings & update OptionTree.
 */
function custom_theme_options() {

  /* OptionTree is not loaded yet */
  if ( ! function_exists( 'ot_settings_id' ) )
    return false;

  /**
   * Get a copy of the saved settings array.
   */
  \$saved_settings = get_option( ot_settings_id(), array() );

  /**
   * Custom settings array that will eventually be
   * passes to the OptionTree Settings API Class.
   */
  \$custom_settings = array( $build_settings
  );

  /* allow settings to be filtered before saving */
  \$custom_settings = apply_filters( ot_settings_id() . '_args', \$custom_settings );

  /* settings are not the same update the DB */
  if ( \$saved_settings !== \$custom_settings ) {
    update_option( ot_settings_id(), \$custom_settings );
  }

  /* Lets OptionTree know the UI Builder is being overridden */
  global \$ot_has_custom_theme_options;
  \$ot_has_custom_theme_options = true;

}";


    file_put_contents($filepath, $content);
}

