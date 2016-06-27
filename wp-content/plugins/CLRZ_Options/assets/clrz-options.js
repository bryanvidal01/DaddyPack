var clrz_options_form = [],
    clrz_options_fieldsets = [];

jQuery(document).ready(function($) {
    clrz_options_form_accordion();
    wputh_options_set_media();

});

function clrz_options_form_accordion() {
    clrz_options_form = jQuery('#clrz_options_client');
    clrz_options_fieldsets = clrz_options_form.find('.clrz-options-fieldset');

    /* On each fieldset */
    clrz_options_fieldsets.each(function() {
        var legend = jQuery(this).find('.legend');

        // Add event on legend
        legend.on('click', function() {
            document.location.hash = jQuery(this).attr('id');
            clrz_options_fieldsets.addClass('is-closed');
            jQuery(this).parent().removeClass('is-closed');
        });
    });
    if(document.location.hash && jQuery('.legend'+document.location.hash)) {
        jQuery('.legend'+document.location.hash).trigger('click');
    } else {
        clrz_options_fieldsets.eq(0).find('.legend').trigger('click');
    }

}

/* ----------------------------------------------------------
  Upload files
---------------------------------------------------------- */
var wpuopt_file_frame,
    wpuopt_datafor;

var wputh_options_set_media = function() {
    jQuery('#clrz_options_client').on('click', '.wpuoptions_add_media', function(event) {
        event.preventDefault();
        var $this = jQuery(this);

        wpuopt_datafor = $this.data('for');

        // If the media frame already exists, reopen it.
        if (wpuopt_file_frame) {
            wpuopt_file_frame.open();
            return;
        }

        // Create the media frame.
        wpuopt_file_frame = wp.media.frames.wpuopt_file_frame = wp.media({
            title: $this.data('uploader_title'),
            button: {
                text: $this.data('uploader_button_text'),
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        wpuopt_file_frame.on('select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = wpuopt_file_frame.state().get('selection').first().toJSON();

            // Set attachment ID
            jQuery('#' + wpuopt_datafor).attr('value', attachment.id);

            // Set preview image
            jQuery('#preview-' + wpuopt_datafor).html('<img class="wpu-options-upload-preview" src="' + attachment.url + '" />');

        });

        // Finally, open the modal
        wpuopt_file_frame.open();
    });
};