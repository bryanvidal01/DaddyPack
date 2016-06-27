<?php
$backup = $post;  // backup the current object
$tags = wp_get_post_tags($post->ID);
$tagIDs = array();
if ($tags) {
    $tagcount = count($tags);
    for ($i = 0; $i < $tagcount; $i++) {
        $tagIDs[$i] = $tags[$i]->term_id;
    }
    $args=array(
        'tag__in' => $tagIDs,
        'post__not_in' => array($post->ID),
        'posts_per_page'=>5
    );
    $my_query = new WP_Query($args);
    if( $my_query->have_posts() ) {
        echo '<h3>Articles similaires</h3>';
        echo '<ul>';
        while ($my_query->have_posts()) : $my_query->the_post(); 
            ?><li><h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4><?php the_excerpt(); ?></li><?php 
        endwhile;
        echo '</ul>';
    }
}
$post = $backup;  // copy it back
wp_reset_query(); // to use the original query again
