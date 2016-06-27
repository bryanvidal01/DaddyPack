<?php

add_action( 'widgets_init', 'Changez_Cet_Identifiant_svp_register_widgets' );
function Changez_Cet_Identifiant_svp_register_widgets() {
    register_widget( 'Changez_Cet_Identifiant_svp' );
}

class Changez_Cet_Identifiant_svp extends WP_Widget {
    function Changez_Cet_Identifiant_svp() {parent::WP_Widget( false,
        'Widget par défaut',
        array( 'description' => 'Un widget à utiliser comme modèle' )
    );}
    function form( $instance ) {}
    function update( $new_instance, $old_instance ) {return $new_instance;}
    function widget( $args, $instance ) {
        echo $args['before_widget'];
        // Content
        echo $args['after_widget'];
    }
}
