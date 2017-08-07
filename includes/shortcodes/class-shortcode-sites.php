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
		add_action( 'register_shortcode_ui', array( $this, 'shortcake' ) );
		add_action( 'enqueue_shortcode_ui', array( $this, 'shortcake_scripts' ) );

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

		return wp_network_content_display()->components->sites->get_network_sites( $attr );

	}



	/**
	 * Add compatibility with Shortcake UI.
	 *
	 * Be aware that in Shortcode UI < 0.7.2, values chosen via selects do not
	 * "stick". At present, you will need the master branch of Shortcode UI from
	 * GitHub, which has solved this problem.
	 *
	 * @see https://github.com/wp-shortcake/shortcake/issues/747
	 *
	 * Furthermore, multi-selects do not function at all.
	 *
	 * @see https://github.com/wp-shortcake/shortcake/issues/757
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
				'label' => __( 'Network Sites', 'wp-network-content-display' ),

				// icon
				'listItemImage' => 'dashicons-admin-multisite',

				// window elements
				'attrs' => array(

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

					// number of sites
					array(
						'label' => __( 'Number of Sites', 'wp-network-content-display' ),
						'description' => __( 'Please enter the maximum number of sites to show.', 'wp-network-content-display' ),
						'attr' => 'number_sites',
						'type' => 'number',
						'value' => '20',
					),

					// exclude sites
					array(
						'label' => __( 'Exclude Sites', 'wp-network-content-display' ),
						'description' => __( 'Please choose which sites to exclude.', 'wp-network-content-display' ),
						'attr' => 'exclude_sites',
						'type' => 'select',
						'options' => $this->shortcake_select_sites(),
						'meta' => array(
							'multiple' => true,
						),
					),

					// sort by
					array(
						'label' => __( 'Sort By', 'wp-network-content-display' ),
						'description' => __( 'Please select how you want the sites sorted.', 'wp-network-content-display' ),
						'attr'  => 'sort_by',
						'type'  => 'select',
						'options' => array(
							array( 'value' => 'blogname', 'label' => __( 'Alphabetical', 'wp-network-content-display' ) ),
							array( 'value' => 'last_updated', 'label' => __( 'Recently Active', 'wp-network-content-display' ) ),
							array( 'value' => 'post_count', 'label' => __( 'Most Active', 'wp-network-content-display' ) ),
							array( 'value' => 'registered', 'label' => __( 'Newest', 'wp-network-content-display' ) ),
						),
						'value' => 'blogname',
					),

					// show site header image
					array(
						'label' => __( 'Show Site Image', 'wp-network-content-display' ),
						'description' => __( 'Choose whether to show Site Images and (if so) from which source.', 'wp-network-content-display' ),
						'attr' => 'show_image',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '', 'label' => __( 'No Site Image', 'wp-network-content-display' ) ),
							array( 'value' => '1', 'label' => __( 'From Media Library', 'wp-network-content-display' ) ),
							array( 'value' => '2', 'label' => __( 'From URL', 'wp-network-content-display' ) ),
						),
						'value' => '',
					),

					// default header image URL
					array(
						'label' => __( 'Default Site Image URL', 'wp-network-content-display' ),
						'description' => __( 'Please enter the URL of the image.', 'wp-network-content-display' ),
						'attr' => 'default_image',
						'type' => 'text',
					),

					// default header image ID
					array(
						'label' => __( 'Default Site Image', 'wp-network-content-display' ),
						'description' => __( 'Please choose an image from the Media Library.', 'wp-network-content-display' ),
						'attr' => 'attachment_id',
						'type' => 'attachment',
						'libraryType' => array( 'image' ),
						'addButton' => __( 'Choose an image', 'wp-network-content-display' ),
						'frameTitle' => __( 'Use this image', 'wp-network-content-display' ),
					),

					// show meta
					array(
						'label' => __( 'Show Site Metadata', 'wp-network-content-display' ),
						'description' => __( 'Please select if you want to display additional information for each site.', 'wp-network-content-display' ),
						'attr' => 'show_meta',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '1', 'label' => __( 'Yes', 'wp-network-content-display' ) ),
							array( 'value' => '', 'label' => __( 'No', 'wp-network-content-display' ) ),
						),
						'value' => '',
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

		// init query args
		$site_args = array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		);

		/**
		 * Apply plugin-wide $site_args filter.
		 *
		 * @since 2.0.0
		 *
		 * @param array $site_args The arguments used to query the sites.
		 */
		$site_args = apply_filters( 'wpncd_filter_site_args', $site_args );

		/**
		 * Allow the $site_args to be specifically filtered here.
		 *
		 * @since 2.0.0
		 *
		 * @param array $site_args The arguments used to query the sites.
		 */
		$site_args = apply_filters( 'wpncd_shortcake_select_sites_for_sites_args', $site_args );

		// get sites
		$sites = get_sites( $site_args );

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
	 * Enqueue Javascript for custom functionality in Shortcake.
	 *
	 * @since 2.0.0
	 */
	public function shortcake_scripts() {

		wp_enqueue_script(
			'wpncd-shortcode-ui',
			WP_NETWORK_CONTENT_DISPLAY_URL . '/assets/js/shortcake-ui.js',
			array( 'shortcode-ui' ),
			WP_NETWORK_CONTENT_DISPLAY_VERSION
		);

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
		$mce_css .= ', ' . WP_NETWORK_CONTENT_DISPLAY_URL . 'assets/css/shortcake-sites.css';

		// --<
		return $mce_css;

	}



} // end class WP_Network_Content_Display_Sites_Shortcode
