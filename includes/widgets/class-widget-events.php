<?php
/**
 * Network Events Display Widget
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.6.0
 * @package   WP_Network_Content_Display
 */



/**
 * Creates a custom Widget for displaying a list of events.
 *
 * @since 1.6.0
 */
class WP_Network_Content_Display_Events_Widget extends WP_Widget {



	/**
	 * Constructor registers widget with WordPress.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// init parent
		parent::__construct(

			// base ID
			'wpncd-network-events',

			// title
			__( 'Network Events', 'wp-network-content-display' ),

			// args
			array(
				'description' => __( 'Display list of events from your network.', 'wp-network-content-display' ),
				//'classname'	 => 'widget_wpncd-network-events',
			)

		);

	}



	/**
	 * Outputs the HTML for this widget.
	 *
	 * @since 1.6.0
	 *
	 * @param array $args An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		// Convert $exclude_sites array to comma-separated string
		if ( isset( $instance['exclude_sites'] ) && is_array( $instance['exclude_sites'] ) ) {
			$instance['exclude_sites'] = implode( ',', $instance['exclude_sites'] );
		} else {
			unset( $instance['exclude_sites'] );
		}

		// Convert arrays to comma-separated strings
		if ( is_array( $instance['include_categories'] ) && ( ! empty( $instance['include_categories'][0] ) ) ) {
			$instance['include_categories'] = implode( ',', $instance['include_categories'] );
		} else {
			unset( $instance['include_categories'] );
		}
		if ( isset( $instance['include_tags'] ) && is_array( $instance['include_tags'] ) ) {
			$instance['include_tags'] = implode( ',', $instance['include_tags'] );
		} else {
			unset( $instance['include_tags'] );
		}

		$instance['post_type'] = 'event';

		echo $before_widget;

		// if the title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// display events
		echo wp_network_content_display()->components->events->get_posts_from_network( $instance );

		echo $after_widget;

	}



	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 * @since 1.6.0
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		// Set default values
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'number_posts' => '10',
			'exclude_sites' => '',
			'style' => 'list',
			'posts_per_site' => '5',
			'show_meta' => true,
			'show_thumbnail' => false,
			'show_excerpt' => true,
			'excerpt_length' => 20,
			'show_site_name' => false,
			'event_scope' => 'future',
			'include_categories' => array(),
			'include_tags' => array(),
		) );

		// Retrieve an existing value from the database
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$number_posts = ! empty( $instance['number_posts'] ) ? $instance['number_posts'] : '10';
		$exclude_sites = ! empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : array();
		$style = ! empty( $instance['style'] ) ? $instance['style'] : 'list';
		$posts_per_site = ! empty( $instance['posts_per_site'] ) ? $instance['posts_per_site'] : '5';
		$show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : true;
		$show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : false;
		$show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : true;
		$excerpt_length = ! empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '20';
		$show_site_name = isset( $instance['show_site_name'] ) ? (bool) $instance['show_site_name'] : false;
		$event_scope = ! empty( $instance['event_scope'] ) ? $instance['event_scope'] : 'future';

		$include_categories = ! empty( $instance['include_categories'] ) ? $instance['include_categories'] : array();
		$include_tags = ! empty( $instance['include_tags'] ) ? $instance['include_tags'] : array();

		// init query args
		$site_args = array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		);

		/**
		 * Apply plugin-wide $site_args filter.
		 *
		 * @since 2.0.0
		 *
		 * @param array $site_args The arguments used to query the sites.
		 */
		$site_args = apply_filters( 'wpncd_filter_site_args', $site_args );

		/**
		 * Allow the $site_args to be specifically filtered here.
		 *
		 * @since 2.0.0
		 *
		 * @param array $site_args The arguments used to query the sites.
		 */
		$site_args = apply_filters( 'wpncd_widget_form_sites_for_events_args', $site_args );

		// get sites
		$sites = get_sites( $site_args );

		// get categories and tags
		$categories = wp_network_content_display()->components->events->get_network_terms( 'event-category' );
		$tags = wp_network_content_display()->components->events->get_network_terms( 'event-tag' );

		$scopes = array(
			'future' => __( 'Future', 'wp-network-content-display' ),
			'past' => __( 'Past', 'wp-network-content-display' ),
			'all' => __( 'All', 'wp-network-content-display' ),
		);

		$styles = array(
			'list' => __( 'List (Default)', 'wp-network-content-display' ),
			'block' => __( 'Block', 'wp-network-content-display' )
		);

		// include form template
		include( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/widget-form-events.php' );

	}



	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 * @since 1.6.0
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array $instance Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number_posts'] = ! empty( $new_instance['number_posts'] ) ? strip_tags( $new_instance['number_posts'] ) : '10';
		$instance['show_meta'] = ! empty( $new_instance['show_meta'] ) ? true : false;
		$instance['show_thumbnail'] = ! empty( $new_instance['show_thumbnail'] ) ? true : false;
		$instance['style'] = ! empty( $new_instance['style'] ) ? $new_instance['style'] : 'list';
		$instance['posts_per_site'] = ! empty( $new_instance['posts_per_site'] ) ? strip_tags( $new_instance['posts_per_site'] ) : '5';
		$instance['event_scope'] = ! empty( $new_instance['event_scope'] ) ? $new_instance['event_scope'] : 'future';
		$instance['show_excerpt'] = ! empty( $new_instance['show_excerpt'] ) ? true : false;
		$instance['excerpt_length'] = ! empty( $new_instance['excerpt_length'] ) ? strip_tags( $new_instance['excerpt_length'] ) : 20;
		$instance['show_site_name'] = ! empty( $new_instance['show_site_name'] ) ? true : false;

		// now handle multi-selects - these may be pseudo-empty arrays
		$instance['exclude_sites'] = WPNCD_Helpers::sanitize_pseudo_array( $new_instance['exclude_sites'] );
		$instance['include_categories'] = WPNCD_Helpers::sanitize_pseudo_array( $new_instance['include_categories'] );
		$instance['include_tags'] = WPNCD_Helpers::sanitize_pseudo_array( $new_instance['include_tags'] );

		return $instance;

	}



} // end class WP_Network_Content_Display_Events_Widget
