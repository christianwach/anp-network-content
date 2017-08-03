<?php

/**
 * Network Content Widgets and Shortcodes Events Class.
 *
 * A class that encapsulates Events functionality. It inherits all methods from
 * WP_Network_Content_Display_Posts and adds a couple of methods.
 *
 * @since 2.0.0
 */
class WP_Network_Content_Display_Events extends WP_Network_Content_Display_Posts {



	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// init parent
		parent::__construct();

	}



	/**
	 * Get sitewide taxonomy terms.
	 *
	 * @param str $taxonomy The name of of the taxonomy.
	 * @param array $exclude_sites The sites to exclude.
	 * @return array $term_list The array of terms with unique taxonomy term slugs and names.
	 */
	public function get_sitewide_taxonomy_terms( $taxonomy, $exclude_sites = null ) {

		// Site statuses to include
		$siteargs = array(
			'limit' => null,
			'public' => 1,
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'mature' => null,
		);

		// Allow the $siteargs to be changed
		if ( has_filter( 'glocal_network_tax_term_siteargs_arguments' ) ) {
			$siteargs = apply_filters( 'glocal_network_tax_term_siteargs_arguments', $siteargs );
		}

		$sites_list = ( $exclude_sites ) ? glocal_exclude_sites( $exclude_sites ) : get_sites( $siteargs );

		$termargs = array();

		// Allow the $siteargs to be changed
		if ( has_filter( 'glocal_network_tax_termarg_arguments' ) ) {
			$termargs = apply_filters( 'glocal_network_tax_termarg_arguments', $termargs );
		}

		$term_list = array();

		foreach( $sites_list as $site ) {

			$site_id = $site->blog_id;

			// Switch to the site to get details and posts
			switch_to_blog( $site_id );

			$site_terms = get_terms( $taxonomy, $termargs );

			foreach( $site_terms as $term ) {
				if ( !in_array( $term->slug, $term_list ) ) {
					$term_list[$term->slug] = $term->name;
				}
			}

			// Unswitch the site
			restore_current_blog();

		}

		$term_list = array_unique( $term_list );

		return $term_list;

	}



	/**
	 * Custom event meta.
	 *
	 * @param int $event_id The numeric ID of the event.
	 * @return str $html The formatted event meta.
	 */
	public function glocal_get_event_taxonomy( $event_id = 0 ) {

		$event_id = (int) ( empty( $event_id ) ? get_the_ID() : $event_id );

		if ( empty( $event_id ) ){
			return false;
		}

		$html = '<div class="entry-meta event-meta">';
		$venue = get_taxonomy( 'event-venue' );

		if ( get_the_terms( $event_id, 'event-category' ) ) {
			$html .= get_the_term_list( $event_id, 'event-category', '<ul class="category event-category"><li>','</li><li class="cat-item">', '</li></ul>' );
		}

		if ( get_the_terms( $event_id, 'event-tag' ) && ! is_wp_error( get_the_terms( $event_id, 'event-tag' ) ) ) {
			$html .= get_the_term_list( $event_id, 'event-tag', '<ul class="event-tags tags"><li class="tag-item">','</li><li>', '</li></ul>' );
		}

		$html .='</div>';

		return $html;

	}




} // end class WP_Network_Content_Display_Events
