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
Version: 2.0.1
License: GPLv3
Text Domain: wp-network-content-display
Domain Path: /languages
Network: true
*/



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


/* ---------------------------------- *
 * Constants
 * ---------------------------------- */

// set our version here
define( 'WP_NETWORK_CONTENT_DISPLAY_VERSION', '2.0.1' );

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
	 * Components object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $components The Components object
	 */
	public $components;



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

		// enqueue styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 200 );

	}



	/**
	 * Include files.
	 *
	 * @since 2.0.0
	 */
	public function include_files() {

		// include helper class
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/class-helpers.php' );

		// include Posts Component class file
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/components/class-posts.php' );

		// include Sites Component class file
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/components/class-sites.php' );

		// include Network Events Component if Event Organiser plugin is present and active
		if ( defined( 'EVENT_ORGANISER_VER' ) ) {
			require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/components/class-events.php' );
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

		// init components
		$this->components = new stdClass;

		// instantiate Posts Component class
		$this->components->posts = new WP_Network_Content_Display_Posts;

		// instantiate Sites Component class
		$this->components->sites = new WP_Network_Content_Display_Sites;

		// instantiate Network Events Component if Event Organiser plugin is present and active
		if ( defined( 'EVENT_ORGANISER_VER' ) ) {
			$this->components->events = new WP_Network_Content_Display_Events;
		}

		// we're done
		$done = true;

	}



	/**
	 * Enqueue stylesheets for this plugin.
	 *
	 * Bumping the plugin version will cause the styles to be reloaded.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			'wp-network-content-styles',
			WP_NETWORK_CONTENT_DISPLAY_URL . 'assets/css/style.min.css',
			false, // dependencies
			WP_NETWORK_CONTENT_DISPLAY_VERSION, // version
			'all' // media
		);

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
