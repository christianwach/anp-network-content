<?php

/**
 * Network Content Widgets and Shortcodes Posts Class.
 *
 * A class that encapsulates Posts functionality.
 *
 * @since 2.0.0
 */
class WP_Network_Content_Display_Posts {

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
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/shortcodes/class-shortcode-posts.php' );

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

		// instantiate Shortcode class
		$this->shortcode = new WP_Network_Content_Display_Posts_Shortcode;

		// we're done
		$done = true;

	}



	/**
	 * Register widgets for this component.
	 *
	 * @since 2.0.0
	 */
	public function register_widgets() {

		// register Network Posts Widget
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/class-widget-posts.php' );
		register_widget( 'WP_Network_Content_Display_Posts_Widget' );

	}



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
	public function get_network_posts( $parameters = array() ) {

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
		$sites_list = glocal_get_sites_list( $settings );

		// CALL GET POSTS FUNCTION
		$posts_list = $this->get_posts_list( $sites_list, $settings );

		if ( $output == 'array' ) {

			// Return an array
			return $posts_list;

		} else {

			// CALL RENDER FUNCTION
			return render_html( $posts_list, $settings );

		}

	}



	/************* GET CONTENT FUNCTIONS *****************/



	/**
	 * Get an array of posts from the specified sites.
	 *
	 * @param array $sites_array The array of sites to include.
	 * @param array $options_array The options for post retrieval.
	 * @return array $post_list The array of posts with site information, sorted by post_date.
	 */
	public function get_posts_list( $sites_array, $options_array ) {

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

			// If $this->get_sites_posts( $site_id, $settings ) isn't null, add it to the array, else skip it
			// Trying to add a null value to the array using this syntax produces a fatal error.

			$site_posts = $this->get_sites_posts( $site_id, $settings );

			if ( $this->get_sites_posts( $site_id, $settings ) ) {
				$post_list = $post_list + $this->get_sites_posts( $site_id, $settings );
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
	public function get_sites_posts( $site_id, $options_array ) {

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



} // end class WP_Network_Content_Display_Posts
