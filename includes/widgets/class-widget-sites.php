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
			'wpncd-network-sites',

			// title
			__( 'Network Sites', 'wp-network-content-display' ),

			// args
			array(
				'description' => __( 'Display list of sites in your network.', 'wp-network-content-display' ),
				//'classname'	 => 'widget_wpncd-network-sites',
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
			'number_sites' => '20',
			'exclude_sites' => array(),
			'sort_by' => 'blogname',
			'style' => 'list',
			'show_meta' => false,
			'show_image' => false,
			'default_image' => '',
			'attachment_id' => '',
		) );

		// Retrieve an existing value from the database
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$number_sites = ! empty( $instance['number_sites'] ) ? $instance['number_sites'] : '20';
		$exclude_sites = ! empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : array();
		$sort_by = ! empty( $instance['sort_by'] ) ? $instance['sort_by'] : 'blogname';

		$style = ! empty( $instance['style'] ) ? $instance['style'] : 'list';
		$show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
		$show_image = isset( $instance['show_image'] ) ? (bool) $instance['show_image'] : false;
		$default_image = ! empty( $instance['default_image'] ) ? $instance['default_image'] : '';
		$attachment_id = ! empty( $instance['attachment_id'] ) ? $instance['attachment_id'] : '';

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
		$instance['number_sites'] = ! empty( $new_instance['number_sites'] ) ? strip_tags( $new_instance['number_sites'] ) : '20';
		$instance['sort_by'] = ! empty( $new_instance['sort_by'] ) ? $new_instance['sort_by'] : 'blogname';
		$instance['style'] = ! empty( $new_instance['style'] ) ? $new_instance['style'] : 'list';
		$instance['show_meta'] = ! empty( $new_instance['show_meta'] ) ? true : false;
		$instance['show_image'] = ! empty( $new_instance['show_image'] ) ? true : false;
		$instance['default_image'] = ! empty( $new_instance['default_image'] ) ? strip_tags( $new_instance['default_image'] ) : '';
		$instance['attachment_id'] = ! empty( $new_instance['attachment_id'] ) ? strip_tags( $new_instance['attachment_id'] ) : '';

		// now handle multi-selects - these may be pseudo-empty arrays
		$instance['exclude_sites'] = WPNCD_Helpers::sanitize_pseudo_array( $new_instance['exclude_sites'] );

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

		// enqueue our script
		wp_enqueue_script(
			'wpncd-widget-upload',
			WP_NETWORK_CONTENT_DISPLAY_URL . '/assets/js/upload-media.js',
			array( 'jquery' ),
			WP_NETWORK_CONTENT_DISPLAY_VERSION
		);

		// translations
		$localisation = array(
			'modal_title' => __( 'Choose an image', 'wp-network-content-display' ),
			'modal_submit' => __( 'Use this image', 'wp-network-content-display' ),
		);

		// localise the WordPress way
		wp_localize_script(
			'wpncd-widget-upload',
			'WP_Network_Content_Display_Settings',
			$localisation
		);

	}



} // end class WP_Network_Content_Display_Sites_Widget
