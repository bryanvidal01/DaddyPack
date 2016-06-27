<?php include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php'; ?>
<form role="search" action="<?php bloginfo( 'url' ); ?>" method="get" id="searchform">
    <fieldset>
        <div>
            <label for="search">Rechercher sur <?php bloginfo( 'name' ); ?></label>
            <input class="fake-placeholder-me" type="text" name="s" id="search" value="<?php the_search_query(); ?>" placeholder="<?php echo __( 'Votre recherche', 'clrz_lang' ); ?>" />
            <input type="submit" value="Rechercher" />
        </div>
    </fieldset>
</form>
