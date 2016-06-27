<?php /* Template Name: Formulaire de contact */
include dirname(__FILE__) . '/../includes/clrz-check-in-wp.php';
// On définit les champs à tester
$fields = array(
    'clrz_contact_name' => array(
        'valeur' => '',
        'typehtml' => 'text',
        'nom' => __( 'Votre nom', 'clrz_lang' ),
        'label' => __( 'Nom', 'clrz_lang' ),
        'test' => 'nonvide'
    ),
    'clrz_contact_email' => array(
        'valeur' => '',
        'typehtml' => 'email',
        'nom' => __( 'Votre adresse e-mail', 'clrz_lang' ),
        'label' => __( 'Email', 'clrz_lang' ),
        'test' => 'email'
    ),
    'clrz_contact_message' => array(
        'valeur' => '',
        'typehtml' => 'textarea',
        'nom' => __( 'Contenu de votre message', 'clrz_lang' ),
        'label' => __( 'Message', 'clrz_lang' ),
        'test' => 'nonvide'
    ),
);

function success_form( &$fields, $aposter ) {
    $mail_retour = '<p>'.__( 'Bonjour, <br />Vous avez re&ccedil;u un message sur le site', 'clrz_lang' ).' &quot;'.htmlentities( get_bloginfo( 'name' ) ).'&quot;.</p>';
    foreach ( $fields as $post_id => &$field ) {
        $mail_retour .= '<p style="padding-bottom: 5px;border-bottom:1px solid #f0f0f0;margin:0 0 10px;"><strong>'.$field['nom'].'</strong><br />'.$field['valeur'].'</p>';
        $field['valeur']='';
    }
    clrz_send_mail(
        get_option( 'client_mail_contact' ),
        '['.get_bloginfo('name').'] '.__( 'Nouveau message du formulaire de contact', 'clrz_lang' ),
        $mail_retour);
}
$message_ok = '<p><strong>'.__( 'Merci de votre message !', 'clrz_lang' ).'</strong></p>';
include TEMPLATEPATH.'/includes/modules/test_champs.php';

get_header();
?>
    <div class="cssn-lay">
        <div class="col-main" id="content">
            <?php
if ( have_posts () ) : while ( have_posts () ) : the_post(); ?>
            <header class="headercontent">
                <h2><?php the_title(); ?></h2>
            </header>
            <article class="post post-single">
                <div class="post-content">
                    <?php the_content(); ?>
                    <?php echo $message_retour; ?>
                </div>
            </article>
            <?php endwhile; endif; ?>
            <form id="formcontact" action="" method="post">
                <ul class="cssc-form float-form">
                    <?php foreach ( $fields as $idf => $le_field ) :
                    if ( isset( $le_field['specialbefore'] ) ) {echo $le_field['specialbefore'];}
                        $common_attr = ' required name="'.$idf.'" id="'.$idf.'"  class="fake-placeholder-me" placeholder="'.$le_field['nom'].'" '; ?>
                        <li class="box">
                            <label for="<?php echo $idf; ?>"><?php echo $le_field['label']; ?></label>
                            <?php if ( $le_field['typehtml'] == 'textarea' ) { ?>
                                <textarea <?php echo $common_attr; ?> rows="3" cols="45"><?php echo $le_field['valeur']; ?></textarea>
                            <?php } else { ?>
                                <input <?php echo $common_attr; ?> type="<?php echo $le_field['typehtml']; ?>" value="<?php echo $le_field['valeur']; ?>" />
                            <?php } ?>
                        </li>
                    <?php
                    if ( isset( $le_field['specialafter'] ) ) {echo $le_field['specialafter'];}
                    endforeach;
                    ?>
                    <li class="box submit-box">
                        <button type="submit" class="cssc-button"><?php echo __( 'Envoyer votre message', 'clrz_lang' ); ?></button>
                    </li>
                </ul>
            </form>
            <script>
                if($('formcontact') && $('submitcontact')){
                    tmp_time = new Date().getTime();
                    $('formcontact').set('action','<?php echo get_permalink( 'CONTACT_PAGEID' ) ?>?t='+tmp_time);
                    $('formcontact').addEvent('submit',function(e){
                        $('submitcontact').set('value','Envoi en cours').disabled = 1;
                    });
                }
            </script>
        </div>
        <div class="col-side">
            <?php get_sidebar(); ?>
        </div>
    </div>

<?php
get_footer();
