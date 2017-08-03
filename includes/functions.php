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
 * NETWORK POSTS MAIN FUNCTION.
 *
 * Get (or render) posts from sites across the network.
 *
 * 1/5/2016: Updated to allow for custom post types.
 *
 * Editable Templates
 * ---
 * Display of Network Content can be customized by adding a custom template to your theme in 'plugins/wp-network-content-display/'
 * event-block.php
 * event-list.php
 * post-block.php
 * post-highlights.php
 * post-list.php
 * sites-list.php
 *
 * @param array $parameters An array of settings with the following options:
 *    post_type (string) - post type to display ( default: 'post' )
 *    event_scope (string) - timeframe of events, 'future', 'past', 'all' (default: 'future') - ignored if post_type !== 'event'
 *    number_posts (int) - the total number of posts to display ( default: 10 )
 *    posts_per_site (int) - the number of posts for each site ( default: no limit )
 *    include_categories (array) - the categories of posts to include ( default: all categories )
 *    exclude_sites (array) - the sites from which posts should be excluded ( default: all sites ( public sites, except archived, deleted and spam ) )
 *    output (string) - HTML or array ( default: HTML )
 *    style - (string) normal ( list ), block or highlights ( default: normal ) - ignored if @output is 'array'
 *    id (int) - ID used in list markup ( default: network-posts-RAND ) - ignored if @output is 'array'
 *    class (string) - class used in list markup ( default: post-list ) - ignored if @output is 'array'
 *    title (string) - title displayed for list ( default: Posts ) - ignored unless @style is 'highlights'
 *    title_image (string) - image displayed behind title ( default: home-highlight.png ) - ignored unless @style is 'highlights'
 *    show_thumbnail (bool) - display post thumbnail ( default: False ) - ignored if @output is 'array'
 *    show_meta (bool) - if meta info should be displayed ( default: True ) - ignored if @output is 'array'
 *    show_excerpt (bool) - if excerpt should be displayed ( default: True ) - ignored if @output is 'array' or if @show_meta is False
 *    excerpt_length (int) - number of words to display for excerpt ( default: 50 ) - ignored if @show_excerpt is False
 *    show_site_name (bool) - if site name should be displayed ( default: True ) - ignored if @output is 'array'
 * @return array $posts_list The array of posts.
 */
function glocal_networkwide_posts_module( $parameters = array() ) {

    // Default parameters
    $defaults = array(
        'post_type' => (string) 'post', // (string) - post, event
        'number_posts' => (int) 10, // (int)
        'exclude_sites' => array(),
        'include_categories' => array(),
        'posts_per_site' => (int) null, // (int)
        'output' => (string) 'html', // (string) - html, array
        'style' => (string) 'normal', // (string) - normal
        'id' => (string) 'network-posts-' . rand(), // (string)
        'class' => (string) 'post-list', // (string)
        'title' => (string) 'Posts', // (string)
        'title_image' => (string) null, // (string)
        'show_meta' => (bool) True, // (bool)
        'show_thumbnail' => (bool) False, // (bool)
        'show_excerpt' => (bool) True, // (bool)
        'excerpt_length' => (int) 55, // (int)
        'show_site_name' => (bool) True, // (bool)
        'event_scope' => (string) 'future', // (string) - future, past, all
        'include_event_categories' => array(), // (array) - event-category (term name) to include
        'include_event_tags' => array(), // (array) - event-tag (term name) to include
    );

    // SANITIZE INPUT
    $parameters = WP_Network_Content_Display_Helpers::sanitize_input( $parameters );

    if ( isset( $parameters['exclude_sites'] ) && !empty( $parameters['exclude_sites'] ) ) {
        $parameters['exclude_sites'] = explode( ',', $parameters['exclude_sites'] );
    }

    if ( isset( $parameters['include_event_categories'] ) && !empty( $parameters['include_event_categories'] ) ) {
        $parameters['include_event_categories'] = explode( ',', $parameters['include_event_categories'] );
    }

    if ( isset( $parameters['include_event_tags'] ) && !empty( $parameters['include_event_tags'] ) ) {
        $parameters['include_event_tags'] = explode( ',', $parameters['include_event_tags'] );
    }

    // CALL MERGE FUNCTION
    $settings = WP_Network_Content_Display_Helpers::get_merged_settings( $parameters, $defaults );

    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );

    // CALL SITES FUNCTION
    $sites_list = get_sites_list( $settings );

    // CALL GET POSTS FUNCTION
    $posts_list = get_posts_list( $sites_list, $settings );

    if ( $output == 'array' ) {

        // Return an array
        return $posts_list;

    } else {

        // CALL RENDER FUNCTION
        return render_html( $posts_list, $settings );

    }

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
function glocal_networkwide_sites_module( $parameters = array() ) {

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
    $sites_list = get_sites_list( $settings );

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



/************* GET CONTENT FUNCTIONS *****************/



/**
 * Retrieve an array of site data.
 *
 * @param array $options_array The array of parameters.
 * @return array $site_list The array of sites with site information.
 */
function get_sites_list( $options_array ) {

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
	$sites = ( ! empty( $exclude_sites ) ) ? exclude_sites( $exclude_sites, $sites ) : $sites;

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

		$site_list[$site_id]['recent_post'] = get_most_recent_post( $site_id );

	}

	return $site_list;

}



