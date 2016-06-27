<?php
// Id => Legend
$clrz_groupes_options = array(
//    'donnees_site' => 'Donnees du site',
//    'donnees_lieu' => 'Donnees de lieu',
);


/*
 * Paramètres
 * ----------
 * clef du tableau : id du champ dans la BDD
 *
 *              * Champs obligatoires
 * label :      * Label du champ, valeur affichée
 * typehtml :   * Type de champ parmi text, number, textarea, email, image, file, select, page, editor, media
 * typetest :   * Type de test de validité parmi email, number, url, image, file, select, simpletext
 *                  email : invalide si non email
 *                  number : invalide si non numérique
 *                  url : invalide si non url
 *                  select : invalide si option value pas dans les datas associées
 *                  simpletext : supprime les balises html, ne fait aucun test en plus.
 *                  defaut : aucun test.
 * defaut :     * Valeur par défaut à la première initialisation du champ
 * group :      * Groupe parmi les groupes dans $clrz_groupes_options
 * niveau :     Si indiqué, doit être non vide. Valeur = niveau minimal pour éditer ce champ
 * datas :      Valeurs proposées dans un select, * Obligatoire si champ type select
 * sizex :      Largeur obligatoire de l'image
 * sizey :      Hauteur obligatoire de l'image
 * lang :       Si indiqué, le champ devient multilingue
 *
 */

// Datas pour un <select>
$datas = array(
    'cle1'=>'Valeur 1',
    'cle2'=>'Valeur 2',
    'cle3'=>'Valeur 3',
    'cle4'=>'Valeur 4',
);


$clrz_champs_options_clients = array(
//    'clrz_options_code_google_analytics' => array( 'label' => 'Code Google Analytics', 'typehtml' =>'text','typetest' =>'text', 'defaut' => 'UA-XXXXX-X', 'group' => 'donnees_site','niveau'=>'upload_files'),
//    'clrz_google_site_verification' => array( 'label' => 'Code Google Site Verification', 'typehtml' =>'text','typetest' =>'text', 'defaut' => '', 'group' => 'donnees_site'),
//    'clrz_options_adresse' => array( 'label' => 'Adresse postale', 'typehtml' =>'textarea', 'typetest' =>'text', 'defaut' => '3 rue Titon', 'group' => 'donnees_lieu'),
//    'clrz_options_code_postal' => array( 'label' => 'Code postal', 'typehtml' =>'number', 'typetest' =>'number', 'defaut' => '75011', 'group' => 'donnees_lieu'),
//    'clrz_options_ville' => array( 'label' => 'Ville', 'typehtml' =>'text', 'typetest' =>'text', 'defaut' => 'Paris', 'group' => 'donnees_lieu'),
//    'clrz_options_adresse_mail' => array( 'label' => 'Adresse Mail', 'typehtml' =>'email', 'typetest' =>'email', 'defaut' => 'contact@email.com', 'group' => 'donnees_lieu'),
//    'clrz_options_numero_tel' => array( 'label' => 'Numero de telephone', 'typehtml' =>'text', 'typetest' =>'tel', 'defaut' => '01 23 45 67 89', 'group' => 'donnees_lieu'),
//    'clrz_test_image_contrainte' => array( 'label' => 'Image', 'typehtml' =>'image', 'typetest' =>'image', 'defaut' => '', 'sizex'=>610, 'sizey'=>170, 'group' => 'donnees_site'),
//    'clrz_test_img' => array( 'label' => 'Image', 'typehtml' =>'image', 'typetest' =>'image', 'defaut' => '', 'group' => 'donnees_site'),
//    'clrz_test_fichier' => array( 'label' => 'Fichier', 'typehtml' =>'file', 'typetest' =>'file', 'defaut' => '', 'group' => 'donnees_site'),
//    'clrz_options_list' => array( 'label' => 'Liste', 'typehtml' =>'select','datas'=>$datas, 'typetest' =>'number', 'defaut' => '0', 'group' => 'donnees_lieu'),
    );
