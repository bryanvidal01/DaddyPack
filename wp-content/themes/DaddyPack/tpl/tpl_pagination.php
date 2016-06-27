<?php

// Pagination
$page_num = $paged;
if($page_num == '')
    $page_num = '1';
if(clrz_is_template('template-members.php'))
    $max_num_pages = ceil($user_search->total_users_for_query/$user_search->users_per_page);
else
    $max_num_pages = $wp_query->max_num_pages;

if(isset($stl_the_query) && is_object($stl_the_query)){
    $max_num_pages = $stl_the_query->max_num_pages;
}

$pas_pagination = 5;

$im_suivante = __('Suivant &rsaquo; ','clrz_lang');
$im_precedente = __('&lsaquo; Pr&eacute;c&eacute;dent','clrz_lang');

if($max_num_pages > 1){
    echo '<ul class="cssn_pagination">';
    // Si on a une page précédente
    echo (($page_num==1) ? '' : '<li><a href="'.get_pagenum_link($page_num-1).'" rel="prev">Précédent</a></li>')."\n";
    echo '<li>';
        echo '<ul>';
            if(is_search())
                $s = (isset($_GET['s'])) ? strip_tags($_GET['s']) : '';
            $cat_id = (isset($_GET['cat_id']) && ctype_digit($_GET['cat_id'])) ? '?cat_id='.$_GET['cat_id'] : '';

            for($i=1;$i<=$max_num_pages;$i++){
                if(($i > ($page_num - $pas_pagination) && $i < ($page_num + $pas_pagination)) || $i == 1 || $i == $max_num_pages){

                    // Si pagination affichée éloignée, on affiche ".." pour signaler la derniere page
                    if($i == $max_num_pages && $page_num < $i - $pas_pagination) echo '<li class="sep"><span>...</span></li> ';

                    echo '<li><a '. ($page_num == $i ? 'class="current"' : "").' href="'.get_pagenum_link($i).'">'.$i.'</a></li> ';

                    // Si pagination affichée éloignée, on affiche ".." pour signaler la premiere page
                    if($i == 1 && $page_num > $i + $pas_pagination) echo '<li class="sep"><span>...</span></li> ';

                }
            }
        echo '</ul>';
    echo '</li>'."\n";
    // Si on a une page suivante
    echo (($page_num == $max_num_pages) ? '' : '<li><a href="'.get_pagenum_link($page_num+1).'" rel="next">Suivant</a></li>')."\n";
    echo '</ul>';
}
