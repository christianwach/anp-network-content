<?php
/**
 * ANP Network Events Widget
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.6.0
 * @package   ANP_Network_Content
 */



/**
 * Creates a custom Widget for displaying a list of events.
 *
 * @since 1.6.0
 */
class ANP_Network_Events_Widget extends WP_Widget {



	/**
	 * Constructor registers widget with WordPress.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// init parent
		parent::__construct(

			// base ID
			'anp-network-events',

			// title
			__( 'Network Events', 'anp-network-content' ),

			// args
			array(
				'description' => __( 'Display list of events from your network.', 'anp-network-content' ),
				'classname'	 => 'widget__anp-network-events',
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

		// Convert $include_event_categories array to comma-separated string
		if ( isset( $instance['include_event_categories'] ) && is_array( $instance['include_event_categories'] ) ) {
			$instance['include_event_categories'] = implode( ',', $instance['include_event_categories'] );
		} else {
			unset( $instance['include_event_categories'] );
		}

		// Convert $include_event_tags array to comma-separated string
		if ( isset( $instance['include_event_tags'] ) && is_array( $instance['include_event_tags'] ) ) {
			$instance['include_event_tags'] = implode( ',', $instance['include_event_tags'] );
		} else {
			unset( $instance['include_event_tags'] );
		}

		$instance['post_type'] = 'event';

		echo $before_widget;

		// if the title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// Use glocal_networkwide_sites function to display sites
		if ( function_exists( 'glocal_networkwide_posts_module' ) ) {
			echo glocal_networkwide_posts_module( $instance );
		}

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
			'include_event_categories' => '',
			'include_event_tags' => '',
		) );

		// Retrieve an existing value from the database
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		// $post_type = 'event';
		$number_posts = ! empty( $instance['number_posts'] ) ? $instance['number_posts'] : '';
		$exclude_sites = ! empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : '';
		$style = ! empty( $instance['style'] ) ? $instance['style'] : '';
		$id = ! empty( $instance['id'] ) ? $instance['id'] : '';
		$class = ! empty( $instance['class'] ) ? $instance['class'] : '';
		$show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
		$excerpt_length = ! empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '';
		$show_site_name = isset( $instance['show_site_name'] ) ? (bool) $instance['show_site_name'] : false;
		$event_scope = ! empty( $instance['event_scope'] ) ? $instance['event_scope'] : '';

		$include_event_categories = ! empty( $instance['include_event_categories'] ) ? $instance['include_event_categories'] : '';
		$include_event_tags = ! empty( $instance['include_event_tags'] ) ? $instance['include_event_tags'] : '';

		// Form fields
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'title' ) . '" class="title_label">' . __( 'Title', 'anp-network-content' ) . '</label>';
		echo '	<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Widget Title', 'anp-network-content' ) . '" value="' . esc_attr( $title ) . '">';
		echo '</p>';

		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'number_posts' ) . '" class="number_posts_label">' . __( 'Number of Posts', 'anp-network-content' ) . '</label>';
		echo '	<input type="number" id="' . $this->get_field_id( 'number_posts' ) . '" name="' . $this->get_field_name( 'number_posts' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $number_posts ) . '">';
		echo '</p>';

		// Exclude Sites
		echo '<p>';
		echo '	<label for="exclude_sites" class="exclude_sites_label">' . __( 'Exclude Sites', 'anp-network-content' ) . '</label>';
		echo '	<select id="' . $this->get_field_id( 'exclude_sites' ) . '" name="' . $this->get_field_name( 'exclude_sites' ) . '[]" multiple="multiple" class="widefat">';
		echo '		<option value="" ' . selected( $exclude_sites, '', false ) . '> ' . __( 'None', 'anp-network-content' );

		$siteargs = array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		);

		$sites = get_sites( $siteargs );

		foreach( $sites as $site ) {
			$site_id = $site['blog_id'];
			$site_name = get_blog_details( $site_id )->blogname;
			echo '		<option id="' . $site_id . '" value="' . $site_id . '"', ( ! empty( $exclude_sites ) && in_array( $site_id,	$exclude_sites) )	? ' selected="selected"' : '','>' . $site_name . '</option>';
		}

		echo '	</select>';
		echo '</p>';

		// Include Event Categories
		echo '<p>';
		echo '	<label for="include_event_categories" class="include_event_categories_label">' . __( 'Include Categories', 'anp-network-content' ) . '</label>';
		echo '	<select id="' . $this->get_field_id( 'include_event_categories' ) . '" name="' . $this->get_field_name( 'include_event_categories' ) . '[]" multiple="multiple" class="widefat">';
		echo '		<option value="" ' . selected( $include_event_categories, '', false ) . '> ' . __( 'None', 'anp-network-content' );

		$categories = get_sitewide_taxonomy_terms( 'event-category' );

		foreach( $categories as $key => $value ) {
			echo '		<option id="' . $key . '" value="' . $key . '"', ( ! empty( $include_event_categories ) && in_array( $key,	$include_event_categories) ) ? ' selected="selected"' : '','>' . $value . '</option>';
		}

		echo '	</select>';
		echo '</p>';

		// Include Event Tags
		echo '<p>';
		echo '	<label for="include_event_tags" class="include_event_tags_label">' . __( 'Include Tags', 'anp-network-content' ) . '</label>';
		echo '	<select id="' . $this->get_field_id( 'include_event_tags' ) . '" name="' . $this->get_field_name( 'include_event_tags' ) . '[]" multiple="multiple" class="widefat">';
		echo '		<option value="" ' . selected( $include_event_tags, '', false ) . '> ' . __( 'None', 'anp-network-content' );

		$tags = get_sitewide_taxonomy_terms( 'event-tag' );

		foreach( $tags as $key => $value ) {
			echo '		<option id="' . $key . '" value="' . $key . '"', ( ! empty( $include_event_tags ) && in_array( $key,	$include_event_tags ) ) ? ' selected="selected"' : '','>' . $value . '</option>';
		}

		echo '	</select>';
		echo '</p>';

		// Event Scope
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'event_scope' ) . '" class="event_scope_label">' . __( 'Event Scope', 'anp-network-content' ) . '</label>';
		echo '	<select id="' . $this->get_field_id( 'event_scope' ) . '" name="' . $this->get_field_name( 'event_scope' ) . '" class="widefat">';

		$scopes = array(
			'future' => __( 'Future', 'anp-network-content' ),
			'past'	 => __( 'Past', 'anp-network-content' ),
			'all'	=> __( 'All', 'anp-network-content' )
		);

		foreach( $scopes as $key => $value ) {
			echo '<option value="' . $key . '" ' . selected( $event_scope, $key ) . '> ' . $value . '</option>';
		}

		echo '	</select>';
		echo '</p>';

		// Style
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'style' ) . '" class="style_label">' . __( 'Display Style', 'anp-network-content' ) . '</label>';
		echo '	<select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '" class="widefat">';

		$styles = array(
			''		=> __( 'List (Default)', 'anp-network-content' ),
			'block' => __( 'Block', 'anp-network-content' )
		);

		foreach( $styles as $key => $value ) {
			echo '<option value="' . $key . '" ' . selected( $style, $key, false ) . '> ' . $value;
		}

		echo '	</select>';
		echo '</p>';

		// ID
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'id' ) . '" class="id_label">' . __( 'ID', 'anp-network-content' ) . '</label>';
		echo '	<input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter ID', 'anp-network-content' ) . '" value="' . esc_attr( $id ) . '">';
		echo '</p>';

		// Class
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'class' ) . '" class="class_label">' . __( 'Class', 'anp-network-content' ) . '</label>';
		echo '	<input type="text" id="' . $this->get_field_id( 'class' ) . '" name="' . $this->get_field_name( 'class' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Class', 'anp-network-content' ) . '" value="' . esc_attr( $class ) . '">';
		echo '</p>';

		// Show meta
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'show_meta' ) . '" class="show_meta_label">' . __( 'Show Meta', 'anp-network-content' ) . '</label>';
		echo '	<input type="checkbox" id="' . $this->get_field_id( 'show_meta' ) . '" name="' . $this->get_field_name( 'show_meta' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_meta, true, false ) . '>';
		echo '</p>';

		// Excerpt Length
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'excerpt_length' ) . '" class="excerpt_length_label">' . __( 'Excerpt Length', 'anp-network-content' ) . '</label>';
		echo '	<input type="number" id="' . $this->get_field_id( 'excerpt_length' ) . '" name="' . $this->get_field_name( 'excerpt_length' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $excerpt_length ) . '">';
		echo '</p>';

		// Show Site Name
		echo '<p>';
		echo '	<label for="' . $this->get_field_id( 'show_site_name' ) . '" class="show_site_name_label">' . __( 'Show Site Name', 'anp-network-content' ) . '</label>';
		echo '	<input type="checkbox" id="' . $this->get_field_id( 'show_site_name' ) . '" name="' . $this->get_field_name( 'show_site_name' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_site_name, true, false ) . '>';
		echo '</p>';

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
		$instance['include_event_categories'] = ! empty( $new_instance['include_event_categories'] ) ? $new_instance['include_event_categories'] : '';
		$instance['include_event_tags'] = ! empty( $new_instance['include_event_tags'] ) ? $new_instance['include_event_tags'] : '';
		$instance['show_meta'] = ! empty( $new_instance['show_meta'] ) ? true : false;
		$instance['style'] = ! empty( $new_instance['style'] ) ? $new_instance['style'] : '';
		$instance['event_scope'] = ! empty( $new_instance['event_scope'] ) ? $new_instance['event_scope'] : '';
		$instance['id'] = ! empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
		$instance['class'] = ! empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
		$instance['excerpt_length'] = ! empty( $new_instance['excerpt_length'] ) ? strip_tags( $new_instance['excerpt_length'] ) : 20;
		$instance['show_site_name'] = ! empty( $new_instance['show_site_name'] ) ? true : false;

		return $instance;

	}



} // end class ANP_Network_Events_Widget
