<?php

/* 
new ClrzImportFileToPost(array(
    'path' => '/Users/colorz/Desktop/image.png',
    'url' => 'http://www.colorz.fr/image.jpg',
),POSTID);
*/

class ClrzImportFileToPost {

    function __construct($file, $post_id) {

        $return = false;
        $wp_upload_dir = wp_upload_dir();

        // On verifie que le fichier est bien un tableau
        if(is_array($file)){
            // On télécharge un fichier depuis une URL
            if(isset($file['url'])){
                $new_file_name = $this->get_new_filename($file['url']);
                $filename = $wp_upload_dir['path'].'/'.$new_file_name;
                $response = wp_remote_get( $file['url'] );
                if(!is_wp_error( $response )) {
                    $return = file_put_contents($filename,$response['body']);
                }
            }
            // Ou on récupère un fichier sur ce serveur
            else if(isset($file['path']) && file_exists($file['path'])) {
                $new_file_name = $this->get_new_filename($file['path']);
                $filename = $wp_upload_dir['path'].'/'.$new_file_name;
                // Si le fichier original doit être détruit après
                if(isset($file['delete_after']) && $file['delete_after']){
                    $return = rename($file['path'], $filename);
                }
                else {
                    $return = copy($file['path'], $filename);
                }
            }
        }

        // Si le fichier a bien été transféré
        if($return !== FALSE){
            $wp_filetype = wp_check_filetype(basename($filename), null );
            $post_title = preg_replace('/\.[^.]+$/', '', basename($filename));
            $post_title = str_replace(array('-', '_', '.'), ' ', $post_title);
            $attachment = array(
               'guid' => $wp_upload_dir['baseurl'] . '/' . _wp_relative_upload_path( $filename ),
               'post_mime_type' => $wp_filetype['type'],
               'post_title' => $post_title,
               'post_content' => '',
               'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
            $return = $attach_id;
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );
        }

        return $return;
    }

    function get_new_filename($file){
        $name = time() . '-' . basename( $file );
        $name = str_replace(array(' ','\\','/'), '-' , $name);
        return strtolower($name);
    }
}