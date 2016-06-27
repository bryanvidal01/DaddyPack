<?php

// Fonctions lancées à l'activation du thème
include(TEMPLATEPATH.'/includes/functions/theme_activation.php');
// Classes de formulaires
include(TEMPLATEPATH.'/includes/classes/clrz_forms.class.php');
// Post Similaires
include(TEMPLATEPATH.'/includes/classes/clrz_similar.php');
// Import d'une image dans un post
include(TEMPLATEPATH.'/includes/classes/clrz_import_file.php');
// Imports Twitter & Facebook
include(TEMPLATEPATH.'/includes/functions/importerz.php');

// Empeche certaines fonctions de WordPress
include(TEMPLATEPATH.'/includes/functions/_reset.php');
// Empeche certaines actions aux clients ou aux visiteurs
include(TEMPLATEPATH.'/includes/functions/_protect.php');
// Ameliore certaines fonctions de WordPress (Admin)
include(TEMPLATEPATH.'/includes/functions/_enhance_admin.php');
// Actions diverses
include(TEMPLATEPATH.'/includes/functions/actions.php');
// Functions diverses
include(TEMPLATEPATH.'/includes/functions/functions.php');
// Filters divers
include(TEMPLATEPATH.'/includes/functions/filters.php');
// Shortcodes
include(TEMPLATEPATH.'/includes/functions/shortcodes.php');
// Boutons TinyMCE
include(TEMPLATEPATH.'/includes/functions/tinymce.php');
// Widgets personnalisés
include(TEMPLATEPATH.'/includes/functions/widgets.php');
// Lancement des paramètres
include(TEMPLATEPATH.'/includes/functions/params.php');


/* --------------------------------------------------------------------
   Params
   ----------------------------------------------------------------- */

// Rajoute des post types
include(TEMPLATEPATH.'/includes/params/posttypes.php');
// Rajoute des custom taxonomies
include(TEMPLATEPATH.'/includes/params/customtaxonomies.php');
// Register Sidebars
include(TEMPLATEPATH.'/includes/params/sidebars.php');
// Register Javascript
include(TEMPLATEPATH.'/includes/params/javascript.php');
// Register CSS
include(TEMPLATEPATH.'/includes/assets/styles.php');

// Plugins
include(TEMPLATEPATH.'/includes/params/clrz_options.php');
include(TEMPLATEPATH.'/includes/params/thumbtheme.php');


include(TEMPLATEPATH.'/includes/modules/exporteur_options.php');