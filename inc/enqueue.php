<?php

/**
 * ANP Network Content Enqueue
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */



/**
 * Enqueue styles.
 *
 * @todo Enqueue only when there is a shortcode present.
 *
 * @since 1.0.1
 */
if ( ! function_exists( 'load_highlight_styles' ) ) {

	function load_highlight_styles() {

		wp_enqueue_style(
			'anp-network-posts',
			WP_NETWORK_CONTENT_DISPLAY_URL . 'stylesheets/css/style.min.css'
		);

	}

	add_action( 'wp_enqueue_scripts', 'load_highlight_styles', 200 );

}
