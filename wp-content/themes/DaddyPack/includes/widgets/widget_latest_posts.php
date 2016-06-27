<?php

add_action('widgets_init', 'clrz_latest_posts_register_widgets');

function clrz_latest_posts_register_widgets() {
    register_widget('clrz_latest_posts');
}

// Exemple de widget
class clrz_latest_posts extends WP_Widget {

    function clrz_latest_posts() {
        parent::WP_Widget(false, '[CLRZ] Latest Posts');
    }

    function form($instance) {

    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function widget($args, $instance) {
        $the_query = new WP_Query(array(
                    'posts_per_page' => 5,
                    'post_type' => 'post',
                    'orderby' => 'date',
                    'order' => 'desc'
                ));
        if ($the_query->have_posts()) :
            echo $args['before_widget'];
            echo '<ul>';
            echo $args['before_title'].__('Derniers articles').$args['after_title'];
            while ($the_query->have_posts()) : $the_query->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> <time datetime="' . get_the_time(DATE_W3C) . '">' . get_the_time('l j F Y') . '</time></li>';
            endwhile;
            echo '</ul>';
            echo $args['after_widget'];
        endif;
        wp_reset_postdata();

    }

}