/**
 * Get an array of all sites excluding those passed to this function.
 *
 * @param array $exclude_array The array of sites to exclude.
 * @return array $sites The array of sites, excluding those passed into function.
 */
function exclude_sites( $exclude_array ) {

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

	$exclude = ( !is_array( $exclude_array ) ) ? explode( ',', $exclude_array ) : $exclude_array ;

	$sites = array_filter( $sites, function( $site ) use ( $exclude ) {
		return !in_array( $site->blog_id, $exclude );
	} );

	return $sites;

}



/**
 * Get an array of posts from the specified sites.
 *
 * @param array $sites_array The array of sites to include.
 * @param array $options_array The options for post retrieval.
 * @return array $post_list The array of posts with site information, sorted by post_date.
 */
function get_posts_list( $sites_array, $options_array ) {

	$sites = $sites_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$post_list = array();

	// For each site, get the posts
	foreach( $sites as $site ) {

		$site_id = $site['blog_id'];

		// Switch to the site to get details and posts
		switch_to_blog( $site_id );

		// CALL GET SITE'S POST FUNCTION
		// And add to array of posts

		// If get_sites_posts( $site_id, $settings ) isn't null, add it to the array, else skip it
		// Trying to add a null value to the array using this syntax produces a fatal error.

		$site_posts = get_sites_posts( $site_id, $settings );

		if ( get_sites_posts( $site_id, $settings ) ) {
			$post_list = $post_list + get_sites_posts( $site_id, $settings );
		}

		// Unswitch the site
		restore_current_blog();

	}

	// SORT ARRAY
	if ( 'event' === $post_type ) {
		$post_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $post_list, 'event_start_date' );
	} else {
		$post_list = WP_Network_Content_Display_Helpers::sort_by_date( $post_list );
	}

	// CALL LIMIT FUNCTIONS
	$post_list = ( isset( $number_posts ) ) ?
				 WP_Network_Content_Display_Helpers::limit_number_posts( $post_list, $number_posts ) :
				 $post_list;

	return $post_list;

}



/**
 * Get an array of posts for a specified site.
 *
 * @param int $site_id The numeric ID of the site.
 * @param array $options_array The options for post retrieval.
 * @return array $post_list The array of posts with site information, sorted by post_date.
 */
