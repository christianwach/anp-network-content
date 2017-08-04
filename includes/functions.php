<?php

/**
 * Network Content Display Constructors
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   WP_Network_Content_Display
 */



/**
 * Retrieve an array of site data.
 *
 * @param array $options_array The array of parameters.
 * @return array $site_list The array of sites with site information.
 */
function glocal_get_sites_list( $options_array ) {

	// Make each parameter as its own variable
	extract( $options_array, EXTR_SKIP );

	$site_args = array(
		'limit' => null,
		'public' => 1,
		'archived' => 0,
		'spam' => 0,
		'deleted' => 0,
		'mature' => null,
	);

	 // Allow the $site_args to be filtered
	if ( has_filter( 'glocal_network_sites_site_arguments' ) ) {
		$site_args = apply_filters( 'glocal_network_sites_site_arguments', $site_args );
	}

	// get sites
	$sites = get_sites( $site_args );

	// CALL EXCLUDE SITES FUNCTION
	$sites = ( ! empty( $exclude_sites ) ) ? glocal_exclude_sites( $exclude_sites, $sites ) : $sites;

	$site_list = array();

	foreach( $sites as $site ) {

		$site_details = get_blog_details( $site->blog_id );

		$site_list[$site->blog_id] = array(
			'blog_id' => $site->blog_id,	// Put site ID into array
			'blogname' => $site_details->blogname,	// Put site name into array
			'siteurl' => $site_details->siteurl,	// Put site URL into array
			'path' => $site_details->path,	// Put site path into array
			'registered' => $site_details->registered,
			'last_updated' => $site_details->last_updated,
			'post_count' => intval( $site_details->post_count ),
		);

		// CALL GET SITE IMAGE FUNCTION
		$site_image = WP_Network_Content_Display_Helpers::get_site_header_image( $site->blog_id );

		if ( $site_image ) {
			$site_list[$site->blog_id]['site-image'] = $site_image;
		} elseif ( isset( $default_image ) ) {
			$site_list[$site->blog_id]['site-image'] = $default_image;
		} else {
			$site_list[$site->blog_id]['site-image'] = '';
		}

		$site_list[$site->blog_id]['recent_post'] = glocal_get_most_recent_post( $site->blog_id );

	}

	return $site_list;

}



/**
 * Get an array of all sites excluding those passed to this function.
 *
 * @param array $exclude_array The array of sites to exclude.
 * @return array $sites The array of sites, excluding those passed into function.
 */
function glocal_exclude_sites( $exclude_array ) {

	// Site statuses to include
	$site_args = array(
		'limit'			=> null,
		'public'		 => 1,
		'archived'	 => 0,
		'spam'			 => 0,
		'deleted'		=> 0,
		'mature'		 => null,
	);

	// Allow the $site_args to be changed
	if ( has_filter( 'glocal_network_exclude_sites_arguments' ) ) {
		$site_args = apply_filters( 'glocal_network_exclude_sites_arguments', $site_args );
	}

	// Get a list of sites
	$sites = get_sites( $site_args );

	$exclude = ( ! is_array( $exclude_array ) ) ? explode( ',', $exclude_array ) : $exclude_array ;

	$sites = array_filter( $sites, function( $site ) use ( $exclude ) {
		return ! in_array( $site->blog_id, $exclude );
	} );

	return $sites;

}



/**
 * Get the most recent post from the specified site.
 *
 * @param int $site_id The numeric ID of the site.
 * @return array $recent_post_data The array of data for the post.
 */
function glocal_get_most_recent_post( $site_id ) {

	// Switch to current blog
	switch_to_blog( $site_id );

	// Get most recent post
	$recent_posts = wp_get_recent_posts( 'numberposts=1' );

	// Get most recent post info
	foreach( $recent_posts as $post ) {

		$post_id = $post['ID'];

		// Post into $site_list array
		$recent_post_data = array (
			'post_id' => $post_id,
			'post_author' => $post['post_author'],
			'post_slug' => $post['post_name'],
			'post_date' => $post['post_date'],
			'post_title' => $post['post_title'],
			'post_content' => $post['post_content'],
			'permalink' => get_permalink( $post_id ),
		);

		// If there is a featured image, add URL to array, else leave empty
		if ( wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ) ) {
			$recent_post_data['thumbnail'] = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		} else {
			$recent_post_data['thumbnail'] = '';
		}

	}

	// Exit
	restore_current_blog();

	return $recent_post_data;

}
