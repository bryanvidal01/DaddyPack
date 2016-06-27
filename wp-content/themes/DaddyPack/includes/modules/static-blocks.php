<?php

/*
  @TODO :
  - Vider le cache a la modification d'un bloc en back-office ( Save_posts )
 */

class CLRZ_Static_blocks {

    private $options = array();

    function __construct() {
        $this->set_options();
        $this->init();
        /* Regenerate cache */
    }

    private function set_options() {
        $this->options['cache_dir'] = WP_CONTENT_DIR . '/clrz_static_cache/';
    }

    private function init() {

        /* Verification du cache */
        if ( !is_dir( $this->options['cache_dir'] ) ) {
            mkdir( $this->options['cache_dir'] );
            @chmod( $this->options['cache_dir'], 0755 );
            file_put_contents( $this->options['cache_dir'].'.htaccess', 'deny from all' );
        }

        add_action( 'init', array( $this, 'register_posttypes' ) );
    }

    /* Generation du post type */
    public function register_posttypes() {
        register_post_type( 'static_blocks', array(
                'public' => true,
                'label' => __( 'Blocks Statiques', 'clrz_lang' )
            ) );
    }

    private function get_cache_filename( $slug ) {
        return $this->options['cache_dir'].'static-cache-'.$slug.'.php';
    }

    // Recuperation du contenu du fichier cache
    private function get_cached_block( $slug ) {
        $content_return = false;
        $cached_block_file = $this->get_cache_filename( $slug );
        if ( file_exists( $cached_block_file ) ) {
            $content_return = file_get_contents( $cached_block_file );
        }
        return $content_return;
    }

    // Mise en place du cache
    private function set_cached_block( $slug ) {
        $content_return = '';
        $args = array(
            'post_type' => 'static_blocks',
            'posts_per_page' => 1,
            'name' => $slug
        );
        $sb_query = new WP_Query( $args );
        if ( $sb_query->have_posts() ) {
            $sb_query->the_post();
            ob_start();
            the_content();
            $content_return = ob_get_clean();
            file_put_contents( $this->get_cache_filename( $slug ), $content_return );
        }
        // Create Static Block
        else {}
        wp_reset_postdata();
        return $content_return;
    }

    public function get_block( $slug ) {
        $content_return = '';
        $cached_block = $this->get_cached_block( $slug );
        if ( $cached_block == false ) {
            $content_return = $this->set_cached_block( $slug );
        }
        else {
            $content_return = $cached_block;
        }
        return $content_return;
    }
}

$CLRZ_Static_blocks = new CLRZ_Static_blocks();
