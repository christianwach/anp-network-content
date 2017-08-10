<?php

/**
 * Network Content Widgets and Shortcodes Sites Class.
 *
 * A class that encapsulates Posts functionality.
 *
 * @since 2.0.0
 */
class WP_Network_Content_Display_Sites {

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
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/shortcodes/class-shortcode-sites.php' );

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
		$this->shortcode = new WP_Network_Content_Display_Sites_Shortcode;

		// we're done
		$done = true;

	}



	/**
	 * Register widgets for this component.
	 *
	 * @since 2.0.0
	 */
	public function register_widgets() {

		// register Network Sites Widget
		require( WP_NETWORK_CONTENT_DISPLAY_DIR . 'includes/widgets/class-widget-sites.php' );
		register_widget( 'WP_Network_Content_Display_Sites_Widget' );

	}



	/************* CONTENT METHODS *****************/



	/**
	 * Gets (or renders) a list of sites.
	 *
	 * @param array $parameters An array of settings with the following options:
	 *    return - Return ( display list of sites or return array of sites ) ( default: display )
	 *    number_sites - Number of sites to display/return ( default: 20 )
	 *    exclude_sites - ID of sites to exclude ( default: 1 ( usually, the main site ) )
	 *    sort_by - newest, updated, active, alpha ( registered, last_updated, post_count, blogname ) ( default: blogname )
	 *    default_image - Default image to display if site doesn't have a custom header image ( default: none )
	 *    instance_id - ID name for site list instance ( default: network-sites-RAND )
	 *    class_name - CSS class name( s ) ( default: network-sites-list )
	 *    hide_meta - Select in order to update date and latest post. Only relevant when return = 'display'. ( default: false )
	 *    show_icon - Select how to show site icons. ( default: none )
	 *    show_join - Future
	 *    join_text - Future
	 * @return array $sites_list The array of sites.
	 */
	public function get_network_sites( $parameters = array() ) {

		// Default parameters
		$defaults = array(
			'return' => 'display',
			'number' => -1,
			'exclude_sites' => array(),
			'sort_by' => 'blogname',
			'default_image' => '',
			'show_meta' => false,
			'show_icon' => 'none',
		);

		// merge
		$settings = wp_parse_args( $parameters, $defaults );

		// get sites data
		$sites_list = WPNCD_Helpers::get_data_for_sites( $settings );

		// default to no icon
		$default_icon = '';

		// have we selected to show a site icon?
		if ( ! empty( $settings['show_icon'] ) ) {

			// choose a source ('none' can be ignored)
			switch ( $settings['show_icon'] ) {

				case 'library' :
					if ( ! empty( $settings['attachment_id'] ) ) {
						$icon_data = wp_get_attachment_image_src( $settings['attachment_id'] );
						$default_icon = ( isset( $icon_data[0] ) ) ? $icon_data[0] : '';
					}
					break;

				case 'url' :
					$default_icon = ! empty( $settings['default_image'] ) ? esc_url( $settings['default_image'] ) : '';
					break;

			}

		}

		foreach( $sites_list as $site_id => $site_details ) {

			switch_to_blog( $site_id );

			// add site icon and recent post
			$sites_list[$site_id]['site_icon'] = WPNCD_Helpers::get_site_icon( $site_id, $default_icon );
			$sites_list[$site_id]['recent_post'] = WPNCD_Helpers::get_latest_post( $site_id );

			restore_current_blog();

		}

		// sorting that can't be done in the query
		if ( $settings['sort_by'] == 'active' ) {
			$sites_list = WPNCD_Helpers::sort_array_by_key( $sites_list, 'post_count', 'DESC' );
		}
		if ( $settings['sort_by'] == 'blogname' ) {
			$sites_list = WPNCD_Helpers::sort_array_by_key( $sites_list, 'blogname' );
		}

		// limit to requested number
		$sites_list = ( isset( $settings['number_sites'] ) ) ?
					  WPNCD_Helpers::limit_number_items( $sites_list, $settings['number_sites'] ) :
					  $sites_list;

		// return raw data if requested
		if ( isset( $settings['return'] ) AND $settings['return'] == 'array' ) {
			return $sites_list;
		}

		// return rendered markup
		return $this->render_html( $sites_list, $settings );

	}



	/************* RENDERING METHODS *****************/



	/**
	 * Render a list of sites.
	 *
	 * @param array $sites_array An array of sites data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $rendered_html The data rendered as 'block' or 'list' HTML.
	 */
	public function render_html( $sites_array, $options_array ) {

		// choose how to render
		if ( ! empty( $options_array['style'] ) ) {
			if( 'list'	== $options_array['style'] ) {
				$rendered_html = $this->render_html_list( $sites_array, $options_array );
			} else {
				 $rendered_html = $this->render_html_block( $sites_array, $options_array );
			}
		} else {
			$rendered_html = $this->render_html_list( $sites_array, $options_array );
		}

		// --<
		return $rendered_html;

	}



	/**
	 * Render an array of sites as an HTML list.
	 *
	 * @param array $sites_array An array of sites data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $html The data rendered as an HTML list.
	 */
	public function render_html_list( $sites_array, $options_array ) {

		// Extract each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		$show_icon = ! empty( $show_icon ) ? $show_icon : 'none';
		$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : false;

		if ( $show_icon == 'none' ) {
			$class = ' no-site-image';
		} else {
			$class = ' show-site-image';
		}

		// open list
		$html = '<ul class="wpncd-network-sites sites-list' . $class . '">';

		// find template
		$template = WPNCD_Helpers::find_template( 'sites-list.php' );

		foreach ( $sites_array as $site ) {

			$site_id = $site['blog_id'];

			// get a slug for this item
			$slug = WPNCD_Helpers::get_site_slug( $site['path'] );

			// prevent immediate output
			ob_start();

			// use template
			include( $template );

			// grab markup
			$html .= ob_get_contents();

			// clean up
			ob_end_clean();

		}

		// close list
		$html .= '</ul>';

		// --<
		return $html;

	}



	/**
	 * Render an array of sites as HTML "blocks".
	 *
	 * @param array $sites_array An array of sites data and params.
	 * @param array $options_array An array of rendering options.
	 * @return str $html The data rendered as an HTML "block".
	 */
	public function render_html_block( $sites_array, $options_array ) {

		// Make each parameter as its own variable
		extract( $options_array, EXTR_SKIP );

		// open div
		$html = '<div class="wpncd-network-sites">';

		// find template
		$template = WPNCD_Helpers::find_template( 'sites-block.php' );

		foreach ( $sites_array as $site ) {

			$site_id = $site['blog_id'];

			// CALL GET SLUG FUNCTION
			$slug = WPNCD_Helpers::get_site_slug( $site['path'] );

			// prevent immediate output
			ob_start();

			// use template
			include( $template );

			// grab markup
			$html .= ob_get_contents();

			// clean up
			ob_end_clean();

		}

		// close div
		$html .= '</div>';

		// --<
		return $html;

	}



} // end class WP_Network_Content_Display_Sites
