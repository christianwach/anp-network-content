<?php

/**
 * Network Content Widgets and Shortcodes Helpers Class.
 *
 * A class that "namespaces" utility functions.
 *
 * @since 2.0.0
 */
class WPNCD_Helpers {



	/************* SITE DATA METHODS *****************/



	/**
	 * Retrieve an array of site data.
	 *
	 * @param array $options_array The array of parameters.
	 * @return array $site_list The array of sites with site information.
	 */
	public static function get_data_for_sites( $options_array ) {

		// init return
		$site_list = array();

		// basic site query args
		$site_args = array(
			'number' => 100,
			'public' => 1,
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'mature' => null,
		);

		// check for excludes
		if ( ! empty( $options_array['exclude_sites'] ) ) {
			$site_args['site__not_in'] = $options_array['exclude_sites'];
		}

		// check for orderby 'registered' or 'last_updated' which can be done via query
		if ( ! empty( $options_array['sort_by'] ) ) {
			if ( $options_array['sort_by'] == 'registered' ) {
				$site_args['orderby'] = 'registered';
				$site_args['order'] = 'DESC';
			}
			if ( $options_array['sort_by'] == 'last_updated' ) {
				$site_args['orderby'] = 'last_updated';
				$site_args['order'] = 'DESC';
			}
		}

		/**
		 * Allow the $site_args to be filtered.
		 *
		 * @since 2.0.0
		 *
		 * @param array $site_args The arguments used to query the sites.
		 */
		$site_args = apply_filters( 'wpncd_get_data_for_sites_args', $site_args );

		// get sites
		$sites = get_sites( $site_args );

		foreach( $sites as $site ) {

			$site_details = get_blog_details( $site->blog_id );

			$site_list[$site->blog_id] = array(
				'blog_id' => $site->blog_id,
				'blogname' => $site_details->blogname,
				'siteurl' => $site_details->siteurl,
				'path' => $site_details->path,
				'registered' => $site_details->registered,
				'last_updated' => $site_details->last_updated,
				'post_count' => intval( $site_details->post_count ),
			);

			// switch to blog now so we only switch once per site
			switch_to_blog( $site->blog_id );

			// get site header image
			$site_image = self::get_site_header_image( $site->blog_id );

			if ( $site_image ) {
				$site_list[$site->blog_id]['site-image'] = $site_image;
			} elseif ( isset( $default_image ) ) {
				$site_list[$site->blog_id]['site-image'] = $default_image;
			} else {
				$site_list[$site->blog_id]['site-image'] = '';
			}

			$site_list[$site->blog_id]['recent_post'] = self::get_latest_post( $site->blog_id );

			// switch back
			restore_current_blog();

		}

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'options_array' => $options_array,
			'site_args' => $site_args,
			'sites' => $sites,
			'site_list' => $site_list,
			//'backtrace' => $trace,
		), true ) );
		*/

		// --<
		return $site_list;

	}



	/**
	 * Get the most recent post from the specified site.
	 *
	 * @param int $site_id The numeric ID of the site.
	 * @return array $recent_post_data The array of data for the post.
	 */
	public static function get_latest_post( $site_id ) {

		// get current site ID
		$blog_id = get_current_blog_id();

		// do we need to switch?
		$switched = false;
		if ( $blog_id != $site_id ) {
			switch_to_blog( $site_id );
			$switched = true;
		}

		// Get most recent post
		$recent_posts = wp_get_recent_posts( 'numberposts=1' );

		// Get most recent post info
		foreach( $recent_posts as $post ) {

			// Post into $site_list array
			$recent_post_data = array (
				'post_id' => $post['ID'],
				'post_author' => $post['post_author'],
				'post_slug' => $post['post_name'],
				'post_date' => $post['post_date'],
				'post_title' => $post['post_title'],
				'post_content' => $post['post_content'],
				'permalink' => get_permalink( $post['ID'] ),
			);

			// If there is a featured image, add URL to array, else leave empty
			if ( wp_get_attachment_url( get_post_thumbnail_id( $post['ID'] ) ) ) {
				$recent_post_data['thumbnail'] = wp_get_attachment_url( get_post_thumbnail_id( $post['ID'] ) );
			} else {
				$recent_post_data['thumbnail'] = '';
			}

		}

		// switch back if needed
		if ( $switched === true ) {
			restore_current_blog();
		}

		// --<
		return $recent_post_data;

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
	 * Get the image for a site.
	 *
	 * @param int $site_id The numeric ID of the site.
	 * @return str $thumbnail_url The URL of the site image.
	 */
	public static function get_site_header_image( $site_id ) {

		// get current site ID
		$blog_id = get_current_blog_id();

		// do we need to switch?
		$switched = false;
		if ( $blog_id != $site_id ) {
			switch_to_blog( $site_id );
			$switched = true;
		}

		// get site header
		$site_image = get_custom_header();

		// switch back if needed
		if ( $switched === true ) {
			restore_current_blog();
		}

		// --<
		return $site_image->thumbnail_url;

	}



	/************* SORTING METHODS *****************/



	/**
	 * Sort an array of posts by post date.
	 *
	 * @param array $posts_array An array of posts data.
	 * @return array $settings An array of posts data sorted by post_date.
	 */
	public static function sort_by_date( $posts_array ) {

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

		usort( $sites_array, function( $b, $a ) {
			return strcmp( $a['last_updated'], $b['last_updated'] );
		});

		return $sites_array;

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

		usort( $sites_array, function( $b, $a ) {
			return strcmp( $a['post_count'], $b['post_count'] );
		});

		return $sites_array;

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

				// sanitise arrays
				if (  is_array( $val ) ) {

					// Assign value directly for now
					$new_input[ $key ] = $val;

				} else {

					// sanitize value
					$sanitized_val = sanitize_text_field( $val );

					// Set type back to original variable type
					settype( $sanitized_val, $type );

					// Assign sanitized value
					$new_input[$key] = $sanitized_val;

				}

			}

		}

		return $new_input;

	}



	/**
	 * Merge settings and remove unused items.
	 *
	 * @param array $parameters The provided array of settings.
	 * @param array $defaults The default array of settings.
	 * @return array $settings The merged array of settings.
	 */
	public static function get_merged_settings( $parameters, $defaults ) {

		// Parse & merge parameters with the defaults
		// @see http://codex.wordpress.org/Function_Reference/wp_parse_args
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



	/**
	 * Find a template file.
	 *
	 * @since 2.0.0
	 *
	 * @param str $template The name of the template file.
	 */
	public static function find_template( $template ) {

		// define paths template directories
		$theme_path = 'plugins/wp-network-content-display/';
		$plugin_path = 'assets/templates/';

		// first check active theme
		if ( file_exists( get_stylesheet_directory() . $theme_path . $template ) ) {
			$template = get_stylesheet_directory() . $theme_path . $template;

		// next look in parent theme
		} elseif ( is_child_theme() && file_exists( get_template_directory() . $theme_path . $template ) ) {
			$template = get_template_directory() . $theme_path . $template;

		// lastly, use supplied template
		} else {
			$template = WP_NETWORK_CONTENT_DISPLAY_DIR . $plugin_path . $template;
		}

		/**
		 * Filter template path and return.
		 *
		 * @since 2.0.0
		 *
		 * @param str $template The path to the found template file.
		 */
		return apply_filters( 'wpncd_find_template', $template );

	}



} // end class WPNCD_Helpers
