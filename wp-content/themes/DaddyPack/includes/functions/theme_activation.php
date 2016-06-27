<?php

// http://old.nabble.com/Re%3A-Activation-hook-exist-for-themes--p25211038.html
if (is_admin() && isset($_GET['activated']) && $pagenow == "themes.php") {
    add_action('init', 'clrz_theme_activation');
    function clrz_theme_activation() {
        $pages_a_creer = array(
            'clrz_define_about_pageid' => array(
                'name' => 'A Propos',
            ),
            'clrz_define_contact_pageid' => array(
                'name' => 'Nous contacter',
                'template' => 'page-templates/page-contact.php'
            ),
            'clrz_define_webservice_pageid' => array(
                'name' => 'Webservice',
                'template' => 'page-templates/page-webservice.php'
            ),
            'clrz_define_mentionslegales_pageid' => array(
                'name' => 'Mentions légales',
                'content' => '
				<p>En navigant sur le site <strong>' . get_bloginfo('name') . '</strong>, vous reconnaissez avoir pris connaissance de ces mentions légales et vous engagez à les respecter.</p>

				<h3>Edition du site</h3>
				<p><strong>Directeur de la publication</strong> : [...]</p>
				<p>Le site <strong>' . get_bloginfo('name') . '</strong> est édité par [...]</p>
				<p>Société ...<br />12 rue Colorz<br />75016 Paris<br />08-36-65-65-65<br />SIREN/SIRET</p>

				<h3>Propriété intellectuelle</h3>
				<p>Tous les contenus présents, tels que les textes, graphiques, logos, images, photographies, vidéos présents sur ce site sont, sauf mention contraire, la propriété de <strong>' . get_bloginfo('name') . '</strong>.</p>

                <h3>Cookies</h3>
                <p>
                    Un ou plusieurs &quot;cookies&quot; peuvent &ecirc;tre plac&eacute;s sur le disque dur de l&#x27;ordinateur des internautes visiteurs du site afin de faciliter leur connexion au site, la gestion des comptes ou de garder en m&eacute;moire leurs r&eacute;glages.
                    Aucune corr&eacute;lation n&#x27;est faite entre ces cookies et des informations nominatives que pourrait d&eacute;tenir <strong>'.get_bloginfo('name').'.</strong>
                </p>

				<h3>Responsabilités</h3>
				<p>
					Les informations fournies sur le site <strong>' . get_bloginfo('name') . '</strong> le sont à titre indicatif.
					L\'exactitude, la complétude, l\'actualité des informations diffusées sur  <strong>' . get_bloginfo('name') . '</strong> ne sauraient être garanties.
				</p>
				<p>
					Vous êtes le seul et unique responsable de l’usage du site  <strong>' . get_bloginfo('name') . '</strong> et de ses contenus.
					Le site <strong>' . get_bloginfo('name') . '</strong> ne pourra être tenu pour responsable d\'un usage non conforme aux normes des lois en vigueur, du site internet ou de ses contenus.
				</p>
				<p>
					Ce site peut comporter des informations fournies par des sociétés externes ou des liens hypertextes vers d’autres sites qui ne sont pas gérés par  <strong>' . get_bloginfo('name') . '</strong>.<br />
					L\'existence d\'un lien depuis <strong>' . get_bloginfo('name') . '</strong> vers un autre site ne constitue pas une validation de ce site ou de son contenu.
				</p>
				<p>
				    Tout message publi&eacute;, de mani&egrave;re g&eacute;n&eacute;rale, demeure sous l&#x27;enti&egrave;re responsabilit&eacute; de son auteur.
				</p>

				<h3>Hébergement</h3>
				<p>Typhon SAS, <br />41 rue de l\'Echiquier,<br />75010 Paris,<br />France</p>

				<h3>Crédits</h3>
				<ul>
					<li><strong>Conception</strong> : <a href="http://www.colorz.fr/">Colorz</a></li>'.
					'<li><strong>Développement</strong> : <a href="http://www.colorz.fr/">Colorz</a></li>
				</ul>
				<p>Ce site utilise le <a href="http://fr.wordpress.org/">CMS WordPress</a> et le <a href="http://mootools.net/" lang="en">FrameWork MooTools</a></p>
                ',
            ),
            'clrz_define_plandusite_pageid' => array(
                'name' => 'Plan du site',
            ),
        );

        $nb_pages_brut = 0;
        $nb_pages = new WP_Query(array(
                    'posts_per_page' => 5,
                    'post_type' => 'page'
                ));


        if (isset($nb_pages->posts))
            $nb_pages_brut = count($nb_pages->posts);
        wp_reset_postdata();

        // S'il n'y a qu'une seule page
        if ($nb_pages_brut <= 1) {

            // On met à jour les options du site
            $options_default = array(
                // Config Simple
                'use_smilies' => '',
                'ping_sites' => 'http://rpc.pingomatic.com/'."\n".'http://blogsearch.google.com/ping/RPC2',
                'blacklist_keys' => "д\nи\nж\nЧ\nБ\nЏ\nЂ\nћ\nР°\nЃ",
                // Dates
                'date_format' => 'j F Y',
                'timezone_string' => 'Europe/Paris',
                'links_updated_date_format' => 'j F Y, G \h i \m\i\n',
                // Formats
                'permalink_structure' => '/%category%/%postname%/',
                // Medias
                'large_size_h' => '800',
                'large_size_w' => '800',
            );
            foreach ($options_default as $k => $v) {
                update_option($k, $v);
            }
            // Rafraichissement
            flush_rewrite_rules();

            foreach ($pages_a_creer as $clrz_id_page => $page) {
                $my_post = array(
                    'post_title' => $page['name'],
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'post_author' => 1,
                );

                if (isset($page['content']))
                    $my_post['post_content'] = $page['content'];

                // Insert the post into the database
                $id_page = wp_insert_post($my_post);
                if ($id_page != false) {
                    if (isset($page['template']))
                        update_post_meta($id_page, '_wp_page_template', $page['template']);
                    update_option($clrz_id_page, $id_page);
                }
            }
        }
    }
}