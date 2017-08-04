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
	 * Get (or render) posts from sites across the network.
	 *
	 * 1/5/2016: Updated to allow for custom post types.
	 *
	 * @param array $parameters An array of settings with the following options:
	 *    post_type (string) - post type to display ( default: 'post' )
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
	 *    show_thumbnail (bool) - display post thumbnail ( default: false ) - ignored if @output is 'array'
	 *    show_meta (bool) - if meta info should be displayed ( default: true ) - ignored if @output is 'array'
	 *    show_excerpt (bool) - if excerpt should be displayed ( default: true ) - ignored if @output is 'array' or if @show_meta is false
	 *    excerpt_length (int) - number of words to display for excerpt ( default: 50 ) - ignored if @show_excerpt is false
	 *    show_site_name (bool) - if site name should be displayed ( default: true ) - ignored if @output is 'array'
	 * @return array $posts_list The array of posts.
	 */
	public function get_posts_from_network( $parameters = array() ) {

		// Default parameters
		$defaults = array(
			'post_type' => (string) 'post', // (string) - post, page
			'number_posts' => (int) 10, // (int)
			'exclude_sites' => array(),
			'include_categories' => array(),
			'posts_per_site' => (int) null, // (int)
			'output' => (string) 'html', // (string) - html, array
			'style' => (string) 'normal', // (string) - normal
			'id' => (string) 'network-posts-' . rand(), // (string)
			'class' => (string) 'post-list', // (string)
			'title' => (string) __( 'Posts', 'wp-network-content-display' ), // (string)
			'title_image' => (string) null, // (string)
			'show_meta' => (bool) true, // (bool)
			'show_thumbnail' => (bool) false, // (bool)
			'show_excerpt' => (bool) true, // (bool)
			'excerpt_length' => (int) 55, // (int)
			'show_site_name' => (bool) true, // (bool)
		);

		// sanitize params
		$parameters = WPNCD_Helpers::sanitize_input( $parameters );

		// convert comma-delimited list to array
		if ( isset( $parameters['exclude_sites'] ) && ! empty( $parameters['exclude_sites'] ) ) {
			$parameters['exclude_sites'] = explode( ',', $parameters['exclude_sites'] );
		}

		// merge params with defaults
		$settings = WPNCD_Helpers::get_merged_settings( $parameters, $defaults );

		// get sites data
		$sites_list = WPNCD_Helpers::get_data_for_sites( $settings );

		// get posts for those sites
		$posts_list = $this->get_posts_for_sites( $sites_list, $settings );

		// return raw if requested
		if ( $settings['output'] == 'array' ) {
			return $posts_list;
		}

		// return rendered
		return $this->render_html( $posts_list, $settings );

	}



	/************* GET CONTENT FUNCTIONS *****************/



	/**
	 * Get an array of posts from the specified sites.
	 *
	 * @param array $sites_array The array of sites to include.
	 * @param array $options_array The options for post retrieval.
	 * @return array $post_list The array of posts with site information, sorted by post_date.
	 */
	public function get_posts_for_sites( $sites_array, $options_array ) {

		// init return
		$post_list = array();

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		// For each site, get the posts
		foreach( $sites_array as $site ) {

			// Switch to the site to get details and posts
			switch_to_blog( $site['blog_id'] );

			// And add to array of posts
			$site_posts = $this->get_posts_for_site( $site['blog_id'], $options_array );

			if ( is_array( $site_posts ) ) {
				$post_list = $post_list + $site_posts;
			}

			// Unswitch the site
			restore_current_blog();

		}

		// SORT ARRAY
		if ( 'event' === $post_type ) {
			$post_list = WPNCD_Helpers::sort_array_by_key( $post_list, 'event_start_date' );
		} else {
			$post_list = WPNCD_Helpers::sort_by_date( $post_list );
		}

		// CALL LIMIT FUNCTIONS
		$post_list = ( isset( $number_posts ) ) ?
					 WPNCD_Helpers::limit_number_posts( $post_list, $number_posts ) :
					 $post_list;

		// --<
		return $post_list;

	}



	/**
	 * Get an array of posts for a specified site.
	 *
	 * The site will already have been switched to, so all WordPress API calls
	 * will return data for that context.
	 *
	 * @param int $site_id The numeric ID of the site.
	 * @param array $options_array The options for post retrieval.
	 * @return array $post_list The array of posts with site information, sorted by post_date.
	 */
	public function get_posts_for_site( $site_id, $options_array ) {

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'site_id' => $site_id,
			'options_array' => $options_array,
			//'backtrace' => $trace,
		), true ) );
		*/

		// init return
		$post_list = array();

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		$site_details = get_blog_details( $site_id );

		$post_args['post_type'] = ( isset( $post_type ) ) ? $post_type : 'post';
		$post_args['posts_per_page'] = ( isset( $posts_per_page ) ) ? $posts_per_page : 20;
		$post_args['category_name'] = ( isset( $include_categories ) ) ? $include_categories : '';

		// get recent posts for this site
		$recent_posts = wp_get_recent_posts( $post_args );

		// Put all the posts in a single array
		foreach( $recent_posts as $post => $post_detail ) {

			$post_id = $post_detail['ID'];
			$author_id = $post_detail['post_author'];

			// prefix the array key with post date
			$prefix = $post_detail['post_date'] . '-' . $post_detail['post_name'];

			// Returns an array
			$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'medium' );

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

			// Get post categories
			$post_categories = wp_get_post_categories( $post_id );

			foreach( $post_categories as $post_category ) {
				$cat = get_category( $post_category );
				$post_list[$prefix]['categories'][] = $cat->name;
			}

		}

		// --<
		return $post_list;

	}



	/**
	 * Render a list of posts.
	 *
	 * @param array $posts_array An array of posts data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $rendered_html The data rendered as 'normal' or 'highlight' HTML.
	 */
	public function render_html( $posts_array, $options_array ) {

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'posts_array' => $posts_array,
			'options_array' => $options_array,
			//'backtrace' => $trace,
		), true ) );
		*/

		// choose how to render
		if ( ! empty( $options_array['style'] ) ) {
			if( 'list'	== $options_array['style'] ) {
				$rendered_html = $this->render_list_html( $posts_array, $options_array );
			} else {
				 $rendered_html = $this->render_block_html( $posts_array, $options_array );
			}
		} else {
			$rendered_html = $this->render_list_html( $posts_array, $options_array );
		}

		// --<
		return $rendered_html;

	}



	/**
	 * Render an array of posts as an HTML list.
	 *
	 * @param array $posts_array An array of posts data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $html The data rendered as an HTML list.
	 */
	public function render_list_html( $posts_array, $options_array ) {

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		// Convert strings to booleans
		$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_excerpt = ( ! empty( $show_excerpt ) ) ? filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_thumbnail = ( ! empty( $show_thumbnail ) ) ? filter_var( $show_thumbnail, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_site_name = ( ! empty( $show_site_name ) ) ? filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) : '';

		$html = '<ul class="wp-network-posts ' . $post_type . '-list">';

		// find template
		$template = WPNCD_Helpers::find_template( $post_type . '-list.php' );

		foreach( $posts_array as $key => $post_detail ) {

			$post_id = $post_detail['post_id'];

			// get post class
			$post_class = '';
			if ( isset( $post_detail['post_class'] ) AND  is_array( $post_detail['post_class'] ) ) {
				$post_class = ' class="' . implode( ' ', $post_detail['post_class'] ) . '"';
			}

			// get post categories
			$categories = '';
			if ( isset( $post_detail['categories'] ) AND  ! empty( $post_detail['categories'] ) ) {
				$categories = implode( ', ', $post_detail['categories'] );
			}

			// get post tags
			$tags = '';
			if ( isset( $post_detail['tags'] ) AND  ! empty( $post_detail['tags'] ) ) {
				$tags = implode( ', ', $post_detail['tags'] );
			}

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
	 * Render an array of posts as an HTML "block".
	 *
	 * @param array $posts_array An array of posts data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $html The data rendered as an HTML "block".
	 */
	public function render_block_html( $posts_array, $options_array ) {

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		$html = '<div class="wp-network-posts ' . $post_type . '-list">';

		// find template
		$template = WPNCD_Helpers::find_template( $post_type . '-block.php' );

		foreach( $posts_array as $key => $post_detail ) {

			$post_id = $post_detail['post_id'];

			// Convert strings to booleans
			$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : '';
			$show_excerpt = ( ! empty( $show_excerpt ) ) ? filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) : '';
			$show_thumbnail = ( ! empty( $show_thumbnail ) ) ? filter_var( $show_thumbnail, FILTER_VALIDATE_BOOLEAN ) : '';
			$show_site_name = ( ! empty( $show_site_name) ) ? filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) : '';

			// get post class
			$post_class = '';
			if ( isset( $post_detail['post_class'] ) AND  is_array( $post_detail['post_class'] ) ) {
				$post_class = ' class="' . implode( ' ', $post_detail['post_class'] ) . '"';
			}

			// get post categories
			$categories = '';
			if ( isset( $post_detail['categories'] ) AND  ! empty( $post_detail['categories'] ) ) {
				$categories = implode( ', ', $post_detail['categories'] );
			}

			// get post tags
			$tags = '';
			if ( isset( $post_detail['tags'] ) AND  ! empty( $post_detail['tags'] ) ) {
				$tags = implode( ', ', $post_detail['tags'] );
			}

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



	/**
	 * Render an array of posts as "highlights".
	 *
	 * @param array $posts_array An array of posts data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $html The data rendered as "highlights".
	 */
	public function render_highlights_html( $posts_array, $options_array ) {

		// Extract each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		$title_image = ( isset( $title_image ) ) ? 'style="background-image:url(' . $title_image . ')"' : '';

		$html = '';

		// look for template
		$template = WPNCD_Helpers::find_template( 'post-highlights.php' );

		// prevent immediate output
		ob_start();

		// use template
		include( $template );

		// grab markup
		$html .= ob_get_contents();

		// clean up
		ob_end_clean();

		return $html;

	}



} // end class WP_Network_Content_Display_Posts
