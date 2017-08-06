<?php
/**
 * Network Posts Display Widget.
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.0
 * @package   WP_Network_Content_Display
 */



/**
 * Creates a custom Widget for displaying a list of posts.
 *
 * @since 1.0.0
 */
class WP_Network_Content_Display_Posts_Widget extends WP_Widget {



	/**
	 * Constructor registers widget with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// init parent
		parent::__construct(

			// base ID
			'wpncd-network-posts',

			// title
			__( 'Network Posts', 'wp-network-content-display' ),

			// args
			array(
				'description' => __( 'Display list of posts from your network.', 'wp-network-content-display' ),
				//'classname'	 => 'widget_wpncd-network-posts',
			)

		);

	}



	/**
	 * Outputs the HTML for this widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		// Convert arrays to comma-separated string
		if ( is_array( $instance['exclude_sites'] ) && ( ! empty( $instance['exclude_sites'][0] ) ) ) {
			$instance['exclude_sites'] = implode( ',', $instance['exclude_sites'] );
		} else {
			unset( $instance['exclude_sites'] );
		}
		if ( is_array( $instance['include_categories'] ) && ( ! empty( $instance['include_categories'][0] ) ) ) {
			$instance['include_categories'] = implode( ',', $instance['include_categories'] );
		} else {
			unset( $instance['include_categories'] );
		}

		echo $before_widget;

		// if the title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// display posts
		echo wp_network_content_display()->components->posts->get_posts_from_network( $instance );

		echo $after_widget;

	}



	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 * @since 1.0.0
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		// Set default values
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'number_posts' => '',
			'exclude_sites' => array(),
			'include_categories' => array(),
			'style' => '',
			'posts_per_site' => '',
			'show_meta' => true,
			'show_thumbnail' => false,
			'show_excerpt' => true,
			'excerpt_length' => 20,
			'show_site_name' => true,
		) );

		// Retrieve an existing value from the database
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$number_posts = ! empty( $instance['number_posts'] ) ? $instance['number_posts'] : '';
		$exclude_sites = ! empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : array();
		$include_categories = ! empty( $instance['include_categories'] ) ? $instance['include_categories'] : array();
		$style = ! empty( $instance['style'] ) ? $instance['style'] : '';
		$posts_per_site = ! empty( $instance['posts_per_site'] ) ? $instance['posts_per_site'] : '';
		$show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
		$show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : false;
		$show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
		$excerpt_length = ! empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '';
		$show_site_name = isset( $instance['show_site_name'] ) ? (bool) $instance['show_site_name'] : false;

		// get sites
		$sites = get_sites( array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		) );

		// get categories
		$categories = wp_network_content_display()->components->events->get_network_terms( 'category' );

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'include_categories' => $include_categories,
			'categories' => $categories,
			'sites' => $sites,
			//'backtrace' => $trace,
		), true ) );
		*/

		// include form template
		include( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/widget-form-posts.php' );

	}



	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 * @since 1.0.0
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array $instance Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number_posts'] = ! empty( $new_instance['number_posts'] ) ? strip_tags( $new_instance['number_posts'] ) : '';
		$instance['style'] = ! empty( $new_instance['style'] ) ? $new_instance['style'] : '';
		$instance['posts_per_site'] = ! empty( $new_instance['posts_per_site'] ) ? strip_tags( $new_instance['posts_per_site'] ) : '';
		$instance['show_meta'] = ! empty( $new_instance['show_meta'] ) ? true : false;
		$instance['show_thumbnail'] = ! empty( $new_instance['show_thumbnail'] ) ? true : false;
		$instance['show_excerpt'] = ! empty( $new_instance['show_excerpt'] ) ? true : false;
		$instance['excerpt_length'] = ! empty( $new_instance['excerpt_length'] ) ? strip_tags( $new_instance['excerpt_length'] ) : 20;
		$instance['show_site_name'] = ! empty( $new_instance['show_site_name'] ) ? true : false;

		// now handle multi-selects - these may be pseudo-empty arrays
		$instance['exclude_sites'] = WPNCD_Helpers::sanitize_pseudo_array( $new_instance['exclude_sites'] );
		$instance['include_categories'] = WPNCD_Helpers::sanitize_pseudo_array( $new_instance['include_categories'] );

		return $instance;

	}



} // end class WP_Network_Content_Display_Posts_Widget
