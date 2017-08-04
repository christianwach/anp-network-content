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
			'glocal-network-events',

			// title
			__( 'Network Events', 'wp-network-content-display' ),

			// args
			array(
				'description' => __( 'Display list of events from your network.', 'wp-network-content-display' ),
				'classname'	 => 'widget__glocal-network-events',
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
			'number_posts' => '',
			'exclude_sites' => '',
			'style' => '',
			'id' => '',
			'class' => '',
			'show_meta' => true,
			'show_site_name' => true,
			'event_scope' => '',
			'include_categories' => array(),
			'include_tags' => array(),
		) );

		// Retrieve an existing value from the database
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		// $post_type = 'event';
		$number_posts = ! empty( $instance['number_posts'] ) ? $instance['number_posts'] : '';
		$exclude_sites = ! empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : array();
		$style = ! empty( $instance['style'] ) ? $instance['style'] : '';
		$id = ! empty( $instance['id'] ) ? $instance['id'] : '';
		$class = ! empty( $instance['class'] ) ? $instance['class'] : '';
		$show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
		$excerpt_length = ! empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '';
		$show_site_name = isset( $instance['show_site_name'] ) ? (bool) $instance['show_site_name'] : false;
		$event_scope = ! empty( $instance['event_scope'] ) ? $instance['event_scope'] : '';

		$include_categories = ! empty( $instance['include_categories'] ) ? $instance['include_categories'] : array();
		$include_tags = ! empty( $instance['include_tags'] ) ? $instance['include_tags'] : array();

		// get sites
		$sites = get_sites( array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		) );

		// get categories and tags
		$categories = wp_network_content_display()->components->events->get_network_event_terms( 'event-category' );
		$tags = wp_network_content_display()->components->events->get_network_event_terms( 'event-tag' );

		$scopes = array(
			'future' => __( 'Future', 'wp-network-content-display' ),
			'past' => __( 'Past', 'wp-network-content-display' ),
			'all' => __( 'All', 'wp-network-content-display' ),
		);

		$styles = array(
			'' => __( 'List (Default)', 'wp-network-content-display' ),
			'block' => __( 'Block', 'wp-network-content-display' )
		);

		///*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'include_categories' => $include_categories,
			'include_tags' => $include_tags,
			//'sites' => $sites,
			//'backtrace' => $trace,
		), true ) );
		//*/

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
		$instance['number_posts'] = ! empty( $new_instance['number_posts'] ) ? strip_tags( $new_instance['number_posts'] ) : '';
		$instance['exclude_sites'] = ! empty( $new_instance['exclude_sites'] ) ? $new_instance['exclude_sites'] : '';
		$instance['include_categories'] = ! empty( $new_instance['include_categories'] ) ? $new_instance['include_categories'] : '';
		$instance['include_tags'] = ! empty( $new_instance['include_tags'] ) ? $new_instance['include_tags'] : '';
		$instance['show_meta'] = ! empty( $new_instance['show_meta'] ) ? true : false;
		$instance['style'] = ! empty( $new_instance['style'] ) ? $new_instance['style'] : '';
		$instance['event_scope'] = ! empty( $new_instance['event_scope'] ) ? $new_instance['event_scope'] : '';
		$instance['id'] = ! empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
		$instance['class'] = ! empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
		$instance['excerpt_length'] = ! empty( $new_instance['excerpt_length'] ) ? strip_tags( $new_instance['excerpt_length'] ) : 20;
		$instance['show_site_name'] = ! empty( $new_instance['show_site_name'] ) ? true : false;

		return $instance;

	}



} // end class WP_Network_Content_Display_Events_Widget
