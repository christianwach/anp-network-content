<?php

/**
 * Network Content Widgets and Shortcodes Helpers Class.
 *
 * A class that "namespaces" utility functions.
 *
 * @since 2.0.0
 */
class WP_Network_Content_Display_Helpers {



	/************* SORTING FUNCTIONS *****************/



	/**
	 * Sort an array of posts by post date.
	 *
	 * @param array $posts_array An array of posts data.
	 * @return array $settings An array of posts data sorted by post_date.
	 */
	public static function sort_by_date( $posts_array ) {

		$posts_array = $posts_array;

		usort( $posts_array, function( $b, $a ) {
			return strcmp( $a['post_date'], $b['post_date'] );
		});

		return $posts_array;

	}



	/**
	 * Sort an array of posts by site.
	 *
	 * NOT USED
	 *
	 * @param array $posts_array An array of posts data.
	 * @return array $settings An array of posts data sorted by site.
	 */
	public static function sort_by_site( $posts_array ) {

		$posts_array = $posts_array;

		usort( $posts_array, function( $b, $a ) {
			return strcmp( $a['site_id'], $b['site_id'] );
		});

		return $posts_array;

	}



	/**
	 * Sort an array of sites by last updated.
	 *
	 * NOT USED
	 *
	 * @param array $sites_array An array of sites.
	 * @return array $sites An array of sites sorted by last_updated.
	 */
	public static function sort_sites_by_last_updated( $sites_array ) {

		$sites = $sites_array;

		usort( $sites, function( $b, $a ) {
			return strcmp( $a['last_updated'], $b['last_updated'] );
		});

	}



	/**
	 * Sort an array of sites by most active.
	 *
	 * NOT USED
	 *
	 * @param array $sites_array An array of sites.
	 * @return array $sites An array of sites sorted by post_count.
	 */
	public static function sort_sites_by_most_active( $sites_array ) {

		$sites = $sites_array;

		usort( $sites, function( $b, $a ) {
			return strcmp( $a['post_count'], $b['post_count'] );
		});

	}

	/**
	 * Sort an array by a provided key.
	 *
	 * @param array $array An associative array.
	 * @param str $key The sort key (e.g. 'post_count').
	 * @param str $order The sort order (e.g. ASC or DESC).
	 * @return array $c The array sorted by key.
	 */
	public static function sort_array_by_key( $array, $key, $order = 'ASC' ) {

		$a = $array;
		$subkey = $key;
		$b = [];
		$c = [];

		foreach( $a as $k => $v ) {
			$b[$k] = strtolower( $v[$subkey] );
		}

		if ( $order == 'DESC' ) {
			arsort( $b );
		} else {
			asort( $b );
		}

		foreach( $b as $key => $val ) {
			$c[] = $a[$key];
		}

		return $c;

	}



	/************* MISC HELPER FUNCTIONS *****************/



	/**
	 * Customise a post excerpt.
	 *
	 * NOT USED
	 *
	 * @param int $post_id The numeric ID of the post.
	 * @param str $length The numeric length of the excerpt.
	 * @param str $trailer The excerpt suffix.
	 * @return str $the_excerpt The modified excerpt.
	 */
	public static function custom_post_excerpt( $post_id, $length = '55', $trailer = ' ...' ) {

		// Get post from ID
		$the_post = get_post( $post_id );

		$the_excerpt = $the_post->post_content; // Gets post_content to be used as a basis for the excerpt
		$excerpt_length = $length; // Sets excerpt length by word count
		$the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) ); // Strips tags and images
		$words = explode( ' ', $the_excerpt, $excerpt_length + 1 );

		if ( count( $words ) > $excerpt_length ) {
			array_pop( $words );
			$trailer = '<a href="' . get_permalink( $post_id ) . '">' . $trailer . '</a>';
			array_push( $words, $trailer );
			$the_excerpt = implode( ' ', $words );
		}

		return $the_excerpt;

	}



	/**
	 * Limit the number of posts in an array.
	 *
	 * @param array $posts_array The array of posts.
	 * @param int $max_number The number to limit the array of posts to.
	 * @return array $posts The limited array of posts.
	 */
	public static function limit_number_posts( $posts_array, $max_number ) {

		$posts = $posts_array;
		$limit = $max_number;

		if ( $limit && ( count( $posts ) > $limit ) ) {
			array_splice( $posts, $limit );
		}

		return $posts;

	}



	/**
	 * Get the slug for a site.
	 *
	 * @param str $site_path The path of the site.
	 * @return str $slug The slug of the site.
	 */
	public static function get_site_slug( $site_path ) {

		$path = $site_path;
		$stripped_path = str_replace( '/', '', $path ); // Strip slashes from path to get slug

		if ( ! $path ) {
			// If there is no slug (it's the main site), make slug 'main'
			$slug = 'main';
		} else {
			// Otherwise use the stripped path as slug
			$slug = $stripped_path;
		}

		return $slug;

	}



	/**
	 * Get the class for a post.
	 *
	 * @param int $post_id The numeric ID of the post.
	 * @return array $post_markup_class The string of post classes for the post.
	 */
	public static function get_post_markup_class( $post_id ) {

		$post_id = $post_id;

		$markup_class_array = get_post_class( array( 'list-item' ), (int) $post_id );

		$post_markup_class = implode( ' ', $markup_class_array );

		return $post_markup_class;

	}



	/**
	 * Get the image for a site.
	 *
	 * @param int $site_id The numeric ID of the site.
	 * @return str $thumbnail_url The URL of the site image.
	 */
	public static function get_site_header_image( $site_id ) {

		global $blog_id;

		// Store the current blog_id being viewed
		$current_blog_id = $blog_id;

		// Switch to the main blog designated in $site_id
		switch_to_blog( $site_id );

		$site_image = get_custom_header();

		// Switch back to the current blog being viewed
		switch_to_blog( $current_blog_id );

		return $site_image->thumbnail_url;

	}



	/**
	 * Sanitize an array.
	 *
	 * @param array $input The array to sanitize.
	 * @return array $new_input A new array with sanitized values.
	 */
	public static function sanitize_input( $input ) {

		// Initialize the new array that will hold the sanitized values
		$new_input = array();

		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {

			// Get variable type
			$type = gettype( $val );

			if ( isset( $input[$key] ) ) {

				// Sanitize value
				$sanitized_val = sanitize_text_field( $val );

				// Set type back to original variable type
				settype( $sanitized_val, $type );

				// Assign sanitized value
				$new_input[ $key ] = $sanitized_val;

			}

		}

		return $new_input;

	}



	/**
	 * Merge settings and remove unused items.
	 *
	 * @param array $user_selections_array The provided array of settings.
	 * @param array $default_values_array The default array of settings.
	 * @return array $settings The merged array of settings.
	 */
	public static function get_merged_settings( $user_selections_array, $default_values_array ) {

		$parameters = $user_selections_array;
		$defaults = $default_values_array;

		// Parse & merge parameters with the defaults - http://codex.wordpress.org/Function_Reference/wp_parse_args
		// This function converts all arguments to strings
		$settings = wp_parse_args( $parameters, $defaults );

		// Remove unset items
		foreach( $settings as $parameter => $value ) {
			if ( empty( $settings[$parameter] ) ) {
				unset( $settings[$parameter] );
			}
		}

		return $settings;

	}



} // end class WP_Network_Content_Display_Helpers
