<?php /* Template Name: Home */
include dirname( __FILE__ ) . '/includes/clrz-check-in-wp.php';
get_header();
?>

<div class="fixed-menu">
    <div class="container">
        <div class="col-sm-6">
            <div class="logo">
                DaddyPack
            </div>
        </div>
        <div class="col-sm-6">
            <ul class="menu-top">
                <li>
                    <a href="#" class="button button-pink">Inscription</a>
                </li>
                <li>
                    <a href="#" class="button button-blue">Connexion</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php $urlTop =  get_field('image_head'); ?>
<div class="header-search" style="background-image: url('<?php echo $urlTop ?>');">
    <div class="container-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="logo">
                        DaddyPack
                    </div>
                </div>
                <div class="col-sm-6">
                    <ul class="menu-top">
                        <li>
                            <a href="#" class="button button-pink">Inscription</a>
                        </li>
                        <li>
                            <a href="#" class="button button-blue">Connexion</a>
                        </li>
                    </ul>
                </div>

                <div class="container-form">
                    <form class="" action="index.html" method="post">
                        <div class="title-form">Fini la chasse au <strong>colis</strong> !</div>
                        <div class="info-sup">
                            Recherche les Daddys prêt de chez vous ! Rien de plus simple. Entrez votre ville et laissez nous chercher des Daddys à votre disposition !
                        </div>
                        <input type="text" name="search" placeholder="Votre ville">
                        <input type="submit" class="button button-pink" value="Rechercher">
                        <div class="info-sup text">
                            Exemple : Paris, Marseille, Nice...
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed-barre">
    <div class="close">
        x
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-5 col-sm-offset-2">
                <div class="text">
                    Vous souhaitez tester le service ? Bénéficiez d’un mois d’essai gratuit.
                </div>
            </div>
            <div class="col-sm-3 text-right">
                <a href="#" class="button button-pink">Je m'inscris</a>
            </div>
        </div>
    </div>
</div>
<div class="container-presentation">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-2">
                <div class="text">
                    <div class="title-section">
                        <strong>Pas d’inquiétude,</strong>
                        les Daddies sont là pour vous !
                    </div>
                    <?php echo get_field('text_presentation'); ?>
                </div>
            </div>
            <div class="col-sm-4">
                <img src="<?php echo get_field('image_presentation'); ?>"  width="100%"/>
            </div>
        </div>
    </div>
</div>

<div class="container container-dash">
    <div class="row">
        <div class="col-sm-4">
            <div class="text-dash text-dash-1">
                <div class="number">
                    1
                </div>
                <div class="title">
                    Je passe commande
                </div>
                <div class="text">
                    <?php echo get_field('text_step_1') ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/dash.png" class="dash" alt="" />
        </div>
        <div class="col-sm-offset-8 col-sm-4">
            <div class="text-dash text-dash-1">
                <div class="number">
                    2
                </div>
                <div class="title">
                    Mon colis arrive chez un Daddy
                </div>
                <div class="text">
                    <?php echo get_field('text_step_2') ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/dash-2.png" class="dash" alt="" />
        </div>
        <div class="col-sm-4">
            <div class="text-dash text-dash-1">
                <div class="number">
                    3
                </div>
                <div class="title">
                    C’est le moment d’aller le chercher
                </div>
                <div class="text">
                    <?php echo get_field('text_step_3') ?>
                </div>
            </div>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/dash-3.png" class="dash" alt="" />
        </div>
    </div>
</div>

<div class="strate-blue">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-section text-center">
                    La sécurité avant tout
                </div>
            </div>
            <div class="col-sm-4 text-center col-sm-offset-2">
                <div class="title-block">
                    Nos daddies sont certifiés
                </div>
                <ul class="list">
                    <li>Vérification d’identitée</li>
                    <li>Contrat avec Daddypack</li>
                    <li>Notes et commentaires</li>
                </ul>
            </div>
            <div class="col-sm-4 text-center">
                <div class="title-block">
                    Vos colis sont assurés
                </div>
                <ul class="list">
                    <li>Contre la perte</li>
                    <li>Contre le vol</li>
                    <li>Contre la casse</li>
                </ul>
            </div>
            <div class="col-sm-12 text-center">
                <a href="#" class="button button-blue">En savoir plus</a>
            </div>
            
        </div>
    </div>
</div>

<?php
get_footer();
