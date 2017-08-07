<?php

/**
 * Network Content Widgets and Shortcodes Events Class.
 *
 * A class that encapsulates Events functionality. It inherits all methods from
 * WP_Network_Content_Display_Posts and adds a couple of methods.
 *
 * @since 2.0.0
 */
class WP_Network_Content_Display_Events extends WP_Network_Content_Display_Posts {

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

		// init parent
		parent::__construct();

	}



	/**
	 * Include files.
	 *
	 * @since 2.0.0
	 */
	public function include_files() {

		// include Shortcode class file
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/shortcodes/class-shortcode-events.php' );

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
		$this->shortcode = new WP_Network_Content_Display_Events_Shortcode;

		// we're done
		$done = true;

	}



	/**
	 * Register widgets for this component.
	 *
	 * @since 2.0.0
	 */
	public function register_widgets() {

		// register Network Events Widget
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/class-widget-events.php' );
		register_widget( 'WP_Network_Content_Display_Events_Widget' );

	}



	/************* CONTENT METHODS *****************/



	/**
	 * Get (or render) posts from sites across the network.
	 *
	 * @param array $parameters An array of settings with the following options:
	 *    post_type (string) - post type to display ( default: 'post' )
	 *    event_scope (string) - timeframe of events, 'future', 'past', 'all' (default: 'future')
	 *    number_posts (int) - the total number of posts to display ( default: 10 )
	 *    posts_per_site (int) - the number of posts for each site ( default: 5 )
	 *    include_categories (array) - the categories of posts to include ( default: all categories )
	 *    exclude_sites (array) - the sites from which posts should be excluded ( default: all sites ( public sites, except archived, deleted and spam ) )
	 *    output (string) - HTML or array ( default: HTML )
	 *    style - (string) block or list ( default: 'list' ) - ignored if @output is 'array'
	 *    id (int) - ID used in list markup ( default: network-posts-RAND ) - ignored if @output is 'array'
	 *    class (string) - class used in list markup ( default: post-list ) - ignored if @output is 'array'
	 *    title (string) - title displayed for list ( default: Posts ) - ignored unless @style is 'highlights'
	 *    title_image (string) - image displayed behind title ( default: home-highlight.png ) - ignored unless @style is 'highlights'
	 *    show_thumbnail (bool) - display post thumbnail ( default: false ) - ignored if @output is 'array'
	 *    show_meta (bool) - if meta info should be displayed ( default: true ) - ignored if @output is 'array'
	 *    show_excerpt (bool) - if excerpt should be displayed ( default: true ) - ignored if @output is 'array' or if @show_meta is false
	 *    excerpt_length (int) - number of words to display for excerpt ( default: 20 ) - ignored if @show_excerpt is false
	 *    show_site_name (bool) - if site name should be displayed ( default: true ) - ignored if @output is 'array'
	 * @return array $posts_list The array of posts.
	 */
	public function get_posts_from_network( $parameters = array() ) {

		// Default parameters
		$defaults = array(
			'post_type' => (string) 'post', // (string) - post, event
			'number_posts' => (int) 10, // (int)
			'exclude_sites' => array(),
			'include_categories' => array(),
			'posts_per_site' => (int) 5, // (int)
			'output' => (string) 'html', // (string) - html, array
			'style' => (string) 'list', // (string) - list
			'title' => (string) 'Posts', // (string)
			'title_image' => (string) null, // (string)
			'show_meta' => (bool) true, // (bool)
			'show_thumbnail' => (bool) false, // (bool)
			'show_excerpt' => (bool) true, // (bool)
			'excerpt_length' => (int) 20, // (int)
			'show_site_name' => (bool) true, // (bool)
			'event_scope' => (string) 'future', // (string) - future, past, all
			'include_categories' => array(), // (array) - event-category (term name) to include
			'include_tags' => array(), // (array) - event-tag (term name) to include
		);

		// sanitize params
		$parameters = WPNCD_Helpers::sanitize_input( $parameters );

		// convert comma-demilited lists to arrays
		if ( isset( $parameters['exclude_sites'] ) && ! empty( $parameters['exclude_sites'] ) ) {
			$parameters['exclude_sites'] = explode( ',', $parameters['exclude_sites'] );
		}
		if ( isset( $parameters['include_categories'] ) && ! empty( $parameters['include_categories'] ) ) {
			$parameters['include_categories'] = explode( ',', $parameters['include_categories'] );
		}
		if ( isset( $parameters['include_tags'] ) && ! empty( $parameters['include_tags'] ) ) {
			$parameters['include_tags'] = explode( ',', $parameters['include_tags'] );
		}

		// merge params with defaults
		$settings = WPNCD_Helpers::get_merged_settings( $parameters, $defaults );

		// Extract each parameter as its own variable
		extract( $settings, EXTR_SKIP );

		// CALL SITES FUNCTION
		$sites_list = WPNCD_Helpers::get_data_for_sites( $settings );

		// CALL GET POSTS FUNCTION
		$posts_list = $this->get_posts_for_sites( $sites_list, $settings );

		// SORT ARRAY
		$posts_list = WPNCD_Helpers::sort_array_by_key( $posts_list, 'event_start_date' );

		// CALL LIMIT FUNCTIONS
		$posts_list = ( isset( $number_posts ) ) ?
					  WPNCD_Helpers::limit_number_posts( $posts_list, $number_posts ) :
					  $post_list;

		// return raw if requested
		if ( $output == 'array' ) {
			return $posts_list;
		}

		// return rendered
		return $this->render_html( $posts_list, $settings );

	}



	/**
	 * Get an array of posts for a specified site.
	 *
	 * @param int $site_id The numeric ID of the site.
	 * @param array $options_array The options for post retrieval.
	 * @return array $post_list The array of posts with site information, sorted by post_date.
	 */
	public function get_posts_for_site( $site_id, $options_array ) {

		// init return
		$post_list = array();

		// bail if there are no Event Organiser options on this site
		$options = get_option( 'eventorganiser_options', array() );
		if ( empty( $options ) ) {
			return $post_list;
		}

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'options' => $options,
			//'backtrace' => $trace,
		), true ) );
		*/

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		$site_details = get_blog_details( $site_id );

		// define arguments to fetch recent posts
		$post_args = array(
			'post_type' => 'event',
			'numberposts' => ( isset( $posts_per_site ) ) ? (int) $posts_per_site : 5,
			'output' => ARRAY_A,
		);

		// Taxonomy queries default to: relation => 'AND'
		// We may want a switch to choose: relation => 'OR'

		// add categories
		if ( isset( $include_categories ) AND ! empty( $include_categories ) ) {

			if ( ! is_array( $include_categories ) ) {
				$include_categories = explode( ',', $include_categories );
			}

			$post_args['tax_query'][] = array(
				'taxonomy' => 'event-category',
				'field' => 'slug',
				'terms' => $include_categories,
			);

		}

		// add tags
		if ( isset( $include_tags ) AND ! empty( $include_tags ) ) {

			if ( ! is_array( $include_tags ) ) {
				$include_tags = explode( ',', $include_tags );
			}

			$post_args['tax_query'][] = array(
				'taxonomy' => 'event-tag',
				'field' => 'slug',
				'terms' => $include_tags,
			);

		}

		// choose a scope ("All" needs no meta query)
		switch ( $event_scope ) {

			case 'past' :

				/*
				$post_args['meta_query'] = array(
					array(
						'key' => '_eventorganiser_schedule_start_start',
						'value' => date_i18n( 'Y-m-d' ),
						'compare' => '<',
					),
				);
				*/

				// restrict to events that have finished
				$post_args['event_end_before'] = 'today';

				break;

			case 'future' :

				/*
				$post_args['meta_query'] = array(
					array(
						'key' => '_eventorganiser_schedule_start_start',
						'value' => date_i18n( 'Y-m-d' ),
						'compare' => '>=',
					),
				);
				*/

				// allow events that have not yet finished to appear
				$post_args['event_end_after'] = 'now';

		}

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'site_id' => $site_id,
			'options_array' => $options_array,
			'post_args' => $post_args,
			//'backtrace' => $trace,
		), true ) );
		*/

		// get recent posts for this site
		//$recent_posts = wp_get_recent_posts( $post_args );
		$recent_posts = eo_get_events( $post_args );


		/*
		//$e = new Exception;
		//$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'recent_posts' => $recent_posts,
			//'backtrace' => $trace,
		), true ) );
		*/

		// Put all the posts in a single array
		foreach( $recent_posts as $post => $post_data ) {

			// convert to ARRAY_A
			$post_detail = get_object_vars( $post_data );

			$post_id = $post_detail['ID'];
			$author_id = $post_detail['post_author'];

			// Prefix the array key with event start date
			$prefix = get_post_meta( $post_id, '_eventorganiser_schedule_start_start', true ) . '-' . $post_detail['post_name'];

			// Returns an array
			$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );

			if ( $post_detail['post_excerpt'] ) {
				$excerpt = $post_detail['post_excerpt'];
			} else {
				$excerpt = wp_trim_words(
					$post_detail['post_content'],
					$excerpt_length,
					sprintf( __( '... <a href="%s">Read More</a>', 'wp-network-content-display' ), get_permalink( $post_id ) )
				);
			}

			$post_list[$prefix] = array(
				'post_id' => $post_id,
				'post_title' => $post_detail['post_title'],
				'post_date' => $post_detail['post_date'],
				'post_author' => get_the_author_meta( 'display_name', $post_detail['post_author'] ),
				'post_content' => $post_detail['post_content'],
				'post_excerpt' => strip_shortcodes( $excerpt ),
				'permalink' => get_permalink( $post_id ),
				'post_image' => $post_thumbnail[0],
				'post_class' => get_post_class( 'siteid-' . $site_id, $post_id ),
				'post_type' => $post_type,
				'site_id' => $site_id,
				'site_name' => $site_details->blogname,
				'site_link' => $site_details->siteurl,
			);

			// start and end
			$post_list[$prefix]['event_start_date'] = get_post_meta( $post_id, '_eventorganiser_schedule_start_start', true );
			$post_list[$prefix]['event_end_date'] = get_post_meta( $post_id, '_eventorganiser_schedule_start_finish', true );

			// venue
			$venue_id = $post_list[$prefix]['venue_id'] = eo_get_venue( $post_id );

			// if the event has a venue
			if ( ! empty( $venue_id ) ) {
				$post_list[$prefix]['venue_link'] = eo_get_venue_link( $venue_id );
				$post_list[$prefix]['venue_name'] = eo_get_venue_name( $venue_id );
				$post_list[$prefix]['venue_address'] = eo_get_venue_address( $venue_id );
				$post_list[$prefix]['venue_lat'] = eo_get_venue_meta( $venue_id, '_lat' );
				$post_list[$prefix]['venue_long'] = eo_get_venue_meta( $venue_id, '_lng' );
			}

			// Get post categories
			$event_categories = wp_get_post_terms( $post_id, 'event-category', array( "fields" => "all" ) );
			foreach( $event_categories as $event_category ) {
				$post_list[$prefix]['categories'][$event_category->slug] = $event_category->name;
			}

			// Get post tags
			$event_tags = wp_get_post_terms( $post_id, 'event-tag', array( "fields" => "all" ) );
			foreach( $event_tags as $event_tag ) {
				$post_list[$prefix]['tags'][$event_tag->slug] = $event_tag->name;
			}

		}

		/*
		//$e = new Exception;
		//$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'post_list' => $post_list,
			//'backtrace' => $trace,
		), true ) );
		*/

		// --<
		return $post_list;

	}



	/**
	 * Get Event meta data.
	 *
	 * NOT USED
	 *
	 * @param int $event_id The numeric ID of the event.
	 * @return str $html The formatted event meta.
	 */
	public function get_taxonomies( $event_id = 0 ) {

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

		$html .= '</div>';

		return $html;

	}



	/************* RENDERING METHODS *****************/



	/**
	 * Render an array of events as an HTML list.
	 *
	 * NOT USED
	 *
	 * @param array $events_array An array of events data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $html The data rendered as an HTML list.
	 */
	public function _render_html( $events_array, $options_array ) {

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
	 *    'show_meta' => true, // boolean
	 *    'post_type' => 'event',
	 * @return str $html The data rendered as an HTML list.
	 */
	public function _render_html_list( $events_array, $options_array ) {

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		// Convert strings to booleans
		$show_meta = ( filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) );

		$html = '<ul class="network-event-list ' . $post_type . '-list">';

		// find template
		$template = WPNCD_Helpers::find_template( 'event-list.php' );

		foreach( $events_array as $key => $post_detail ) {

			global $post;

			$post_id = $post_detail['post_id'];

			$venue_id = $post_detail['event_venue']['venue_id'];
			$venue_name = $post_detail['event_venue']['venue_name'];
			$venue_link = $post_detail['event_venue']['venue_link'];
			$venue_address = $post_detail['event_venue']['venue_location'];

			$post_class = ( ! empty( $post_detail['post_class'] ) ) ? $post_detail['post_class'] : 'event list-item';

			// prevent immediate output
			ob_start();

			// use template
			include( $template );

			// grab markup
			$html .= ob_get_contents();

			// clean up
			ob_end_clean();

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
	public function _render_html_block( $posts_array, $options_array ) {

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		$html = '<div class="network-posts-list style-' . $style . '">';

		// find template
		$template = WPNCD_Helpers::find_template( 'event-block.php' );

		foreach( $posts_array as $post => $post_detail ) {

			global $post;

			$post_id = $post_detail['post_id'];

			// Convert strings to booleans
			$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : true;
			$show_thumbnail = ( ! empty( $show_thumbnail ) ) ? filter_var( $show_thumbnail, FILTER_VALIDATE_BOOLEAN ) : false;
			$show_site_name = ( ! empty( $show_site_name ) ) ? filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) : true;

			$venue_id = $post_detail['event_venue']['venue_id'];
			$venue_name = $post_detail['event_venue']['venue_name'];
			$venue_link = $post_detail['event_venue']['venue_link'];
			$venue_address = $post_detail['event_venue']['venue_location'];

			$post_class = ( $post_detail['post_class'] ) ? $post_detail['post_class'] : 'post entry event event-item hentry';
			$categories = ( isset( $post_detail['categories'] ) ) ? implode( ", ", $post_detail['categories'] ) : '';

			// prevent immediate output
			ob_start();

			// use template
			include( $template );

			// grab markup
			$html .= ob_get_contents();

			// clean up
			ob_end_clean();

		}

		$html .= '</div>';

		return $html;

	}



} // end class WP_Network_Content_Display_Events
