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
	 * Posts Entity object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $posts_entity The Posts Entity object
	 */
	public $posts_entity;

	/**
	 * Sites Entity object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $sites_entity The Sites Entity object
	 */
	public $sites_entity;

	/**
	 * Events Entity object.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @var object $events_entity The Events Entity object
	 */
	public $events_entity;



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

		// global scope functions
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/functions.php' );

		// include helper class
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/class-helpers.php' );

		// include Posts Entity class file
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/entities/class-posts.php' );

		// include Sites Entity class file
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/entities/class-sites.php' );

		// include Network Events Entity if Event Organiser plugin is present and active
		if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
			require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/entities/class-events.php' );
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

		// instantiate Posts class
		$this->posts_entity = new WP_Network_Content_Display_Posts;

		// instantiate Sites class
		$this->sites_entity = new WP_Network_Content_Display_Sites;

		// instantiate Network Events if Event Organiser plugin is present and active
		if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
			$this->events_entity = new WP_Network_Content_Display_Events;
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
			'glocal-network-posts',
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
