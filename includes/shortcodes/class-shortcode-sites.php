<?php

/**
 * Network Sites Shortcode Class.
 *
 * A class that encapsulates functionality for this shortcode.
 *
 * @since 2.0.0
 *
 * @package WP_Network_Content_Display
 */
class WP_Network_Content_Display_Sites_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var str $shortcode_name The name of the shortcode tag.
	 */
	public $shortcode_name = 'embed_network_sites';



	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// register shortcode
		add_action( 'init', array( $this, 'shortcode_register' ) );

		// Shortcake compat
		//add_action( 'register_shortcode_ui', array( $this, 'shortcake' ) );

	}



	/**
	 * Register our shortcode.
	 *
	 * @since 2.0.0
	 */
	public function shortcode_register() {

		// create shortcode
		add_shortcode( $this->shortcode_name, array( $this, 'shortcode_render' ) );

	}



	/**
	 * Render the shortcode output.
	 *
	 * @since 2.0.0
	 *
	 * @param array $attr The saved shortcode attributes
	 * @param str $content The enclosed content of the shortcode
	 */
	public function shortcode_render( $attr, $content = null ) {

		// Attributes
		extract( shortcode_atts( array(
		), $attr ) );

		if ( function_exists( 'glocal_networkwide_sites_module' ) ) {
			return glocal_networkwide_sites_module( $attr );
		}

	}



	/**
	 * Add compatibility with Shortcake UI.
	 *
	 * @since 2.0.0
	 */
	public function shortcake() {

		// let's be extra-safe and bail if not present
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) return;

		// register this shortcode
		shortcode_ui_register_for_shortcode(

			// shortcode name
			$this->shortcode_name,

			// settings
			array(

				// window title
				'label' => __( 'Network Sites', 'wp-network-content-display' ),

				// icon
				'listItemImage' => 'dashicons-admin-post',

				// window elements
				'attrs' => array(

				),

			)

		);

	}



} // end class WP_Network_Content_Display_Sites_Shortcode
