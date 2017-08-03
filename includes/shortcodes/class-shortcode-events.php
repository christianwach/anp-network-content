<?php

/**
 * Network Events Shortcode Class.
 *
 * A class that encapsulates functionality for this shortcode.
 *
 * @since 2.0.0
 *
 * @package WP_Network_Content_Display
 */
class WP_Network_Content_Display_Events_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var str $shortcode_name The name of the shortcode tag.
	 */
	public $shortcode_name = 'embed_network_events';



	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// bail if Event Organiser plugin is not present and active
		if ( ! defined( 'EVENT_ORGANISER_VER' ) ) return;

		// register shortcode
		add_action( 'init', array( $this, 'shortcode_register' ) );

		// Shortcake compat
		add_action( 'register_shortcode_ui', array( $this, 'shortcake' ) );

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

		// enforce post type
		$attr['post_type'] = 'event';

		if( function_exists( 'glocal_networkwide_posts_module' ) ) {
			return glocal_networkwide_posts_module( $attr );
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
				'label' => __( 'Network Events', 'wp-network-content-display' ),

				// icon
				'listItemImage' => 'dashicons-calendar-alt',

				// window elements
				'attrs' => array(

					// post count
					array(
						'label' => __( 'Number of Posts', 'wp-network-content-display' ),
						'attr' => 'number_posts',
						'type' => 'number',
					),

					// event scope
					array(
						'label' => __( 'Event Scope', 'wp-network-content-display' ),
						'attr' => 'event_scope',
						'type' => 'select',
						'options' => array(
							'future' => __( 'Future', 'wp-network-content-display' ),
							'past' => __( 'Past', 'wp-network-content-display' ),
							'all' => __( 'All', 'wp-network-content-display' ),
						),
					),

				),

			)

		);

	}



} // end class WP_Network_Content_Display_Events_Shortcode
