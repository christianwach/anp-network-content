<?php

/**
 * ANP Network Content
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.2.0
 * @package   ANP_Network_Content
 */

/*
Plugin Name: Activist Network Content
Description: Widgets and shortcodes that display network content on your multi-site install.
Author: Pea, Glocal
Author URI: http://glocal.coop
Version: 1.6.7
License: GPLv3
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


/* ---------------------------------- *
 * Constants
 * ---------------------------------- */

if ( !defined( 'ANP_NETWORK_CONTENT_PLUGIN_DIR' ) ) {
    define( 'ANP_NETWORK_CONTENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'ANP_NETWORK_CONTENT_PLUGIN_URL' ) ) {
    define( 'ANP_NETWORK_CONTENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/constructors.php' );
include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/get-content.php' );
include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/helpers.php' );
include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/render.php' );
include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/shortcodes.php' );
include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/shortcake.php' );
include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/enqueue.php' );

include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/class-network-posts.php' );

include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/class-network-events.php' );
include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'inc/class-network-sites.php' );

include_once( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'glocal-network-content-widgets.php' );