function get_sites_posts( $site_id, $options_array ) {

	$site_id = $site_id;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$site_details = get_blog_details( $site_id );

	$post_args['post_type'] = ( isset( $post_type ) ) ? $post_type : 'post' ;
	$post_args['posts_per_page'] = ( isset( $posts_per_page ) ) ? $posts_per_page : 20 ;
	$post_args['category_name'] = ( isset( $include_categories ) ) ? $include_categories : '';

	// Event-specific arguments
	if ( 'event' === $post_type ) {

		if ( isset( $include_event_categories ) ) {
			$post_args['tax_query'][] = array(
				'taxonomy' => 'event-category',
				'field' => 'slug',
				'terms' => $include_event_categories
			);
		}

		if ( isset( $include_event_tags ) ) {
			$post_args['tax_query'][] = array(
				'taxonomy' => 'event-tag',
				'field' => 'slug',
				'terms' => $include_event_tags
			);
		}

		switch ( $event_scope ) {
			case 'past' :
				$post_args['meta_query'] = array(
					array(
						'key' => '_eventorganiser_schedule_start_start',
						'value' => date_i18n( 'Y-m-d' ),
						'compare' => '<',
					),
				);
				break;
			default :
				$post_args['meta_query'] = array(
					array(
						'key' => '_eventorganiser_schedule_start_start',
						'value' => date_i18n( 'Y-m-d' ),
						'compare' => '>=',
					),
				);
		}

	}


	$recent_posts = wp_get_recent_posts( $post_args );

	// Put all the posts in a single array
	foreach( $recent_posts as $post => $postdetail ) {

		//global $post;

		$post_id = $postdetail['ID'];
		$author_id = $postdetail['post_author'];

		// Prefix the array key with event start date or post date
		$prefix = ( 'event' === $post_type ) ?
				  get_post_meta ( $post_id, '_eventorganiser_schedule_start_start', true ) . '-' . $postdetail['post_name'] :
				  $postdetail['post_date'] . '-' . $postdetail['post_name'];

		//CALL POST MARKUP FUNCTION
		$post_markup_class = WP_Network_Content_Display_Helpers::get_post_markup_class( $post_id );
		$post_markup_class .= ' siteid-' . $site_id;

		//Returns an array
		$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'medium' );

		if ( $postdetail['post_excerpt'] ) {
			$excerpt = $postdetail['post_excerpt'];
		} else {
			$excerpt = wp_trim_words(
				$postdetail['post_content'],
				$excerpt_length,
				sprintf( __( '... <a href="%s">Read More</a>', 'wp-network-content-display' ), get_permalink( $post_id ) )
			);
		}

		$post_list[$prefix] = array(
			'post_id' => $post_id,
			'post_title' => $postdetail['post_title'],
			'post_date' => $postdetail['post_date'],
			'post_author' => get_the_author_meta( 'display_name', $postdetail['post_author'] ),
			'post_content' => $postdetail['post_content'],
			'post_excerpt' => strip_shortcodes( $excerpt ),
			'permalink' => get_permalink( $post_id ),
			'post_image' => $post_thumbnail[0],
			'post_class' => $post_markup_class,
			'post_type' => $post_type,
			'site_id' => $site_id,
			'site_name' => $site_details->blogname,
			'site_link' => $site_details->siteurl,
		);

		if ( 'event' === $post_type || function_exists( 'eo_get_venue' ) ) {

			$venue_id = eo_get_venue( $post_id );

			$post_list[$prefix]['event_start_date'] = get_post_meta ( $post_id, '_eventorganiser_schedule_start_start', true );
			$post_list[$prefix]['event_end_date'] = get_post_meta ( $post_id, '_eventorganiser_schedule_start_finish', true );

			$post_list[$prefix]['event_venue']['venue_link'] = eo_get_venue_link( $venue_id );
			$post_list[$prefix]['event_venue']['venue_id'] = $venue_id;
			$post_list[$prefix]['event_venue']['venue_name'] = eo_get_venue_name( $venue_id );
			$post_list[$prefix]['event_venue']['venue_location'] = eo_get_venue_address( $venue_id );
			$post_list[$prefix]['event_venue']['venue_location']['venue_lat'] = eo_get_venue_meta( $venue_id, '_lat' );
			$post_list[$prefix]['event_venue']['venue_location']['venue_long'] = eo_get_venue_meta( $venue_id, '_lng' );

			//Get post categories
			$event_categories = wp_get_post_terms( $post_id, 'event-category', array( "fields" => "all" ) );

			foreach( $event_categories as $event_category ) {
				$post_list[$prefix]['event_categories'][$event_category->slug] = $event_category->name;
			}

			$event_tags = wp_get_post_terms( $post_id, 'event-tag', array( "fields" => "all" ) );

			foreach( $event_tags as $event_tag ) {
				$post_list[$prefix]['event_tags'][$event_tag->slug] = $event_tag->name;
			}

		}

		// Get post categories
		$post_categories = wp_get_post_categories( $post_id );

		foreach( $post_categories as $post_category ) {
			$cat = get_category( $post_category );
			$post_list[$prefix]['categories'][] = $cat->name;
		}

		return $post_list;

	}

}



