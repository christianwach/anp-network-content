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

	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$siteargs = array(
		'limit' => null,
		'public' => 1,
		'archived' => 0,
		'spam' => 0,
		'deleted' => 0,
		'mature' => null,
	);

	 // Allow the $siteargs to be	changed
	if ( has_filter( 'glocal_network_sites_site_arguments' ) ) {
		$siteargs = apply_filters( 'glocal_network_sites_site_arguments', $siteargs );
	}

	// Allow the $siteargs to be changed
	$sites = get_sites( $siteargs );

	// CALL EXCLUDE SITES FUNCTION
	$sites = ( ! empty( $exclude_sites ) ) ? glocal_exclude_sites( $exclude_sites, $sites ) : $sites;

	$site_list = array();

	foreach( $sites as $site ) {

		$site_id = $site->blog_id;
		$site_details = get_blog_details( $site_id );

		$site_list[$site_id] = array(
			'blog_id' => $site_id,	// Put site ID into array
			'blogname' => $site_details->blogname,	// Put site name into array
			'siteurl' => $site_details->siteurl,	// Put site URL into array
			'path' => $site_details->path,	// Put site path into array
			'registered' => $site_details->registered,
			'last_updated' => $site_details->last_updated,
			'post_count' => intval( $site_details->post_count ),
		);

		// CALL GET SITE IMAGE FUNCTION
		$site_image = WP_Network_Content_Display_Helpers::get_site_header_image( $site_id );

		if ( $site_image ) {
			$site_list[$site_id]['site-image'] = $site_image;
		} elseif ( isset( $default_image ) ) {
			$site_list[$site_id]['site-image'] = $default_image;
		} else {
			$site_list[$site_id]['site-image'] = '';
		}

		$site_list[$site_id]['recent_post'] = glocal_get_most_recent_post( $site_id );

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
	$siteargs = array(
		'limit'			=> null,
		'public'		 => 1,
		'archived'	 => 0,
		'spam'			 => 0,
		'deleted'		=> 0,
		'mature'		 => null,
	);

	// Allow the $siteargs to be	changed
	if ( has_filter( 'glocal_network_exclude_sites_arguments' ) ) {
		$siteargs = apply_filters( 'glocal_network_exclude_sites_arguments', $siteargs );
	}

	// Get a list of sites
	$sites = get_sites( $siteargs );

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

	$site_id = $site_id;

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




/************* RENDERING FUNCTIONS *****************/



/**
 * Render a list of posts.
 *
 * @param array $posts_array An array of posts data and params.
 * @param array $options_array An array of rendering options.
 * @return str $rendered_html The data rendered as 'normal' or 'highlight' HTML.
 */
function render_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	/*
	$e = new Exception;
	$trace = $e->getTraceAsString();
	error_log( print_r( array(
		'method' => __METHOD__,
		'settings' => $settings,
		//'backtrace' => $trace,
	), true ) );
	*/

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	if ( ! empty( $style ) ) {
		if( 'list'	== $style ) {
			$rendered_html = render_list_html( $posts_array, $settings );
		} else {
			 $rendered_html = render_block_html( $posts_array, $settings );
		}
	} else {
		$rendered_html = render_list_html( $posts_array, $settings );
	}

	return $rendered_html;

}



/**
 * Render an array of posts as an HTML list.
 *
 * @param array $posts_array An array of posts data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML list.
 */
function render_list_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	// Convert strings to booleans
	$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : '';
	$show_excerpt = ( ! empty( $show_excerpt ) ) ? filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) : '';
	$show_thumbnail = ( ! empty( $show_thumbnail ) ) ? filter_var( $show_thumbnail, FILTER_VALIDATE_BOOLEAN ) : '';
	$show_site_name = ( ! empty( $show_site_name ) ) ? filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) : '';

	$html = '<ul class="wp-network-posts ' . $post_type . '-list">';

	foreach( $posts_array as $post => $post_detail ) {

		global $post;

		$post_id = $post_detail['post_id'];

		if ( isset( $post_detail['categories'] ) ) {
			$post_categories = implode( ", ", $post_detail['categories'] );
		}

		$template_name = $post_type . '-list.php';

		// // use a template for the output so that it can easily be overridden by theme
		// // check for template in active theme
		// $template = locate_template( array( 'plugins/wp-network-content-display/' . $post_type . '-list.php' ) );
		//
		// // if none found use the default template
		// if ( $template == '' ) $template = WP_NETWORK_CONTENT_DISPLAY_DIR . 'assets/templates/' . $post_type . '-list.php';
		//
		// include ( $template );

		glocal_content_locate_template( $template_name, true );

	}

	$html .= '</ul>';

	return $html;

}



/**
 * Render an array of posts as an HTML "block".
 *
 * @param array $posts_array An array of posts data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML "block".
 */
