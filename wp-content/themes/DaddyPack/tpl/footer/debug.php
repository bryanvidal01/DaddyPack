<?php if ( WP_DEBUG ) { ?>
<div id="debug-footer">
<p>
    <?php echo get_num_queries(); ?> queries |
    <?php timer_stop( 1 ); ?>  seconds |
    <?php echo round( memory_get_peak_usage()/( 1024*1024 ), 3 ); ?>mo |
    Template: <?php echo clrz_get_file_template_name(); ?>
</p>
<?php
    if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
        $topreq = array();
        $reql = array();
        foreach ( $wpdb->queries as $req ) {
            $reql[] = trim( str_replace( array( "\n", "\t" ), ' ', $req[0] ) );
        }
        ksort( $reql );
        echo '<pre>';  var_dump( $reql ); echo '</pre>';
    }
    ?></div>
<?php }
