/**
 * Network Content Widgets and Shortcodes Shortcake UI Javascript.
 *
 * Implements custom functionality when using Shortcake UI.
 *
 * @package WP_Network_Content_Display
 */



/**
 * Toggle the visibility of the Site Image fields.
 *
 * @since 2.0.0
 */
function wpncd_toggle_image_fields( changed, collection, shortcode ) {

	/**
	 * Get an attribute object by name.
	 *
	 * @since 2.0.0
	 *
	 * @param {String} name The name of the attribute.
	 * @return {Object} The attribute object.
	 */
	function get_attribute_by_name( name ) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get( 'attr' );
			}
		);
	}

	// set up variables
	var default_image_field = get_attribute_by_name( 'default_image' ),
		attachment_field = get_attribute_by_name( 'attachment_id' );

	// hide declared fields by default
	default_image_field.$el.hide();
	attachment_field.$el.hide();

	// if something is selected
	if ( typeof changed.value != 'undefined' ) {

		// 'library' = From Media Library
		if ( changed.value == 'library' ) {

			// show fields
			default_image_field.$el.hide();
			attachment_field.$el.show();

		}

		// 'url' = From direct URL
		if ( changed.value == 'url' ) {

			// show fields
			default_image_field.$el.show();
			attachment_field.$el.hide();

		}

	}

}

// add action for the above
wp.shortcake.hooks.addAction( 'embed_network_sites.show_icon', wpncd_toggle_image_fields );



/**
 * When a Site Image has been selected via the Attachment UI, set the Default
 * Image field to the URL of the attachment image.
 *
 * I can't decide whether this is actually a good idea or not, because the final
 * output of the widget would be better off using a custom image size rather
 * than the full size image that is grabbed here.
 *
 * The same problem exists for the Widget Form, BTW.
 *
 * @since 2.0.0
 */
function wpncd_sync_image_fields( changed, collection, shortcode ) {

	/**
	 * Get an attribute object by name.
	 *
	 * @since 2.0.0
	 *
	 * @param {String} name The name of the attribute.
	 * @return {Object} The attribute object.
	 */
	function get_attribute_by_name( name ) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get( 'attr' );
			}
		);
	}

	// set up variables
	var attachment_field = get_attribute_by_name( 'attachment_id' ),
		default_image_field = get_attribute_by_name( 'default_image' ),
		image_data;

	// if an attachment has been selected
	if ( typeof changed.value != 'undefined' ) {

		// a populated array means an attachment has been added
		if ( changed.value instanceof Array && changed.value.length ) {

			// check for spurious calls
			if ( 'undefined' != typeof attachment_field.frame.state() ) {

				// get image URL from frame
				image_data = attachment_field.frame.state().get( 'selection' ).first().toJSON();

				// sync with default_image_field
				default_image_field.model.set( 'value', image_data.url );

			}

		} else {

			// should we clear default_image field?
			default_image_field.model.set( 'value', '' );

		}

		// render changes
		default_image_field.render();

	}

}

// add action for the above
//wp.shortcake.hooks.addAction( 'embed_network_sites.attachment_id', wpncd_sync_image_fields );
