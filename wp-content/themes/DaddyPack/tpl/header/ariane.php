<?php
// à utiliser sur un WP >= 3.1

// Selon le nombre d'affichages de fils d'ariane sur la page
if(!isset($compteur_ariane))
    $compteur_ariane = 0;
    $compteur_ariane++;
// Microformat recommandé par Google
function element_ariane($link,$title,$rel='',$separator = '›'){
    if($separator != '') $separator = '<span class="element_ariane_separator"> '.$separator.' </span>';
    return '<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="element_ariane">'.
    ($link == '' ? '<span ':'<a href="'.$link.'" itemprop="url" '.($rel !='' ? 'rel="'.$rel.'"':'').''). ' class="element_ariane_link">'.
    '<span itemprop="title" class="element_ariane_wrapper">'.$title.'</span>'.
    ($link == '' ? '</span>':'</a>').
    $separator.
    '</li>'."\n";
}

?><div id="ariane-<?php echo $compteur_ariane; ?>" class="ariane">
    <ul class="cssc-ariane">
        <?php
        // On affiche toujours l'accueil
        echo element_ariane(site_url(), 'Accueil', 'home');

        if(clrz_is_template('author.php')){
            $auteur = get_user_by('id',$author);
            echo element_ariane('', 'Auteur : '.$auteur->display_name, '','');
        }

        if(clrz_is_template('archive.php')){
            if ( is_day() )
                echo element_ariane('', 'Archives - '.get_the_date('j F Y'), '','');
            elseif ( is_month() )
                echo element_ariane('', 'Archives - '.get_the_date('F Y'), '','');
            elseif ( is_year() )
                echo element_ariane('', 'Archives - '.get_the_date('Y'), '','');

        }
        // Post types & post
        $cpost_types = get_post_types('','names');
        foreach ($cpost_types as $post_type ) {
            if(!in_array($post_type,array('revision','attachment','nav_menu_item','page'))){
                $obj = get_post_type_object($post_type);
                // Archive de post type
                if(clrz_is_template(array('archive-'.$post_type.'.php','type-'.$post_type.'.php'))){
                    echo element_ariane('', $obj->labels->name, '','');
                }
                // Single
                if(is_singular($post_type)){
                    if($post_type != 'post')
                        echo element_ariane(get_post_type_archive_link($post_type), $obj->labels->name, '');
                    $categories = get_the_category();
                    if(!empty($categories)){
                        $cat_parent = array();
                        foreach($categories as $category)
                            $cat_parent[$category->parent][] = $category;
                        ksort($cat_parent);

                        // On affiche toutes les categories, triées par parent
                        foreach($cat_parent as $categories){
                            foreach($categories as $category){
                                echo element_ariane(get_category_link( $category->cat_ID ), $category->cat_name, '');
                            }
                        }
                    }
                    echo element_ariane('', get_the_title(), '','');
                }
            }
        }

        // Page single
        if(is_page()){
            global $wp_query;
            $ancetres = get_post_ancestors(get_the_ID());
            if(is_array($ancetres)){
                krsort($ancetres);
                foreach($ancetres as $ancetre){
                    echo element_ariane(get_permalink($ancetre), get_the_title($ancetre), '');
                }
            }
            echo element_ariane('', get_the_title(), '','');
        }

        // Page de taxonomie
        if(is_category() || is_tag() || is_tax()){
            $term = get_queried_object();
            if(!isset($term->taxonomy)) break;
            $terms_taxonomy = get_taxonomy($term->taxonomy);
            if(!isset($terms_taxonomy->labels->singular_name)) break;
            echo element_ariane('', $terms_taxonomy->labels->singular_name.' : '.single_term_title('',false), '','');
        }

        // Résultat de recherche
        if(is_search()){
            echo element_ariane('', 'Recherche : '.get_search_query(), '','');
        }


        ?>
    </ul>
</div>