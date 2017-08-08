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
	 * Common function to toggle the visibility of Default Image fields.
	 *
	 * @since 2.0.0
	 *
	 * @param {Object} select The jQuery select object that controls the toggle.
	 */
	function wpncd_toggle_fields( select ) {

		var value = select.val(), default_image_field, attachment_field;

		// get Default Image and Attachment ID containers
		default_image_field = select.parent().siblings( '.default_image_container' )[0];
		attachment_field = select.parent().siblings( '.attachment_id_container' )[0];

		// 'none' = Hide them
		if ( value == 'none' ) {
			$(default_image_field).hide();
			$(attachment_field).hide();
		}

		// 'library' = From Media Library
		if ( value == 'library' ) {
			$(default_image_field).hide();
			$(attachment_field).show();
		}

		// 'url' = From direct URL
		if ( value == 'url' ) {
			$(default_image_field).show();
			$(attachment_field).hide();
		}

	}

	/**
	 * Set up widget forms on document ready.
	 *
	 * @since 2.0.0
	 */
	$('.show_icon_container select').each( function(i) {
		var select = $(this);
		wpncd_toggle_fields( select );
	});

	/**
	 * Register changes on "Show Site Icon" dropdown.
	 *
	 * @since 2.0.0
	 */
	$(document).on( 'change', '.show_icon_container select', function (e) {
		var select = $(this);
		wpncd_toggle_fields( select );
	});

	/**
	 * Register callback for widget events.
	 *
	 * @since 2.0.0
	 *
	 * @param {Object} event The event details.
	 * @param {Object} widget The new or updated widget.
	 */
	$(document).on( 'widget-updated widget-added', function( event, widget ) {

		var widget_id = $(widget).attr('id'), select;

		// if it's one of our widgets
		if ( widget_id.match( 'wpncd-network-sites' ) ) {
			select = $(widget).find('.show_icon_container select');
			wpncd_toggle_fields( select );
		}

	});

	/**
	 * Register callback for clicks on "Choose an image" buttons.
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

			// assign Attachment ID to subsequent input field
			button.next( 'input' ).val( image_data.id );

		});

		// open the modal
		media_modal.open();

	});

});
