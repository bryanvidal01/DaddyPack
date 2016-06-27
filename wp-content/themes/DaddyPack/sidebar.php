<?php include dirname(__FILE__) . '/includes/clrz-check-in-wp.php';

// La fonction custom_global_clrz_sidebar_slug()
// doit exister dans includes/function/actions.php
global $global_clrz_sidebar_slug; $sidebar_slug = '';
if(isset($global_clrz_sidebar_slug) && !empty($global_clrz_sidebar_slug))
    $sidebar_slug = $global_clrz_sidebar_slug;

?>
<aside id="sidebar">
    <ul>
    <?php
        // On teste la sidebar appelée (existe et non vide)
        if(!dynamic_sidebar($sidebar_slug)){
            // On teste la sidebar par défaut (existe et non vide)
            if(!dynamic_sidebar('DefaultSideBar')){
                // On affiche des "widgets" par défaut.
                echo '<li id="sidebar-search" class="widget"><h3>Recherche</h3>';
                    get_search_form();
                echo '</li>';
                echo '<li id="sidebar-archives" class="widget"><h3>Archives</h3><ul>';
                    wp_get_archives('type=monthly');
                echo '</ul></li>';
                echo '<li id="sidebar-categories" class="widget"><h3>Catégories</h3><ul>';
                    wp_list_categories(array('title_li' => 0,'show_count' => 1));
                echo '</ul></li>';
            }
        }
    ?>
    </ul>
</aside><!--sidebar-->