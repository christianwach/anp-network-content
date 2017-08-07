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

		return wp_network_content_display()->components->events->get_posts_from_network( $attr );

	}



	/**
	 * Add compatibility with Shortcake UI.
	 *
	 * @since 2.0.0
	 */
	public function shortcake() {

		// let's be extra-safe and bail if not present
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) return;

		// add styles for TinyMCE editor
		add_filter( 'mce_css', array( $this, 'shortcake_styles' ) );

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
						'label' => __( 'Number of Events', 'wp-network-content-display' ),
						'description' => __( 'Please enter the maximum number of Events to show.', 'wp-network-content-display' ),
						'attr' => 'number_posts',
						'type' => 'number',
						'value' => '10',
					),

					// exclude sites
					array(
						'label' => __( 'Exclude Sites', 'wp-network-content-display' ),
						'description' => __( 'Please choose which Sites to exclude.', 'wp-network-content-display' ),
						'attr' => 'exclude_sites',
						'type' => 'select',
						'options' => $this->shortcake_select_sites(),
						'meta' => array(
							'multiple' => true,
						),
					),

					// include categories
					array(
						'label' => __( 'Include Categories', 'wp-network-content-display' ),
						'description' => __( 'Please choose which categories to include Events from.', 'wp-network-content-display' ),
						'attr' => 'include_categories',
						'type' => 'select',
						'options' => $this->shortcake_select_categories(),
						'meta' => array(
							'multiple' => true,
						),
					),

					// include tags
					array(
						'label' => __( 'Include Tags', 'wp-network-content-display' ),
						'description' => __( 'Please choose which tags to include Events from.', 'wp-network-content-display' ),
						'attr' => 'include_tags',
						'type' => 'select',
						'options' => $this->shortcake_select_tags(),
						'meta' => array(
							'multiple' => true,
						),
					),

					// event scope
					array(
						'label' => __( 'Event Scope', 'wp-network-content-display' ),
						'description' => __( 'Please the time period from which to show Events.', 'wp-network-content-display' ),
						'attr' => 'event_scope',
						'type' => 'select',
						'options' => array(
							'future' => __( 'Future', 'wp-network-content-display' ),
							'past' => __( 'Past', 'wp-network-content-display' ),
							'all' => __( 'All', 'wp-network-content-display' ),
						),
						'value' => 'future',
					),

					// display style
					array(
						'label' => __( 'Display Style', 'wp-network-content-display' ),
						'description' => __( 'Please select a Display Style.', 'wp-network-content-display' ),
						'attr'  => 'style',
						'type'  => 'select',
						'options' => array(
							array( 'value' => '', 'label' => __( 'List', 'wp-network-content-display' ) ),
							array( 'value' => 'block', 'label' => __( 'Block', 'wp-network-content-display' ) ),
						),
						'value' => 'list',
					),

					// show meta
					array(
						'label' => __( 'Show Post Metadata', 'wp-network-content-display' ),
						'description' => __( 'Please choose if you want to display additional information for each Event.', 'wp-network-content-display' ),
						'attr' => 'show_meta',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '1', 'label' => __( 'Yes', 'wp-network-content-display' ) ),
							array( 'value' => '', 'label' => __( 'No', 'wp-network-content-display' ) ),
						),
						'value' => '1',
					),

					// show thumbnail
					array(
						'label' => __( 'Show Thumbnail', 'wp-network-content-display' ),
						'description' => __( 'Please choose if you want to show Event feature images.', 'wp-network-content-display' ),
						'attr' => 'show_thumbnail',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '1', 'label' => __( 'Yes', 'wp-network-content-display' ) ),
							array( 'value' => '', 'label' => __( 'No', 'wp-network-content-display' ) ),
						),
						'value' => '',
					),

					// show excerpt
					array(
						'label' => __( 'Show Excerpt', 'wp-network-content-display' ),
						'description' => __( 'Please choose if you want to show Event excerpts.', 'wp-network-content-display' ),
						'attr' => 'show_excerpt',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '1', 'label' => __( 'Yes', 'wp-network-content-display' ) ),
							array( 'value' => '', 'label' => __( 'No', 'wp-network-content-display' ) ),
						),
						'value' => '1',
					),

					// excerpt length
					array(
						'label' => __( 'Excerpt Length', 'wp-network-content-display' ),
						'description' => __( 'Please enter the maximum number of words in the Event excerpt.', 'wp-network-content-display' ),
						'attr' => 'excerpt_length',
						'type' => 'number',
						'value' => '20',
					),

					// show site name
					array(
						'label' => __( 'Show Site Name', 'wp-network-content-display' ),
						'description' => __( 'Please choose if you want to show the site that an Event is published in.', 'wp-network-content-display' ),
						'attr' => 'show_site_name',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '1', 'label' => __( 'Yes', 'wp-network-content-display' ) ),
							array( 'value' => '', 'label' => __( 'No', 'wp-network-content-display' ) ),
						),
						'value' => '1',
					),

				),

			)

		);

	}



	/**
	 * Get options array for Exclude Sites multi-select.
	 *
	 * @since 2.0.0
	 *
	 * @return array $options The properly formatted array for the select
	 */
	public function shortcake_select_sites() {

		// init return
		$options = array(
			array( 'value' => '', 'label' => __( 'None', 'wp-network-content-display' ) ),
		);

		// get sites
		$sites = get_sites( array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		) );

		// add data for each site
		foreach( $sites AS $site ) {
			$options[] = array(
				'value' => $site->blog_id,
				'label' => esc_html( get_blog_details( $site->blog_id )->blogname ),
			);
		}

		// --<
		return $options;

	}



	/**
	 * Get options array for Include Categories multi-select.
	 *
	 * @since 2.0.0
	 *
	 * @return array $options The properly formatted array for the select
	 */
	public function shortcake_select_categories() {

		// init return
		$options = array(
			array( 'value' => '', 'label' => __( 'All', 'wp-network-content-display' ) ),
		);

		// get terms
		$terms = wp_network_content_display()->components->events->get_network_terms( 'event-category' );

		// add data for each
		foreach( $terms AS $slug => $name ) {
			$options[] = array(
				'value' => $slug,
				'label' => esc_html( $name ),
			);
		}

		// --<
		return $options;

	}



	/**
	 * Get options array for Include Tags multi-select.
	 *
	 * @since 2.0.0
	 *
	 * @return array $options The properly formatted array for the select
	 */
	public function shortcake_select_tags() {

		// init return
		$options = array(
			array( 'value' => '', 'label' => __( 'All', 'wp-network-content-display' ) ),
		);

		// get terms
		$terms = wp_network_content_display()->components->events->get_network_terms( 'event-tag' );

		// add data for each
		foreach( $terms AS $slug => $name ) {
			$options[] = array(
				'value' => $slug,
				'label' => esc_html( $name ),
			);
		}

		// --<
		return $options;

	}



	/**
	 * Add stylesheet to TinyMCE when Shortcake is active.
	 *
	 * @since 2.0.0
	 *
	 * @param str $mce_css The existing list of stylesheets that TinyMCE will load
	 * @return str $mce_css The modified list of stylesheets that TinyMCE will load
	 */
	public function shortcake_styles( $mce_css ) {

		// add our styles to TinyMCE
		$mce_css .= ', ' . WP_NETWORK_CONTENT_DISPLAY_URL . 'assets/css/shortcake-events.css';

		// --<
		return $mce_css;

	}



} // end class WP_Network_Content_Display_Events_Shortcode