function render_block_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$html = '<div class="wp-network-posts ' . $post_type . '-list">';

	foreach( $posts_array as $post => $post_detail ) {

		global $post;

		$post_id = $post_detail['post_id'];
		$post_categories = ( isset( $post_detail['categories'] ) ) ? implode( ", ", $post_detail['categories'] ) : '';

		// Convert strings to booleans
		$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_excerpt = ( ! empty( $show_excerpt ) ) ? filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_thumbnail = ( ! empty( $show_thumbnail ) ) ? filter_var( $show_thumbnail, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_site_name = ( ! empty( $show_site_name) ) ? filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) : '';

		$template_name = $post_type . '-block.php';

		if( file_exists( trailingslashit( get_stylesheet_directory() ) . 'plugins/wp-network-content-display/' . $template_name ) ) {
			$template = trailingslashit( get_stylesheet_directory() ) . 'plugins/wp-network-content-display/' . $template_name;
		} else {
			$template = WP_NETWORK_CONTENT_DISPLAY_DIR . 'assets/templates/' . $template_name;
		}

		$template = apply_filters( 'glocal_network_content_block_template', $template );

		// // use a template for the output so that it can easily be overridden by theme
		// // check for template in active theme
		// $template = locate_template( trailingslashit( get_stylesheet_directory() ) . 'plugins/wp-network-content-display/' . $template_name );
		//
		// // if none found use the default template
		// $template = ( '' != $template ) ? trailingslashit( get_stylesheet_directory() ) . 'plugins/wp-network-content-display/' . $template_name : WP_NETWORK_CONTENT_DISPLAY_DIR . 'assets/templates/' . $template_name;
		//
		// $template = trailingslashit( get_stylesheet_directory() ) . 'plugins/wp-network-content-display/' . $template_name;

		include( $template );

	}

	$html .= '</div>';

	return $html;

}



/**
 * Render an array of posts as "highlights".
 *
 * @param array $posts_array An array of posts data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as "highlights".
 */
function render_highlights_html( $posts_array, $options_array ) {

	$highlight_posts = $posts_array;
	$settings = $options_array;

	// Extract each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$title_image = ( isset( $title_image ) ) ? 'style="background-image:url(' . $title_image . ')"' : '';

	$html = '';

	// use a template for the output so that it can easily be overridden by theme
	// check for template in active theme
	$template = locate_template(array( 'plugins/wp-network-content-display/post-highlights.php'));

	// if none found use the default template
	$template = ( $template == '' ) ? WP_NETWORK_CONTENT_DISPLAY_DIR . 'assets/templates/post-highlights.php' : '';

	include( $template );

	return $html;

}



/**
 * Render an array of sites as an HTML list.
 *
 * @param array $sites_array An array of sites data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML list.
 */
function render_sites_list( $sites_array, $options_array ) {

	$sites = $sites_array;
	$settings = $options_array;

	// Extract each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$show_image = (filter_var($show_image, FILTER_VALIDATE_BOOLEAN));
	$show_meta = (filter_var($show_meta, FILTER_VALIDATE_BOOLEAN));

	if ( ! $show_image ) {
		$class .= ' no-site-image';
	} else {
		$class .= ' show-site-image';
	}

	$html = '<ul id="' . $id . '" class="sites-list ' . $class . '">';

	foreach ( $sites as $site ) {

		$site_id = $site['blog_id'];

		// CALL GET SLUG FUNCTION
		$slug = WP_Network_Content_Display_Helpers::get_site_slug( $site['path'] );

		// use a template for the output so that it can easily be overridden by theme
		// check for template in active theme
		$template = locate_template( array( 'plugins/wp-network-content-display/sites-list.php' ) );

		// if none found use the default template
		$template = ( $template == '' ) ? WP_NETWORK_CONTENT_DISPLAY_DIR . 'assets/templates/sites-list.php' : '';

		include( $template );

	}

	$html .= '</ul>';

	return $html;

}



/**
 * Render an array of events as an HTML list.
 *
 * NOT USED
 *
 * @param array $events_array An array of events data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML list.
 */
function render_events_list( $events_array, $options_array ) {

}



/**
 * Render an array of events as an HTML list.
 *
 * NOT USED
 *
 * @param array $events_array An array of events data and params.
 * @param array $options_array An array of rendering options.
 *    'number_posts' => 10, // int
 *    'exclude_sites' => null, // array
 *    'output' => 'html', // string - html, array
 *    'style' => 'list', // string - list, block
 *    'id' => 'network-events-' . rand(),
 *    'class' => 'event-list',
 *    'title' => 'Events',
 *    'show_meta' => True, // boolean
 *    'post_type' => 'event',
 * @return str $html The data rendered as an HTML list.
 */
function render_event_list_html( $events_array, $options_array ) {

	$events_array = $events_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	// Convert strings to booleans
	$show_meta = ( filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) );

	$html = '<ul class="network-event-list ' . $post_type . '-list">';

	foreach( $events_array as $event => $event_detail ) {

		global $post;

		$post_id = $event_detail['post_id'];

		// use a template for the output so that it can easily be overridden by theme
		// check for template in active theme
		$template = locate_template( array( 'plugins/wp-network-content-display/event-list.php' ) );

		// if none found use the default template
		include( $template );

	}

	$html .= '</ul>';

	return $html;

}



/**
 * Render an array of events as an HTML "block".
 *
 * NOT USED
 *
 * @param array $events_array An array of events data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML "block".
 */
function render_event_block_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$html = '<div class="network-posts-list style-' . $style . '">';

	foreach( $posts_array as $post => $post_detail ) {

		global $post;

		$post_id = $post_detail['post_id'];
		$post_categories = ( isset( $post_detail['categories'] ) ) ? implode( ", ", $post_detail['categories'] ) : '';

		// Convert strings to booleans
		$show_meta = ( filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) );
		$show_thumbnail = ( filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) );
		$show_site_name = ( filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) );

		// use a template for the output so that it can easily be overridden by theme
		// check for template in active theme
		$template = locate_template( array( 'plugins/wp-network-content-display/post-block.php' ) );

		// if none found use the default template
		$template = ( $template == '' ) ? WP_NETWORK_CONTENT_DISPLAY_DIR . 'assets/templates/post-block.php' : '';

		include( $template );

	}

	$html .= '</div>';

	return $html;

}

