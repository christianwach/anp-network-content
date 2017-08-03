<?php

/**
 * Network Content Asset Enqueuing.
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   WP_Network_Content_Display
 */



/**
 * Enqueue styles.
 *
 * @todo Enqueue only when there is a shortcode present.
 *
 * @since 1.0.1
 */
if ( ! function_exists( 'glocal_load_highlight_styles' ) ) {

	function glocal_load_highlight_styles() {

		wp_enqueue_style(
			'glocal-network-posts',
			WP_NETWORK_CONTENT_DISPLAY_URL . 'stylesheets/css/style.min.css'
		);

	}

	add_action( 'wp_enqueue_scripts', 'glocal_load_highlight_styles', 200 );

}
