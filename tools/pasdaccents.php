<?php

function clrz_riplace($chaine) {
    $convert_list_before = array(
        'Ã©', 'è', 'à', 'â', 'É', 'é', 'ê', 'û', 'â', 'e&Igrave;', '́́'
    );
    $convert_list_after = array(
        'e', 'e', 'a', 'a', 'E', 'e', 'e', 'u', 'a', 'e', ''
    );
    $chaine3 = str_replace($convert_list_before, $convert_list_after, $chaine);
    $chaine_tmp = '';
    $ex_chaine = str_split($chaine3);
    foreach ($ex_chaine as $ch) {
        if (preg_match('#^([a-zA-Z0-9\:\.\/\-\_]+)$#', $ch)) {
            $chaine_tmp .= $ch;
        }
    }
    return $chaine_tmp;
}

//return;
if (!is_admin()) {
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'attachment',
        'post_status' => 'all'
    );
    $the_query = new WP_Query($args);
    $upload_dir = wp_upload_dir();
    
    while ($the_query->have_posts()) : $the_query->the_post();
        $meta_data = wp_get_attachment_metadata($post->ID);
        $new_meta_data = $meta_data;

        $dir_upload = explode('/', $new_meta_data['file']);
        unset($dir_upload[count($dir_upload) - 1]);

        $uploadir = $upload_dir['basedir'] . '/';
        $dir_uploadz = $uploadir . implode('/', $dir_upload) . '/';


        $newpostguid = clrz_riplace($post->guid);
        $new_meta_data['file'] = clrz_riplace($meta_data['file']);
        echo $post->ID . ' ';
        echo "\n";
        echo ($meta_data['file']);
        echo "\n";
        echo $new_meta_data['file'];
        echo "\n\n";

        @rename($uploadir . $meta_data['file'], $uploadir . $new_meta_data['file']);

        wp_update_post(array('ID' => get_the_ID(), 'guid' => $newpostguid));

        foreach ($meta_data['sizes'] as $id => $size) {
            $new_meta_data['sizes'][$id]['file'] = clrz_riplace($size['file']);
            @rename($dir_uploadz . $size['file'], $dir_uploadz . $new_meta_data['sizes'][$id]['file']);
        }

        update_post_meta($post->ID, '_wp_attached_file', $new_meta_data['file']);
        wp_update_attachment_metadata($post->ID, $new_meta_data);

    endwhile;
    wp_reset_postdata();
    die;
}