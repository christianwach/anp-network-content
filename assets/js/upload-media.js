/**
 * Network Content Widgets and Shortcodes Widget Form Javascript.
 *
 * Implements custom functionality when using Widget Forms.
 *
 * @package WP_Network_Content_Display
 */



/**
 * Do stuff on document ready.
 *
 * @since 2.0.0
 */
jQuery(document).ready( function($) {

	var wpncd_modal_title = '',
		wpncd_modal_submit = '';

	// grab localisation data
	if ( 'undefined' !== typeof WP_Network_Content_Display_Settings ) {
		wpncd_modal_title = WP_Network_Content_Display_Settings.modal_title;
		wpncd_modal_submit = WP_Network_Content_Display_Settings.modal_submit;
	}

	/**
	 * Register clicks on "Choose an image" buttons.
	 *
	 * @since 2.0.0
	 */
	$(document).on( 'click', '.upload_image_button', function (e) {

		var button = $(this),
			media_modal;

		e.preventDefault();

		// init WordPress Media modal
		media_modal = wp.media({
			title: wpncd_modal_title,
			multiple: false,
			button: {
				text: wpncd_modal_submit
			}
		});

		/**
		 * Register callback for Media Dialog "select" trigger.
		 *
		 * @since 2.0.0
		 */
		media_modal.on( 'select', function(e) {

			var image_object, image_data;

			// get selected image object from the Media Uploader
			image_object = media_modal.state().get( 'selection' ).first();

			// convert to JSON object
			image_data = image_object.toJSON();

			// assign URL to the previous input field
			button.prev( 'input' ).val( image_data.url );

			// assign Attachment ID to subsequent input field
			button.next( 'input' ).val( image_data.id );

		});

		// open the modal
		media_modal.open();

	});

});
