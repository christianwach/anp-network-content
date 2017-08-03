<?php

/**
 * Network Content Widgets and Shortcodes
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.2.2
 * @package   WP_Network_Content_Display
 */

/*
Plugin Name: WP Network Content Display
Description: Provides Widgets and Shortcodes that display network content on your multi-site or multi-network install.
Author: Pea, Glocal
Author URI: http://glocal.coop
Version: 2.0.0
License: GPLv3
Text Domain: wp-network-content-display
Domain Path: /languages
*/



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


/* ---------------------------------- *
 * Constants
 * ---------------------------------- */

// set our version here
define( 'WP_NETWORK_CONTENT_DISPLAY_VERSION', '2.0.0' );

if ( ! defined( 'WP_NETWORK_CONTENT_DISPLAY_DIR' ) ) {
    define( 'WP_NETWORK_CONTENT_DISPLAY_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WP_NETWORK_CONTENT_DISPLAY_URL' ) ) {
    define( 'WP_NETWORK_CONTENT_DISPLAY_URL', plugin_dir_url( __FILE__ ) );
}



/**
 * Network Content Widgets and Shortcodes Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 2.0.0
 */
class WP_Network_Content_Display {

	/**
	 * Posts Shortcode object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $posts_shortcode The Posts Shortcode object
	 */
	public $posts_shortcode;

	/**
	 * Sites Shortcode object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $sites_shortcode The Sites Shortcode object
	 */
	public $sites_shortcode;

	/**
	 * Events Shortcode object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $events_shortcode The Events Shortcode object
	 */
	public $events_shortcode;



	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// use translation files
		add_action( 'plugins_loaded', array( $this, 'enable_translation' ) );

		// initialise
		add_action( 'plugins_loaded', array( $this, 'initialise' ) );

	}



	/**
	 * Load translation files.
	 *
	 * A good reference on how to implement translation in WordPress:
	 * http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
	 *
	 * @since 2.0.0
	 */
	public function enable_translation() {

		// not used, as there are no translations as yet
		load_plugin_textdomain(

			// unique name
			'wp-network-content-display',

			// deprecated argument
			false,

			// relative path to directory containing translation files
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'

		);

	}



	/**
	 * Set up this plugin.
	 *
	 * @since 2.0.0
	 */
	public function initialise() {

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

		// legacy files
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/constructors.php' );
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/get-content.php' );
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/helpers.php' );
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/render.php' );
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/enqueue.php' );

		// include Shortcode class files
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/shortcodes/class-shortcode-posts.php' );
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/shortcodes/class-shortcode-sites.php' );

		// include Network Events Shortcode if Event Organiser plugin is present and active
		if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
			require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/shortcodes/class-shortcode-events.php' );
		}

	}



	/**
	 * Set up this plugin's objects and references.
	 *
	 * @since 2.0.0
	 */
	public function setup_objects() {

		// only do this once
		static $done;
		if ( isset( $done ) AND $done === true ) return;

		// instantiate default Shortcode classes
		$this->posts_shortcode = new WP_Network_Content_Display_Posts_Shortcode;
		$this->sites_shortcode = new WP_Network_Content_Display_Sites_Shortcode;

		// instantiate Network Events Shortcode if Event Organiser plugin is present and active
		if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
			$this->events_shortcode = new WP_Network_Content_Display_Events_Shortcode;
		}

		// we're done
		$done = true;

	}



	/**
	 * Register widgets for this plugin.
	 *
	 * @since 2.0.0
	 */
	public function register_widgets() {

		// register Network Posts Widget
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/class-network-posts-widget.php' );
		register_widget( 'Glocal_Network_Posts_Widget' );

		// register Network Sites Widget
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/class-network-sites-widget.php' );
		register_widget( 'Glocal_Network_Sites_Widget' );

		// register Network Events Widget if Event Organiser plugin is present and active
		if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
			require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/class-network-events-widget.php' );
			register_widget( 'Glocal_Network_Events_Widget' );
		}

	}



} // end class WP_Network_Content_Display



// declare as global
global $wp_network_content_display;

// init plugin
$wp_network_content_display = new WP_Network_Content_Display;



/**
 * Utility to get a reference to this plugin.
 *
 * @since 2.0.0
 *
 * @return object $wp_network_content_display The plugin reference.
 */
function wp_network_content_display() {

	// return instance
	global $wp_network_content_display;
	return $wp_network_content_display;

}
