<?php

/**
 * Network Content Widgets and Shortcodes Sites Class.
 *
 * A class that encapsulates Posts functionality.
 *
 * @since 2.0.0
 */
class WP_Network_Content_Display_Sites {

	/**
	 * Shortcode object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $shortcode The Shortcode object
	 */
	public $shortcode;



	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// include files
		$this->include_files();

		// set up objects and references
		$this->setup_objects();

		// add widgets
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

	}



	/**
	 * Include files.
	 *
	 * @since 2.0.0
	 */
	public function include_files() {

		// include Shortcode class file
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/shortcodes/class-shortcode-sites.php' );

	}



	/**
	 * Set up this component's objects and references.
	 *
	 * @since 2.0.0
	 */
	public function setup_objects() {

		// only do this once
		static $done;
		if ( isset( $done ) AND $done === true ) return;

		// instantiate Shortcode class
		$this->shortcode = new WP_Network_Content_Display_Sites_Shortcode;

		// we're done
		$done = true;

	}



	/**
	 * Register widgets for this component.
	 *
	 * @since 2.0.0
	 */
	public function register_widgets() {

		// register Network Sites Widget
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/class-widget-sites.php' );
		register_widget( 'WP_Network_Content_Display_Sites_Widget' );

	}



	/**
	 * NETWORK SITES MAIN FUNCTION.
	 *
	 * Gets (or renders) a list of sites.
	 *
	 * @param array $parameters An array of settings with the following options:
	 *    return - Return ( display list of sites or return array of sites ) ( default: display )
	 *    number_sites - Number of sites to display/return ( default: no limit )
	 *    exclude_sites - ID of sites to exclude ( default: 1 ( usually, the main site ) )
	 *    sort_by - newest, updated, active, alpha ( registered, last_updated, post_count, blogname ) ( default: alpha )
	 *    default_image - Default image to display if site doesn't have a custom header image ( default: none )
	 *    instance_id - ID name for site list instance ( default: network-sites-RAND )
	 *    class_name - CSS class name( s ) ( default: network-sites-list )
	 *    hide_meta - Select in order to update date and latest post. Only relevant when return = 'display'. ( default: false )
	 *    show_image - Select in order to hide site image. ( default: false )
	 *    show_join - Future
	 *    join_text - Future
	 * @return array $sites_list The array of sites.
	 */
	public function get_network_sites( $parameters = array() ) {

		/** Default parameters **/
		$defaults = array(
			'return' => (string) 'display',
			'number_sites' => (int) null,
			'exclude_sites' => array(),
			'sort_by' => (string) 'alpha',
			'default_image' => (string) null,
			'show_meta' => (bool) False,
			'show_image' => (bool) False,
			'id' => (string) 'network-sites-' . rand(),
			'class' => (string) 'network-sites-list',
		);

		// CALL MERGE FUNCTION
		$settings = wp_parse_args( $parameters, $defaults );

		// Extract each parameter as its own variable
		extract( $settings, EXTR_SKIP );

		// CALL GET SITES FUNCTION
		$sites_list = glocal_get_sites_list( $settings );

		// Sorting
		switch ( $sort_by ) {

			case 'newest':
				$sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'registered', 'DESC' );
				break;

			case 'updated':
				$sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'last_updated', 'DESC' );
				break;

			case 'active':
				$sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'post_count', 'DESC' );
				break;

			default:
				$sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'blogname' );

		}

		if ( $return == 'array' ) {
			return $sites_list;
		} else {
			// CALL RENDER FUNCTION
			return render_sites_list( $sites_list, $settings );
		}

	}



} // end class WP_Network_Content_Display_Sites
