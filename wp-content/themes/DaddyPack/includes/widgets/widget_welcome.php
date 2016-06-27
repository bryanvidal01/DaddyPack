<?php

add_action('widgets_init', 'clrz_welcome_txt_register_widgets');

function clrz_welcome_txt_register_widgets() {
    register_widget('clrz_welcome_txt');
}

class clrz_welcome_txt extends WP_Widget {

    public $values_def = array(
        'wcom_title' => array('label' => 'Titre du widget', 'value' => 'Welcome'),
        'wcom_content' => array('label' => 'Contenu du widget', 'value' => 'Welcome', 'type' => 'textarea'),
        'wcom_link_txt' => array('label' => 'Libellé du lien', 'value' => 'En savoir +'),
        'wcom_link_dest' => array('label' => 'Destination(ID) du lien', 'value' => '1'),
    );

    function clrz_welcome_txt() {
        parent::WP_Widget(false, '[CLRZ] Welcome');
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
        echo $args['before_title'] . $instance['wcom_title']['value'] . $args['after_title'];
        echo '<div class="content">' . wpautop($instance['wcom_content']['value']) . '</div>';
        echo '<div class="more">' . '<a href="' . get_permalink($instance['wcom_link_dest']['value']) . '">' . $instance['wcom_link_txt']['value'] . '</a>' . '</div>';
        echo $args['after_widget'];
    }

}
