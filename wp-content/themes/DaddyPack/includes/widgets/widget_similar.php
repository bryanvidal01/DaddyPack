<?php
add_action( 'widgets_init', 'dk_widg_similar_post_register_widgets' );
function dk_widg_similar_post_register_widgets() {
    register_widget( 'dk_widg_similar_post' );
}

// Exemple de widget
class dk_widg_similar_post extends WP_Widget {
    function dk_widg_similar_post(){parent::WP_Widget(false, '[DKLG] Similar Posts');}
    function form($instance){}
    function update($new_instance, $old_instance) {return $new_instance;}
    function widget($args, $instance) {
		global $post;
		if(is_single()){
			$similar = new dklg_similar_posts($post);
			if(count($similar->similar_posts) > 0){
		        echo $args['before_widget'];
		        echo $args['before_title'].__('Articles Similaires').$args['after_title'];
		        echo $similar->return_similar(5);
		        echo $args['after_widget'];
			}
		}
    }
}
