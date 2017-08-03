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
	 * Enqueue the Javascripts for the media uploader.
	 *
	 * @since 1.0.0
	 */
	public function upload_scripts( ) {

		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'upload_media_widget', WP_NETWORK_CONTENT_DISPLAY_URL . 'js/upload-media.js', array( 'jquery' ) );

		wp_enqueue_style( 'thickbox' );

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

		// Use glocal_networkwide_sites function to display sites
		if ( function_exists( 'glocal_networkwide_sites_module' ) ) {
			echo glocal_networkwide_sites_module( $instance );
		}

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

		// Form fields
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'title' ) . '" class="title_label">' . __( 'Title', 'wp-network-content-display' ) . '</label>';
		echo '	<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Widget Title', 'wp-network-content-display' ) . '" value="' . esc_attr( $title ) . '">';
		echo '</p>';

		// Number of Sites
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'number_sites' ) . '" class="number_sites_label">' . __( 'Number of Sites', 'wp-network-content-display' ) . '</label>';
		echo '	<input type="number" id="' . $this->get_field_id( 'number_sites' ) . '" name="' . $this->get_field_name( 'number_sites' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'wp-network-content-display' ) . '" value="' . esc_attr( $number_sites ) . '">';
		echo '</p>';

		// Exclude Sites
		echo '<p>';
		echo '	<label for="exclude_sites" class="exclude_sites_label">' . __( 'Exclude Sites', 'wp-network-content-display' ) . '</label>';
		echo '	<select id="' . $this->get_field_id( 'exclude_sites' ) . '" name="' . $this->get_field_name( 'exclude_sites' ) . '[]" multiple="multiple" class="widefat">';
		echo '		<option value="" ' . selected( $exclude_sites, '', false ) . '> ' . __( 'None', 'wp-network-content-display' );

		$siteargs = array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		);

		$sites = get_sites( $siteargs );

		foreach( $sites as $site ) {
			$site_id = $site->blog_id;
			$site_name = get_blog_details( $site_id )->blogname;
			echo '		<option id="' . $site_id . '" value="' . $site_id . '"', ( ! empty( $exclude_sites ) && in_array( $site_id,	$exclude_sites ) ) ? ' selected="selected"' : '','>' . $site_name . '</option>';
		}

		echo '	</select>';
		echo '</p>';

		// Sort by
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'sort_by' ) . '" class="sort_by_label">' . __( 'Sort By', 'wp-network-content-display' ) . '</label>';
		echo '	<select id="' . $this->get_field_id( 'sort_by' ) . '" name="' . $this->get_field_name( 'sort_by' ) . '" class="widefat">';
		echo '		<option value="blogname" ' . selected( $sort_by, 'blogname', false ) . '> ' . __( 'Alphabetical', 'wp-network-content-display' );
		echo '		<option value="last_updated" ' . selected( $sort_by, 'last_updated', false ) . '> ' . __( 'Recently Active', 'wp-network-content-display' );
		echo '		<option value="post_count" ' . selected( $sort_by, 'post_count', false ) . '> ' . __( 'Most Active', 'wp-network-content-display' );
		echo '		<option value="registered" ' . selected( $sort_by, 'registered', false ) . '> ' . __( 'Newest', 'wp-network-content-display' );
		echo '	</select>';
		echo '</p>';

		// Widget ID
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'id' ) . '" class="id_label">' . __( 'ID', 'wp-network-content-display' ) . '</label>';
		echo '	<input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter ID', 'wp-network-content-display' ) . '" value="' . esc_attr( $id ) . '">';
		echo '</p>';

		// Widget Class
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'class' ) . '" class="class_label">' . __( 'Class', 'wp-network-content-display' ) . '</label>';
		echo '	<input type="text" id="' . $this->get_field_id( 'class' ) . '" name="' . $this->get_field_name( 'class' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Class', 'wp-network-content-display' ) . '" value="' . esc_attr( $class ) . '">';
		echo '</p>';

		// Default Meta
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'show_meta' ) . '" class="show_meta_label">' . __( 'Show Meta', 'wp-network-content-display' ) . '</label>';
		echo '	<input type="checkbox" id="' . $this->get_field_id( 'show_meta' ) . '" name="' . $this->get_field_name( 'show_meta' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'wp-network-content-display' ) . '" value="1" ' . checked( $show_meta, true, false ) . '>';
		echo '</p>';

		// Show Image
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'show_image' ) . '" class="show_image_label">' . __( 'Show Site Image', 'wp-network-content-display' ) . '</label>';
		echo '	<input type="checkbox" id="' . $this->get_field_id( 'show_image' ) . '" name="' . $this->get_field_name( 'show_image' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'wp-network-content-display' ) . '" value="1" ' . checked( $show_image, true, false ) . '>';
		echo '</p>';

		// Default Image
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'default_image' ) . '" class="default_image_label">' . __( 'Default Image', 'wp-network-content-display' ) . '</label>';
		echo '	<input type="text" id="' . $this->get_field_id( 'default_image' ) . '" name="' . $this->get_field_name( 'default_image' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter path/url of default image', 'wp-network-content-display' ) . '" value="' . esc_url( $default_image ) . '">';
		echo '	<input class="upload_image_button button button-primary" type="button" value="Upload Image" />';
		echo '</p>';

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



} // end class WP_Network_Content_Display_Sites_Widget
