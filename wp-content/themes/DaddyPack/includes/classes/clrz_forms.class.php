<?php

/*
<form action="" method="post">
    <input type="hidden" name="clrz_form_type" value="formtest" />
    <ul>
        <li>
            <label for="form_data_test">Form Data Test</label>
            <input name="form_data[test]" id="form_data_test" type="text" placeholder="Form Data" />
        </li>
        <li>
            <button>Valider</button>
        </li>
    </ul>
</form>
*/

class CLRZContact {

    var $messages = array(
        'errors' => array(),
        'success' => array()
    );
    var $redirect_to = '/contact/?success';
    var $upload_url = '/wp-content/uploads/contact/';
    var $options = array(
        'mail_contact' => 'junk@colorz.fr',
        'upload_url' => '/wp-content/uploads/contact/',
        'table_name' => 'clrz_submit',
        'redirect_to' => '/',
        'file_types' => array( 'png', 'pdf', 'jpg', 'gif', 'doc', 'xls' ),
        'id_page_back' => 'manage-contact',
        'admin_page_back' => 'index.php'
    );

    public function __construct() {
        $this->configOptions();
        if ( is_admin() ) {
            add_action( 'admin_menu', array( &$this, 'addMenu' ) );

            add_action( 'init', array( &$this, 'downloadcsv' ) );
        }
        if ( isset( $_POST['clrz_form_type'], $_POST['form_data'] ) ) {
            $this->submit();
        }
    }

    // Configuration des options initiales
    private function configOptions() {
        global $wpdb;
        $this->upload_path = ABSPATH . '/wp-content/uploads/contact/';
        $this->options['upload_path'] = ABSPATH . $this->options['upload_url'];
        $this->options['upload_url'] = site_url() . $this->options['upload_url'];
        $this->options['table_name'] = $wpdb->prefix.$this->options['table_name'];
        $this->options['mail_contact'] = get_option( 'admin_email' );
    }

    private function setupScript() {
        global $wpdb;

        // Creation de la table
        $wpdb->query( "CREATE TABLE IF NOT EXISTS `".$this->options['table_name']."` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `type` varchar(200) DEFAULT NULL,
                `created_at` varchar(200) DEFAULT NULL,
                `remote_addr` varchar(200) DEFAULT NULL,
                `datas` text,
                PRIMARY KEY (`id`)
            );" );

        // Creation du dir d'upload
        if ( !is_dir( $this->options['upload_path'] ) ) {
            mkdir( $this->options['upload_path'] );
        }
    }

