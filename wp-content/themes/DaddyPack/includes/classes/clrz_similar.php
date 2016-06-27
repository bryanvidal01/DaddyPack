<?php

/*
TODO :
- Checker les liens internes à l'article et ajouter 2 points à ces posts
*/


class dklg_similar_posts {
	public $similar_posts = array();
	private $post;
	private $main_taxos = array();

	function __construct($post,$taxos = array('post_tag' => array(),'category' => array())){
		$this->post = $post;
		$this->main_taxos = $taxos;
		foreach($this->main_taxos as $id_tax => $val){
			$this->similar_taxos($id_tax);
		}

		$this->similar_title($this->post->post_title,$this->post->ID);
		$this->similar_author($this->post->post_author);
		$this->sort_similars();
	}

	function return_similar($nb=5){
		$retour = '<ul>';
		$i=0;
		foreach($this->similar_posts as $id => $similar){
			ob_start();
			?><li class="loop-similar note-<?php echo $similar['note']; ?> sim-post-<?php echo $id; ?>"><a href="<?php echo $similar['permalink']; ?>"><?php echo $similar['title']; ?></a></li><?php
			$retour .= ob_get_clean();
			if(++$i >= $nb) break;
		}
		$retour .= '</ul>';

		return $retour;
	}

	function sort_similars(){
		$tmp_similars = $this->similar_posts;
		$this->similar_posts = array();

		$max_note = 0;

		// On cherche la note maximale
		foreach($tmp_similars as $id=>$postt){
			$max_note = max($max_note,$postt['note']);
		}

		// On trie par note descendante
		for($i=$max_note;$i>=0;$i--){
			foreach($tmp_similars as $id => $postt){
				if($postt['note'] == $i){
					$this->similar_posts[$id] = $postt;
					unset($tmp_similars[$id]);
				}
			}
		}
	}

	function similar_author($author_id){
		$args = array(
			'post_type' => 'post',
			'post__not_in' => array($this->post->ID),
		    'posts_per_page' => 100,
			'author' => $author_id
		);

		$the_query = new WP_Query($args);
		while ($the_query->have_posts()) : $the_query->the_post();
			// 1 point pour chaque tag en commun
			$this->add_point(get_the_ID());
		endwhile;
		wp_reset_postdata();
	}

	function similar_title($title,$id){
		$title = strip_tags($title);
		$title_words_basics = explode(' ',$title);
		$title_words_sql = array();
		$title_words = array();
		foreach($title_words_basics as $word){
			if(strlen($word) > 2){
				$title_words_sql[] = " post_title LIKE '%".addslashes($word)."%' ";
				$title_words[] = $word;
			}
		}


		if(!empty($title_words)){
			global $wpdb;
			$results = $wpdb->get_results("SELECT post_title,ID FROM wp_posts WHERE ID <> '".$id."' AND post_status = 'publish' AND (".implode(' OR ', $title_words_sql).")");
			foreach($results as $result){
				foreach($title_words as $word){
					if(strstr($result->post_title,$word) !== FALSE){
						$this->add_point($result->ID);
						$this->add_point($result->ID);
					}
				}
			}
		}
	}

	function similar_taxos($taxonomy_choisie){
		// Récupérer les posts ayant les mêmes taxos
		$this->main_taxos[$taxonomy_choisie] = $this->get_taxos_ids($this->post->ID,$taxonomy_choisie);

		foreach($this->main_taxos[$taxonomy_choisie] as $tagid){

			$args = array(
				'post_type' => 'post',
				'post__not_in' => array($this->post->ID),
			    'posts_per_page' => 50,
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy_choisie,
						'field' => 'id',
						'terms' => array($tagid)
					)
				)
			);

			$the_query = new WP_Query($args);
			while ($the_query->have_posts()) : $the_query->the_post();

			    $taags = $this->get_taxos_ids(get_the_ID(),$taxonomy_choisie);
				foreach($taags as $tagid_cur){
					// 1 point pour chaque tag en commun
					if($tagid_cur == $tagid){
						$this->add_point(get_the_ID());
					}
				}
			endwhile;
			wp_reset_postdata();
		}
	}

	function add_post($postid){
		if(!isset($this->similar_posts[$postid])){
			$this->similar_posts[$postid] = array(
				'permalink' => get_permalink($postid),
				'author' => get_the_author_meta('ID'),
				'title' => get_the_title($postid),
				'note' => 0
			);
		}
	}

	function add_point($postid){
		$this->add_post($postid);
		$this->similar_posts[$postid]['note']++;
	}

	function get_taxos_ids($postid,$taxonomy){
		$taxs = array();
		$taxs_tmp = get_the_terms($postid,$taxonomy);
		if($taxs_tmp !== FALSE){
			foreach($taxs_tmp as $tax){
				$taxs[] = $tax->term_id;
			}
		}
		return $taxs;
	}
}