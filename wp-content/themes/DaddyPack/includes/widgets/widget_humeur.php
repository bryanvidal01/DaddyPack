<?php

add_action('widgets_init', 'clrz_humeur_widget_register_widgets');

function clrz_humeur_widget_register_widgets() {
    register_widget('clrz_humeur_widget');
}

// Exemple de widget
class clrz_humeur_widget extends WP_Widget {

    public $values_def = array(
        'whum_title' => array('label' => 'Titre du widget', 'value' => 'Humeur'),
        'whum_citation' => array('label' => 'Citation', 'value' => 'The world needs dreamers and the world needs doers. But above all, the world needs dreamers who do.', 'type' => 'textarea'),
        'whum_auteur' => array('label' => 'Auteur', 'value' => 'Sarah Ban Breathnach'),
    );

    function clrz_humeur_widget() {
        parent::WP_Widget(false, '[CLRZ] Humeur');
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, $this->values_def);
        foreach ($this->values_def as $key => $value):
            echo '<p>' . $value['label'] . ': ';
            switch ($value['type']) {
                case 'textarea' :
                    echo '<textarea style="resize:vertical;" class="widefat" name="' . $this->get_field_name($key) . '">' . esc_attr($instance[$key]['value']) . '</textarea>';
                    break;
                default :
                    echo '<input class="widefat" name="' . $this->get_field_name($key) . '"  type="text" value="' . esc_attr($instance[$key]['value']) . '" />';
            }
            echo '</p>';
        endforeach;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        foreach ($this->values_def as $key => $value):
            $instance[$key]['value'] = strip_tags($new_instance[$key]);
        endforeach;
        return $instance;
    }

    function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . $instance['whum_title']['value'] . $args['after_title'];
        echo '<blockquote>' . wpautop($instance['whum_citation']['value']) . '<footer>' . wpautop($instance['whum_auteur']['value']) . '</footer></blockquote>';
        echo $args['after_widget'];
    }

}