    function submit() {

        global $wpdb;
        $this->setupScript();
        $POST = array();

        // Clean POST DATAS
        foreach ( $_POST['form_data'] as $name => $value ) {
            $POST[strip_tags( $name )] = $value;
        }

        // check fields

        // On parcourt les pieces jointes
        if ( isset( $_FILES ) && !empty( $_FILES ) ) {
            $POST['attachment'] = array();
            $FILES = $this->getFiles();
            foreach ( $FILES as $k => $file ) :
                if ( $file['error'] != 4 ) {
                    $ext = substr( $file['name'], -3, 3 );
                    $filename = date( 'Y-m-d-His' ) . '.' . $ext;
                    $inc = 0;
                    while ( file_exists( $this->options['upload_path'] . $filename ) ) {
                        $filename = date( 'Y-m-d-His' ) . '-' . ( $inc++ ) . '.' . $ext;
                    }

                    if ( in_array( $ext, $this->options['file_types'] ) ) {
                        if ( move_uploaded_file( $file['tmp_name'], $this->options['upload_path'] . $filename ) ) {
                            $POST['attachment'][$k] = $this->options['upload_url'] . $filename;
                        }
                    }
                    else {
                        $this->messages['errors'][] = 'Type de fichier invalide pour '.var_export( $file, true );
                    }
                }
            endforeach;

            // Si pas de pieces jointes, pas besoin de garder une liste
            if ( empty( $POST['attachment'] ) ) unset( $POST['attachment'] );
        }



        $wpdb->query( '
                INSERT INTO '.$this->options['table_name'].'
                (type,created_at,remote_addr,datas)
                VALUES (
                    "' . mysql_real_escape_string( $_POST['clrz_form_type'] ) . '",
                    NOW(),
                    "' . mysql_real_escape_string( $_SERVER['REMOTE_ADDR'] ) . '",
                    "' . mysql_real_escape_string( serialize( $POST ) ) . '"
                )
            ' );

        $insert_id = $wpdb->insert_id;
        do_action( 'clrz_submit_forms', $_POST );

        // On envoie un mail sauf en mode debug
        if ( !WP_DEBUG ) {
            $this->sendByMail( $insert_id );
        }

        wp_redirect( $this->options['redirect_to'] . '?success=' . $_POST['clrz_form_type'] );

        die;
    }

    function getFiles() {
        $files = array();
        if ( isset( $_FILES['attachment'] ) && is_array( $_FILES['attachment'] ) ) {
            foreach ( $_FILES['attachment']['name'] as $k => $file ) {
                $files[$k] = array( 'name' => $file,
                    'type' => $_FILES['attachment']['type'][$k],
                    'tmp_name' => $_FILES['attachment']['tmp_name'][$k],
                    'error' => $_FILES['attachment']['error'][$k],
                    'size' => $_FILES['attachment']['size'][$k]
                );
            }
        }
        return apply_filters( 'clrz_contact_files', $files );
    }

    function sendByMail( $id ) {

        global $wpdb;
        add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
        $data = $wpdb->get_row( 'SELECT * FROM '.$this->options['table_name'].' WHERE id ="' . $id . '"' );

        $message = '<p style="margin:20px 0 0 0;">';
        $infos = unserialize( $data->datas );
        foreach ( $infos as $k => $v ) :
            if ( is_array( $v ) ) {
                $message.= '<strong>' . $k . '</strong> : ';
                foreach ( $v as $fn => $fl ) {
                    $message.= $fn . ' =>' . $fl . ' / ';
                }
                $message.='<br/>';
            } else {
                $message.= '<strong>' . $k . '</strong> : ' . strip_tags( (string) $v ) . '<br/>';
            }
        endforeach;
        $message .= '</p>';

        $headers = 'From: mailManager <manager@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
        $attachments = array();

        foreach ( $infos['attachment'] as $att ) {
            $attachments[] = ABSPATH . $att;
        }

        $model = 'forms';
        $mail_content = '';
        if(!empty($model) && preg_match('#^([a-z0-9_-]+)$#', $model) && file_exists(TEMPLATEPATH.'/mails/model-'.$model.'.php')) {
            ob_start();
            @include TEMPLATEPATH.'/mails/model-'.$model.'.php';
            $mail_content = ob_get_clean();
        }
        ob_start();
        @include TEMPLATEPATH.'/mails/header.php';
        echo $mail_content;
        echo $message;
        @include TEMPLATEPATH.'/mails/footer.php';
        $email_body = ob_get_clean();

        wp_mail(
            $this->options['mail_contact'],
            'Nouveau message [' . $data->type . ']',
            $email_body,
            $headers,
            $attachments
        );

    }

    function addMenu() {
        add_submenu_page( $this->options['admin_page_back'], 'Contact', 'Contact', 'manage_categories', $this->options['id_page_back'], array( &$this, 'pageManage' ) );
    }


    function downloadcsv() {

        if ( isset( $_GET['download'] ) && ( $_GET['page'] == $this->options['id_page_back'] ) && isset( $_GET['type_form'] ) ) {
            global $wpdb;
            header( "Content-type: application/csv" );
            header( "Content-Disposition: attachment; filename=file.csv" );
            header( "Pragma: no-cache" );
            header( "Expires: 0" );

            $sql_type_form = ' WHERE type="' . $_GET['type_form'] . '" ';
            $this_datas = $wpdb->get_results( 'SELECT * FROM ' . $this->options['table_name'] . ' ' . $sql_type_form . ' ORDER BY created_at DESC' );

            $data = (array) $this_datas[0];

            $infos = (array) unserialize( $data->datas );

            $line = array();

            foreach ( $data as $k => $v ) {
                if ( !is_serialized( $v ) ) {
                    $line[] = $k;
                }
            }

            foreach ( $infos as $k => $v ) {
                if ( !is_serialized( $v ) ) {
                    $line[] = $k;
                }
            }


            echo implode( ';', $line ) . "\r\n";


            foreach ( $this_datas as $data ) {
                $infos = unserialize( $data->datas );
                $type_form_clean = str_replace( array( '&gt;', '&lt;' ), '', $data->type );
                $human_time = human_time_diff( time() + ( get_option( 'gmt_offset' ) * 60 * 60 ), mysql2date( 'U', $data->created_at ) );


                $line = array();

                foreach ( (array) $data as $k => $v ) {

                    if ( !is_serialized( $v ) ) {
                        $line[] = str_replace( ';', ',', $v );
                    }
                }
                foreach ( (array) $infos as $k => $v ) {

                    if ( !is_serialized( $v ) ) {
                        $line[] = str_replace( ';', ',', $v );
                    }
                }
                echo implode( ';', $line ) . "\r\n";
            }


            die();
        }
    }

    function pageManage() {
        global $wpdb; ?>
        <style>
.block-contact-clrz {
    z-index: 1;
    position: relative;
    padding-right: 30px;
}

.block-contact-clrz .clrz-delete-contact-line {
    cursor: pointer;
    z-index: 1;
    position: absolute;
    top: 0;
    right: 3px;
    padding: 0 3px;
}

.clrz-delete-contact-line:before {
    content: "\f158";
    font-family: 'dashicons';
    font-size: 20px;
    display: inline-block;
    -webkit-font-smoothing: antialiased;
}

.download-button:before {
    content: "\f316";
    font: 400 15px/28px dashicons;
    display: inline-block;
    margin-right: 5px;
    vertical-align: top;
    -webkit-font-smoothing: antialiased;
}
        </style>
        <div class="wrap">
        <h2>Contact</h2>
        <?php

        if(isset($_POST['clrz-delete-contact-line']) && ctype_digit($_POST['clrz-delete-contact-line'])){
            global $wpdb;
            $result_delete = $wpdb->delete( $this->options['table_name'], array( 'id' => $_POST['clrz-delete-contact-line'] ) );
            if($result_delete > 0){
                echo '<div class="updated"><p>La ligne "'.$_POST['clrz-delete-contact-line'].'" a &eacute;t&eacute; supprim&eacute;e avec succ&egrave;s.</p></div>';
            }
        }

        $par_page = 150;
        $pagination = isset( $_GET['pagination'] ) && ctype_digit( $_GET['pagination'] ) ? $_GET['pagination'] : 1;
        $start_paged = ( $par_page*( $pagination-1 ) );

        $this_datas_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(id) FROM '.$this->options['table_name'].';' ) );

        $sql_type_form = isset( $_GET['type_form'] ) ? ' WHERE type="'.$_GET['type_form'].'" ' :'';

        $this_datas = $wpdb->get_results( 'SELECT * FROM '.$this->options['table_name'].' '.$sql_type_form.' ORDER BY created_at DESC LIMIT '.$start_paged.','.$par_page.'' );


        $current_type_form = isset( $_GET['type_form'] ) ? $_GET['type_form'] : 'all';


        if ( !empty( $this_datas ) ) {
            $types_forms = array();
            foreach ( $this_datas as $data ) : $infos = unserialize( $data->datas );
                $type_form = htmlentities( $data->type );
                if ( !in_array( $type_form, $types_forms ) ) {
                    $types_forms[] = $type_form;
                }
            endforeach;
            if( count( $types_forms ) < 2 ) {
                $current_type_form = $_GET['type_form'] = current( $types_forms );
            }

            echo '<div style="padding:15px 0;">
                <label for="select_type_form_clrz">Afficher les r&eacute;sultats de :</label>
                <select name="select_type_form_clrz" id="select_type_form_clrz" onchange="window.location =this.value;">';
                echo '<option '.( $current_type_form == 'all' ? 'selected="selected"':'' ).' value="'.site_url().'/wp-admin/'.$this->options['admin_page_back'].'?page='.$this->options['id_page_back'].'">'.__( 'Tous les types de formulaires', 'clrz_lang' ).'</option>';
            foreach ( $types_forms as $type_form ) {
                $type_form_clean = str_replace( array( '&gt;', '&lt;' ), '', $type_form );
                $current = ( $current_type_form  == $type_form_clean ) ? ' selected="selected"' : '';
                echo '<option '.$current.' value="'.site_url().'/wp-admin/'.$this->options['admin_page_back'].'?page='.$this->options['id_page_back'].'&amp;type_form='.$type_form_clean.'">'.__( 'Formulaire ', 'clrz_lang' ).' &quot;'.$type_form.'&quot;</option>';
            }
            echo '</select></div>';

            if ( isset( $_GET['type_form'] ) ) {
                // email
                ?>
                <div style="padding:15px 0;">
                    <label><?php echo __('Mail de contact', 'clrz_lang'); ?></label>
                    <input type="email" />
                </div>

                <?php // actions ?>
                <div style="padding:15px 0;">
                    <?php
                    echo '<a href="'.site_url().'/wp-admin/'.$this->options['admin_page_back'].'?page='.$this->options['id_page_back'].'&amp;type_form='.$type_form_clean.'&download" class="button-primary download-button" >'.__('Télécharger', 'clrz_lang').'</a>';
                    ?>
                </div>
                <?php
            }

        } ?>


            <table class="wp-list-table widefat fixed">
                <thead>
                    <th class="manage-column column-title" scope="col"><span>Type</span></th>
                    <th class="manage-column column-title" scope="col"><span>IP</span></th>
                    <th class="manage-column column-title" scope="col"><span>Date</span></th>
                </thead>
                <tbody>
                <?php


        if ( empty( $this_datas ) ) {
            echo '<tr><td align="center" colspan="3" valign="top">Aucun r&eacute;sultat pour le moment !</td></tr>';
        }
        else {
            foreach ( $this_datas as $data ) : $infos = unserialize( $data->datas );

            $type_form_clean = str_replace( array( '&gt;', '&lt;' ), '', $data->type );

            if ( $current_type_form == $type_form_clean || $current_type_form  == 'all' ) {
                $human_time = human_time_diff( time()+( get_option( 'gmt_offset' )*60*60 ), mysql2date( 'U', $data->created_at ) ); ?>
                        <tr class="hentry  ">
                            <td><?php echo htmlentities( $data->type ); ?></td>
                            <td><?php echo htmlentities( $data->remote_addr ); ?></td>
                            <td><?php echo $data->created_at; ?> (<?php echo $human_time; ?>)</td>
                        </tr>
                        <tr class="inline-edit-row inline-edit-row-post inline-edit-services quick-edit-row quick-edit-row-post inline-edit-services alternate inline-editor">
                            <td class="colspanchange" colspan="3">
                              <div class="block-contact-clrz">
                                <form action="" method="post" onSubmit="return confirm('Voulez-vous vraiment supprimer cette ligne ?');">
                                    <button type="submit" name="clrz-delete-contact-line" value="<?php echo $data->id; ?>" class="clrz-delete-contact-line button"></button>
                                </form>
                              <table style="margin:5px 0;width:100%;">
                                <tr>
                                    <?php
                $fill_infos = array_chunk( $infos,  round( sizeof( $infos ) / 2 ), true );

                foreach ( $fill_infos as $filled ) {
                    echo '<td style="border:0;padding:0;width:50%" >';
                    foreach ( $filled as $k => $v ) :
                        echo '<div style="margin-bottom:3px;">';
                        echo '<b style="white-space:nowrap;">' .htmlentities( $k ) . ' : </b>';
                        if ( is_array( $v ) ) {
                            foreach ( $v as $fn => $fl ) {
                                echo $fn . ' => ' .make_clickable( $fl ) . ' / ';
                            }
                            echo '<br/>';
                        } else {
                            echo strip_tags( (string) $v ) . '<br/>';
                        }
                        echo '</div>';
                    endforeach;
                    echo '</td>';
                }
?>
                                </tr>
                              </table>
                              </div>
                            </td>
                        </tr>
                      <?php
            }
            endforeach;
        }
?>
            </tbody>
            </table>

            <?php if ( !empty( $this_datas ) ) {
            // Pagination
        } ?>

          </div>
          <?php
    }

    function getDatas() {
        global $wpdb;
        return $wpdb->get_results( 'SELECT * FROM '.$this->options['table_name'].' ORDER BY created_at DESC' );
    }

}

global $clrz_contact;
$clrz_contact = new CLRZContact();
