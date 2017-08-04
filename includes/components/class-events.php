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



	/**
	 * Get sitewide taxonomy terms.
	 *
	 * @param str $taxonomy The name of of the taxonomy.
	 * @param array $exclude_sites The sites to exclude.
	 * @return array $term_list The array of terms with unique taxonomy term slugs and names.
	 */
	public function get_sitewide_taxonomy_terms( $taxonomy, $exclude_sites = null ) {

		// Site statuses to include
		$site_args = array(
			'limit' => null,
			'public' => 1,
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'mature' => null,
		);

		// Allow the $site_args to be changed
		if ( has_filter( 'glocal_network_tax_term_siteargs_arguments' ) ) {
			$site_args = apply_filters( 'glocal_network_tax_term_siteargs_arguments', $site_args );
		}

		$sites_list = ( $exclude_sites ) ? glocal_exclude_sites( $exclude_sites ) : get_sites( $site_args );

		$term_args = array();

		// Allow the $site_args to be changed
		if ( has_filter( 'glocal_network_tax_termarg_arguments' ) ) {
			$term_args = apply_filters( 'glocal_network_tax_termarg_arguments', $term_args );
		}

		$term_list = array();

		foreach( $sites_list as $site ) {

			// Switch to the site to get details and posts
			switch_to_blog( $site->blog_id );

			$site_terms = get_terms( $taxonomy, $term_args );

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
	public function glocal_get_event_taxonomy( $event_id = 0 ) {

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




	/**
	 * Render an array of events as an HTML list.
	 *
	 * NOT USED
	 *
	 * @param array $events_array An array of events data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $html The data rendered as an HTML list.
	 */
	public function render_events_list( $events_array, $options_array ) {

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
	public function render_event_list_html( $events_array, $options_array ) {

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		// Convert strings to booleans
		$show_meta = ( filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) );

		$html = '<ul class="network-event-list ' . $post_type . '-list">';

		// find template
		$template = WP_Network_Content_Display_Helpers::find_template( 'event-list.php' );

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
	public function render_event_block_html( $posts_array, $options_array ) {

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		$html = '<div class="network-posts-list style-' . $style . '">';

		// find template
		$template = WP_Network_Content_Display_Helpers::find_template( 'event-block.php' );

		foreach( $posts_array as $post => $post_detail ) {

			global $post;

			$post_id = $post_detail['post_id'];
			$post_categories = ( isset( $post_detail['categories'] ) ) ? implode( ", ", $post_detail['categories'] ) : '';

			// Convert strings to booleans
			$show_meta = ( filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) );
			$show_thumbnail = ( filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) );
			$show_site_name = ( filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) );

			$venue_id = $post_detail['event_venue']['venue_id'];
			$venue_name = $post_detail['event_venue']['venue_name'];
			$venue_link = $post_detail['event_venue']['venue_link'];
			$venue_address = $post_detail['event_venue']['venue_location'];

			$post_class = ( $post_detail['post_class'] ) ? $post_detail['post_class'] : 'post entry event event-item hentry';

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