/**
 * Get the most recent post from the specified site.
 *
 * @param int $site_id The numeric ID of the site.
 * @return array $recent_post_data The array of data for the post.
 */
function get_most_recent_post( $site_id ) {

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



/**
 * Get sitewide taxonomy terms.
 *
 * @param str $taxonomy The name of of the taxonomy.
 * @param array $exclude_sites The sites to exclude.
 * @return array $term_list The array of terms with unique taxonomy term slugs and names.
 */
function get_sitewide_taxonomy_terms( $taxonomy, $exclude_sites = null ) {

	// Site statuses to include
	$siteargs = array(
		'limit' => null,
		'public' => 1,
		'archived' => 0,
		'spam' => 0,
		'deleted' => 0,
		'mature' => null,
	);

	// Allow the $siteargs to be changed
	if ( has_filter( 'glocal_network_tax_term_siteargs_arguments' ) ) {
		$siteargs = apply_filters( 'glocal_network_tax_term_siteargs_arguments', $siteargs );
	}

	$sites_list = ( $exclude_sites ) ? exclude_sites( $exclude_sites ) : get_sites( $siteargs );

	$termargs = array();

	// Allow the $siteargs to be changed
	if ( has_filter( 'glocal_network_tax_termarg_arguments' ) ) {
		$termargs = apply_filters( 'glocal_network_tax_termarg_arguments', $termargs );
	}

	$term_list = array();

	foreach( $sites_list as $site ) {

		$site_id = $site->blog_id;

		// Switch to the site to get details and posts
		switch_to_blog( $site_id );

		$site_terms = get_terms( $taxonomy, $termargs );

		foreach( $site_terms as $term ) {
			if ( !in_array( $term->slug, $term_list ) ) {
				$term_list[$term->slug] = $term->name;
			}
		}

		// Unswitch the site
		restore_current_blog();

	}

	$term_list = array_unique( $term_list );

	return $term_list;

}



/**
 * Custom event meta.
 *
 * @param int $event_id The numeric ID of the event.
 * @return str $html The formatted event meta.
 */
if ( ! function_exists( 'glocal_get_event_taxonomy' ) ) {

	function glocal_get_event_taxonomy( $event_id = 0 ) {

		$event_id = (int) ( empty( $event_id ) ? get_the_ID() : $event_id );

		if ( empty( $event_id ) ){
			return false;
		}

		$html = '<div class="entry-meta event-meta">';
		$venue = get_taxonomy( 'event-venue' );

		if ( get_the_terms( $event_id, 'event-category' ) ) {
			$html .= get_the_term_list( $event_id, 'event-category', '<ul class="category event-category"><li>','</li><li class="cat-item">', '</li></ul>' );
		}

		if ( get_the_terms( $event_id, 'event-tag' ) && ! is_wp_error( get_the_terms( $event_id, 'event-tag' ) ) ) {
			$html .= get_the_term_list( $event_id, 'event-tag', '<ul class="event-tags tags"><li class="tag-item">','</li><li>', '</li></ul>' );
		}

		$html .='</div>';

		return $html;
	}

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

