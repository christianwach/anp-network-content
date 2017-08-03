<?php

/**
 * ANP Network Content Render
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */



/************* RENDERING FUNCTIONS *****************/



/**
 * Render a list of posts.
 *
 * @param array $posts_array An array of posts data and params.
 * @param array $options_array An array of rendering options.
 * @return str $rendered_html The data rendered as 'normal' or 'highlight' HTML.
 */
function render_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	if ( ! empty( $style ) ) {
		if( 'list'	== $style ) {
			$rendered_html = render_list_html( $posts_array, $settings );
		} else {
			 $rendered_html = render_block_html( $posts_array, $settings );
		}
	} else {
		$rendered_html = render_list_html( $posts_array, $settings );
	}

	return $rendered_html;

}



/**
 * Render an array of posts as an HTML list.
 *
 * @param array $posts_array An array of posts data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML list.
 */
function render_list_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	// Convert strings to booleans
	$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : '';
	$show_excerpt = ( ! empty( $show_excerpt ) ) ? filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) : '';
	$show_thumbnail = ( ! empty( $show_thumbnail ) ) ? filter_var( $show_thumbnail, FILTER_VALIDATE_BOOLEAN ) : '';
	$show_site_name = ( ! empty( $show_site_name ) ) ? filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) : '';

	$html = '<ul class="anp-network-posts ' . $post_type . '-list">';

	foreach( $posts_array as $post => $post_detail ) {

		global $post;

		$post_id = $post_detail['post_id'];

		if ( isset( $post_detail['categories'] ) ) {
			$post_categories = implode( ", ", $post_detail['categories'] );
		}

		$template_name = 'anp-' . $post_type . '-list-template.php';

		// // use a template for the output so that it can easily be overridden by theme
		// // check for template in active theme
		// $template = locate_template( array( 'plugins/anp-network-content/anp-' . $post_type . '-list-template.php' ) );
		//
		// // if none found use the default template
		// if ( $template == '' ) $template = ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-' . $post_type . '-list-template.php';
		//
		// include ( $template );

		anp_content_locate_template( $template_name, true );

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
function render_block_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$html = '<div class="anp-network-posts ' . $post_type . '-list">';

	foreach( $posts_array as $post => $post_detail ) {

		global $post;

		$post_id = $post_detail['post_id'];
		$post_categories = ( isset( $post_detail['categories'] ) ) ? implode( ", ", $post_detail['categories'] ) : '';

		// Convert strings to booleans
		$show_meta = ( ! empty( $show_meta ) ) ? filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_excerpt = ( ! empty( $show_excerpt ) ) ? filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_thumbnail = ( ! empty( $show_thumbnail ) ) ? filter_var( $show_thumbnail, FILTER_VALIDATE_BOOLEAN ) : '';
		$show_site_name = ( ! empty( $show_site_name) ) ? filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) : '';

		$template_name = 'anp-' . $post_type . '-block-template.php';

		if( file_exists( trailingslashit( get_stylesheet_directory() ) . 'plugins/anp-network-content/' . $template_name ) ) {
			$template = trailingslashit( get_stylesheet_directory() ) . 'plugins/anp-network-content/' . $template_name;
		} else {
			$template = ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/' . $template_name;
		}

		$template = apply_filters( 'anp_network_content_block_template', $template );

		// // use a template for the output so that it can easily be overridden by theme
		// // check for template in active theme
		// $template = locate_template( trailingslashit( get_stylesheet_directory() ) . 'plugins/anp-network-content/' . $template_name );
		//
		// // if none found use the default template
		// $template = ( '' != $template ) ? trailingslashit( get_stylesheet_directory() ) . 'plugins/anp-network-content/' . $template_name : ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/' . $template_name;
		//
		// $template = trailingslashit( get_stylesheet_directory() ) . 'plugins/anp-network-content/' . $template_name;

		include( $template );

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
function render_highlights_html( $posts_array, $options_array ) {

	$highlight_posts = $posts_array;
	$settings = $options_array;

	// Extract each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$title_image = ( isset( $title_image ) ) ? 'style="background-image:url(' . $title_image . ')"' : '';

	$html = '';

	// use a template for the output so that it can easily be overridden by theme
	// check for template in active theme
	$template = locate_template(array( 'plugins/anp-network-content/anp-post-highlights-template.php'));

	// if none found use the default template
	$template = ( $template == '' ) ? ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-post-highlights-template.php' : '';

	include( $template );

	return $html;

}



/**
 * Render an array of sites as an HTML list.
 *
 * @param array $sites_array An array of sites data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML list.
 */
function render_sites_list( $sites_array, $options_array ) {

	$sites = $sites_array;
	$settings = $options_array;

	// Extract each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$show_image = (filter_var($show_image, FILTER_VALIDATE_BOOLEAN));
	$show_meta = (filter_var($show_meta, FILTER_VALIDATE_BOOLEAN));

	if ( ! $show_image ) {
		$class .= ' no-site-image';
	} else {
		$class .= ' show-site-image';
	}

	$html = '<ul id="' . $id . '" class="sites-list ' . $class . '">';

	foreach ( $sites as $site ) {

		$site_id = $site['blog_id'];

		// CALL GET SLUG FUNCTION
		$slug = get_site_slug($site['path']);

		// use a template for the output so that it can easily be overridden by theme
		// check for template in active theme
		$template = locate_template( array( 'plugins/anp-network-content/anp-sites-list-template.php' ) );

		// if none found use the default template
		$template = ( $template == '' ) ? ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-sites-list-template.php' : '';

		include( $template );

	}

	$html .= '</ul>';

	return $html;

}



/**
 * Render an array of events as an HTML list.
 *
 * @param array $events_array An array of events data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML list.
 */
function render_events_list( $events_array, $options_array ) {

}



/**
 * Render an array of events as an HTML list.
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
function render_event_list_html( $events_array, $options_array ) {

	$events_array = $events_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	// Convert strings to booleans
	$show_meta = ( filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) );

	$html = '<ul class="network-event-list ' . $post_type . '-list">';

	foreach( $events_array as $event => $event_detail ) {

		global $post;

		$post_id = $event_detail['post_id'];

		// use a template for the output so that it can easily be overridden by theme
		// check for template in active theme
		$template = locate_template( array( 'plugins/anp-network-content/anp-event-list-template.php' ) );

		// if none found use the default template
		include( $template );

	}

	$html .= '</ul>';

	return $html;

}



/**
 * Render an array of events as an HTML "block".
 *
 * @param array $events_array An array of events data and params.
 * @param array $options_array An array of rendering options.
 * @return str $html The data rendered as an HTML "block".
 */
function render_event_block_html( $posts_array, $options_array ) {

	$posts_array = $posts_array;
	$settings = $options_array;

	// Make each parameter as its own variable
	extract( $settings, EXTR_SKIP );

	$html = '<div class="network-posts-list style-' . $style . '">';

	foreach( $posts_array as $post => $post_detail ) {

		global $post;

		$post_id = $post_detail['post_id'];
		$post_categories = ( isset( $post_detail['categories'] ) ) ? implode( ", ", $post_detail['categories'] ) : '';

		// Convert strings to booleans
		$show_meta = ( filter_var( $show_meta, FILTER_VALIDATE_BOOLEAN ) );
		$show_thumbnail = ( filter_var( $show_excerpt, FILTER_VALIDATE_BOOLEAN ) );
		$show_site_name = ( filter_var( $show_site_name, FILTER_VALIDATE_BOOLEAN ) );

		// use a template for the output so that it can easily be overridden by theme
		// check for template in active theme
		$template = locate_template( array( 'plugins/anp-network-content/anp-post-block-template.php' ) );

		// if none found use the default template
		$template = ( $template == '' ) ? ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-post-block-template.php' : '';

		include( $template );

	}

	$html .= '</div>';

	return $html;

}
