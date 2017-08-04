<?php
/**
 * Network Sites Display Widget.
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.0
 * @package   WP_Network_Content_Display
 */



/**
 * Creates a custom Widget for displaying a list of sites.
 *
 * @since 1.0.0
 */
class WP_Network_Content_Display_Sites_Widget extends WP_Widget {



	/**
	 * Constructor registers widget with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// init parent
		parent::__construct(

			// base ID
			'glocal-network-sites',

			// title
			__( 'Network Sites', 'wp-network-content-display' ),

			// args
			array(
				'description' => __( 'Display list of sites in your network.', 'wp-network-content-display' ),
				'classname'	 => 'widget__glocal-network-sites',
			)

		);

		add_action( 'admin_enqueue_scripts', array( $this, 'upload_scripts' ) );

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

		// Convert array to comma-separated string
		if ( is_array( $instance['exclude_sites'] ) && ( ! empty( $instance['exclude_sites'][0] ) ) ) {
			$instance['exclude_sites'] = implode( ',', $instance['exclude_sites'] );
		} else {
			unset( $instance['exclude_sites'] );
		}

		echo $before_widget;

		// if the title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// display sites
		echo wp_network_content_display()->components->sites->get_network_sites( $instance );

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
			'number_sites' => '',
			'exclude_sites' => '',
			'sort_by' => '',
			'id' => '',
			'class' => '',
			'show_meta' => true,
			'show_image' => false,
			'default_image' => '',
		) );

		// Retrieve an existing value from the database
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$number_sites = ! empty( $instance['number_sites'] ) ? $instance['number_sites'] : '';
		$exclude_sites = ! empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : '';
		$sort_by = ! empty( $instance['sort_by'] ) ? $instance['sort_by'] : '';

		$id = ! empty( $instance['id'] ) ? $instance['id'] : '';
		$class = ! empty( $instance['class'] ) ? $instance['class'] : '';
		$show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
		$show_image = isset( $instance['show_image'] ) ? (bool) $instance['show_image'] : false;
		$default_image = ! empty( $instance['default_image'] ) ? $instance['default_image'] : '';

		// get sites
		$sites = get_sites( array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		) );

		// include form template
		include( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/widget-form-sites.php' );

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
		$instance['number_sites'] = ! empty( $new_instance['number_sites'] ) ? strip_tags( $new_instance['number_sites'] ) : '';
		$instance['exclude_sites'] = ! empty( $new_instance['exclude_sites'] ) ? $new_instance['exclude_sites'] : '';
		$instance['sort_by'] = ! empty( $new_instance['sort_by'] ) ? $new_instance['sort_by'] : '';
		$instance['id'] = ! empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
		$instance['class'] = ! empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
		$instance['show_meta'] = ! empty( $new_instance['show_meta'] ) ? true : false;
		$instance['show_image'] = ! empty( $new_instance['show_image'] ) ? true : false;
		$instance['default_image'] = ! empty( $new_instance['default_image'] ) ? strip_tags( $new_instance['default_image'] ) : '';

		return $instance;

	}



	/**
	 * Enqueue what we need for the WordPress Media Uploader.
	 *
	 * @since 1.0.0
	 */
	public function upload_scripts() {

		// enable media uploads
		wp_enqueue_media();

	}



} // end class WP_Network_Content_Display_Sites_Widget
