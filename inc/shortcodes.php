<?php

/**
 * ANP Network Content Shortcodes
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */



/************* SHORTCODE FUNCTIONS *****************/



/**
 * Render the 'embed_network_posts' shortcode.
 *
 * Example shortcode:
 *
 * [embed_network_posts title="Module Title" title_image="/path/to/image.png" number_posts="20" exclude_sites="2,3" posts_per_site="5" style="block" show_meta=1 show_excerpt=1 show_site_name=1 id="unique-id" class="class-name"]
 *
 * @param array $atts The shortcode attributes.
 */
function glocal_networkwide_posts_shortcode( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(), $atts )
	);

	$atts['style'] = 'normal';

	if( function_exists( 'glocal_networkwide_posts_module' ) ) {
		return glocal_networkwide_posts_module( $atts );
	}

}
add_shortcode( 'embed_network_posts', 'glocal_networkwide_posts_shortcode' );



/**
 * Render the 'embed_network_events' shortcode.
 *
 * Example shortcode:
 *
 * [embed_network_events title="Module Title" exclude_sites="2,3" posts_per_site="5" style="block" show_meta=1 show_excerpt=1 show_site_name=1 id="unique-id" class="class-name"]
 *
 * @param array $atts The shortcode attributes.
 */
function glocal_networkwide_events_shortcode( $atts, $content = null ) {

	// Attributes
	extract( shortcode_atts(
		array(), $atts )
	);

	$atts['post_type'] = 'event';

	if( function_exists( 'glocal_networkwide_posts_module' ) ) {
		return glocal_networkwide_posts_module( $atts );
	}

}
add_shortcode( 'embed_network_events', 'glocal_networkwide_events_shortcode' );



/**
 * Render the 'embed_network_sites' shortcode.
 *
 * Example shortcode:
 *
 * [embed_network_sites number_sites="20" exclude_sites="1,2" sort_by="registered" default_image="/path/to/image.jpg" show_meta=1 show_image=1 id="unique-id" class="class-name"]
 *
 * @param array $atts The shortcode attributes.
 */
function glocal_networkwide_sites_shortcode( $atts, $content = null ) {

	// Attributes
	extract( shortcode_atts(
		array(), $atts )
	);

	if(function_exists('glocal_networkwide_sites_module')) {
		return glocal_networkwide_sites_module( $atts );
	}

}
add_shortcode( 'embed_network_sites', 'glocal_networkwide_sites_shortcode' );
