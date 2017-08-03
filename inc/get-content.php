<?php

/**
 * ANP Network Content Fetch Content
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */


/************* GET CONTENT FUNCTIONS *****************/

/**
 * Input: array of user inputs and array of default values
 * Output: merged array of $settings
 */
function get_merged_settings( $user_selections_array, $default_values_array ) {

    $parameters = $user_selections_array;
    $defaults = $default_values_array;

    // Parse & merge parameters with the defaults - http://codex.wordpress.org/Function_Reference/wp_parse_args
    // This function converts all arguments to strings
    $settings = wp_parse_args( $parameters, $defaults );

    // Remove unset items
    foreach( $settings as $parameter => $value ) {

        if( empty( $settings[$parameter] ) ) {

            unset( $settings[$parameter] );

        }

    }

    return $settings;

}

/**
 * Input: parameters array
 * Output: array of sites with site information
 */
function get_sites_list( $options_array ) {

    $settings = $options_array;

    // Make each parameter as its own variable
    extract( $settings, EXTR_SKIP );

    $siteargs = array(
        'limit'      => null,
        'public'     => 1,
        'archived'   => 0,
        'spam'       => 0,
        'deleted'    => 0,
        'mature'     => null,
    );

     // Allow the $siteargs to be  changed
    if( has_filter( 'anp_network_sites_site_arguments' ) ) {
        $siteargs = apply_filters( 'anp_network_sites_site_arguments', $siteargs );
    }

    // Allow the $siteargs to be changed
    $sites = wp_get_sites( $siteargs );

    // CALL EXCLUDE SITES FUNCTION
    $sites = ( !empty( $exclude_sites ) ) ? exclude_sites( $exclude_sites, $sites ) : $sites;

    $site_list = array();

    foreach( $sites as $site ) {

        $site_id = $site['blog_id'];
        $site_details = get_blog_details( $site_id );

        $site_list[$site_id] = array(
            'blog_id' => $site_id,  // Put site ID into array
            'blogname' => $site_details->blogname,  // Put site name into array
            'siteurl' => $site_details->siteurl,  // Put site URL into array
            'path' => $site_details->path,  // Put site path into array
            'registered' => $site_details->registered,
            'last_updated' => $site_details->last_updated,
            'post_count' => intval( $site_details->post_count ),
        );

        // CALL GET SITE IMAGE FUNCTION
        $site_image = get_site_header_image( $site_id );

        if( $site_image ) {
            $site_list[$site_id]['site-image'] = $site_image;
        }
        elseif( isset( $default_image ) ) {
            $site_list[$site_id]['site-image'] = $default_image;
        }
        else {
            $site_list[$site_id]['site-image'] = '';
        }

        $site_list[$site_id]['recent_post'] = get_most_recent_post( $site_id );

    }

    return $site_list;

}

/**
 * Inputs: exclude array and sites array
 * Output: array of sites, excluding those passed in function
 */
function exclude_sites( $exclude_array ) {

    // Site statuses to include
    $siteargs = array(
        'limit'      => null,
        'public'     => 1,
        'archived'   => 0,
        'spam'       => 0,
        'deleted'    => 0,
        'mature'     => null,
    );

    // Allow the $siteargs to be  changed
    if( has_filter( 'anp_network_exclude_sites_arguments' ) ) {
        $siteargs = apply_filters( 'anp_network_exclude_sites_arguments', $siteargs );
    }

    // Get a list of sites
    $sites = wp_get_sites( $siteargs );

    $exclude = ( !is_array( $exclude_array ) ) ? explode( ',', $exclude_array ) : $exclude_array ;

    $sites = array_filter( $sites, function( $site ) use ( $exclude ) {
        return !in_array( $site['blog_id'], $exclude );
    } );

    return $sites;

}

/**
 * Inputs: array of sites and parameters array
 * Output: single array of posts with site information, sorted by post_date
 */
function get_posts_list( $sites_array, $options_array ) {

    $sites = $sites_array;
    $settings = $options_array;

    // Make each parameter as its own variable
    extract( $settings, EXTR_SKIP );

    $post_list = array();

    // For each site, get the posts
    foreach( $sites as $site ) {

        $site_id = $site['blog_id'];

        // Switch to the site to get details and posts
        switch_to_blog( $site_id );

        // CALL GET SITE'S POST FUNCTION
        // And add to array of posts

        // If get_sites_posts( $site_id, $settings ) isn't null, add it to the array, else skip it
        // Trying to add a null value to the array using this syntax produces a fatal error.

        $site_posts = get_sites_posts( $site_id, $settings );

        if( get_sites_posts( $site_id, $settings ) ) {

            $post_list = $post_list + get_sites_posts( $site_id, $settings );

        }

        // Unswitch the site
        restore_current_blog();

    }

    // SORT ARRAY
    if( 'event' === $post_type ) {
        $post_list = sort_array_by_key( $post_list, 'event_start_date' );
    } else {
        $post_list = sort_by_date( $post_list );
    }

    // CALL LIMIT FUNCTIONS
    $post_list = ( isset( $number_posts ) ) ? limit_number_posts( $post_list, $number_posts ) : $post_list;

    return $post_list;

}

/**
 * Input: site id and parameters array
 * Ouput: array of posts for site
 */
function get_sites_posts( $site_id, $options_array ) {

    $site_id = $site_id;
    $settings = $options_array;

    // Make each parameter as its own variable
    extract( $settings, EXTR_SKIP );

    $site_details = get_blog_details( $site_id );

    $post_args['post_type'] = ( isset( $post_type ) ) ? $post_type : 'post' ;

    $post_args['posts_per_page'] = ( isset( $posts_per_page ) ) ? $posts_per_page : 20 ;

    $post_args['category_name'] = ( isset( $include_categories ) ) ? $include_categories : '';

    // Event-specific arguments
    if ( 'event' === $post_type ) {

        if( isset( $include_event_categories ) ) {
            $post_args['tax_query'][] = array(
                'taxonomy' => 'event-category',
                'field' => 'slug',
                'terms' => $include_event_categories
            );
        }

        if( isset( $include_event_tags ) ) {
            $post_args['tax_query'][] = array(
                'taxonomy' => 'event-tag',
                'field' => 'slug',
                'terms' => $include_event_tags
            );
        }

        switch ( $event_scope ) {

            case ( 'past' ) :
                $post_args['meta_query'] = array(
                    array(
                        'key'       => '_eventorganiser_schedule_start_start',
                        'value'     => date_i18n( 'Y-m-d' ),
                        'compare'   => '<',
                    ),
                );
                break;
            default :
                $post_args['meta_query'] = array(
                    array(
                        'key'       => '_eventorganiser_schedule_start_start',
                        'value'     => date_i18n( 'Y-m-d' ),
                        'compare'   => '>=',
                    ),
                );

        }

    }


    $recent_posts = wp_get_recent_posts( $post_args );

    // Put all the posts in a single array
    foreach( $recent_posts as $post => $postdetail ) {

        //global $post;

        $post_id = $postdetail['ID'];
        $author_id = $postdetail['post_author'];

        // Prefix the array key with event start date or post date
        $prefix = ( 'event' === $post_type ) ? get_post_meta ( $post_id, '_eventorganiser_schedule_start_start', true ) . '-' . $postdetail['post_name'] : $postdetail['post_date'] . '-' . $postdetail['post_name'];

        //CALL POST MARKUP FUNCTION
        $post_markup_class = get_post_markup_class( $post_id );
        $post_markup_class .= ' siteid-' . $site_id;

        //Returns an array
        $post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'medium' );

        if( $postdetail['post_excerpt'] ) {
            $excerpt = $postdetail['post_excerpt'];
        } else {
            $excerpt = wp_trim_words( $postdetail['post_content'], $excerpt_length, '... <a href="'. get_permalink( $post_id ) .'">Read More</a>' );
        }

        $post_list[$prefix] = array(
            'post_id' => $post_id,
            'post_title' => $postdetail['post_title'],
            'post_date' => $postdetail['post_date'],
            'post_author' => get_the_author_meta( 'display_name', $postdetail['post_author'] ),
            'post_content' => $postdetail['post_content'],
            'post_excerpt' => strip_shortcodes( $excerpt ),
            'permalink' => get_permalink( $post_id ),
            'post_image' => $post_thumbnail[0],
            'post_class' => $post_markup_class,
            'post_type' => $post_type,
            'site_id' => $site_id,
            'site_name' => $site_details->blogname,
            'site_link' => $site_details->siteurl,
        );

        if( 'event' === $post_type || function_exists( 'eo_get_venue' ) ) {
            $venue_id = eo_get_venue( $post_id );
            $post_list[$prefix]['event_start_date'] = get_post_meta ( $post_id, '_eventorganiser_schedule_start_start', true );
            $post_list[$prefix]['event_end_date'] = get_post_meta ( $post_id, '_eventorganiser_schedule_start_finish', true );

            $post_list[$prefix]['event_venue']['venue_link'] = eo_get_venue_link( $venue_id );

            $post_list[$prefix]['event_venue']['venue_id'] = $venue_id;
            $post_list[$prefix]['event_venue']['venue_name'] = eo_get_venue_name( $venue_id );
            $post_list[$prefix]['event_venue']['venue_location'] = eo_get_venue_address( $venue_id );
            $post_list[$prefix]['event_venue']['venue_location']['venue_lat'] = eo_get_venue_meta( $venue_id, '_lat' );
            $post_list[$prefix]['event_venue']['venue_location']['venue_long'] = eo_get_venue_meta( $venue_id, '_lng' );

            //Get post categories
            $event_categories = wp_get_post_terms( $post_id, 'event-category', array( "fields" => "all" ) );

            foreach( $event_categories as $event_category ) {
                $post_list[$prefix]['event_categories'][$event_category->slug] = $event_category->name;
            }

            $event_tags = wp_get_post_terms( $post_id, 'event-tag', array( "fields" => "all" ) );

            foreach( $event_tags as $event_tag ) {
                $post_list[$prefix]['event_tags'][$event_tag->slug] = $event_tag->name;
            }

        }

        //Get post categories
        $post_categories = wp_get_post_categories( $post_id );

        foreach( $post_categories as $post_category ) {
            $cat = get_category( $post_category );
            $post_list[$prefix]['categories'][] = $cat->name;
        }

        return $post_list;

    }


}

/**
 * Input: site_id
 * Output: array post data for single post
 */
function get_most_recent_post( $site_id ) {

    $site_id = $site_id;

    // Switch to current blog
    switch_to_blog( $site_id );

    // Get most recent post
    $recent_posts = wp_get_recent_posts( 'numberposts=1' );

    // Get most recent post info
    foreach( $recent_posts as $post ) {
        $post_id = $post['ID'];

        // Post into $site_list array
        $recent_post_data = array (
            'post_id' => $post_id,
            'post_author' => $post['post_author'],
            'post_slug' => $post['post_name'],
            'post_date' => $post['post_date'],
            'post_title' => $post['post_title'],
            'post_content' => $post['post_content'],
            'permalink' => get_permalink( $post_id ),
        );

        // If there is a featured image, add URL to array, else leave empty
        if( wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ) ) {
            $recent_post_data['thumbnail'] = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
        } else {
            $recent_post_data['thumbnail'] = '';
        }
    }

    // Exit
    restore_current_blog();

    return $recent_post_data;

}


/**
 * Input: $sites and $taxonomy
 * Output: new array with unique taxonomy term slugs and names
 */
function get_sitewide_taxonomy_terms( $taxonomy, $exclude_sites = null ) {

    // Site statuses to include
    $siteargs = array(
        'limit'      => null,
        'public'     => 1,
        'archived'   => 0,
        'spam'       => 0,
        'deleted'    => 0,
        'mature'     => null,
    );

    // Allow the $siteargs to be  changed
    if( has_filter( 'anp_network_tax_term_siteargs_arguments' ) ) {
        $siteargs = apply_filters( 'anp_network_tax_term_siteargs_arguments', $siteargs );
    }

    $sites_list = ( $exclude_sites ) ? exclude_sites( $exclude_sites ) : wp_get_sites( $siteargs );

    $termargs = array();

    // Allow the $siteargs to be  changed
    if( has_filter( 'anp_network_tax_termarg_arguments' ) ) {
        $termargs = apply_filters( 'anp_network_tax_termarg_arguments', $termargs );
    }

    $term_list = array();

    foreach( $sites_list as $site ) {

        $site_id = $site['blog_id'];

        // Switch to the site to get details and posts
        switch_to_blog( $site_id );

        $site_terms = get_terms( $taxonomy, $termargs );

        foreach( $site_terms as $term ) {

            if( !in_array( $term->slug, $term_list ) ) {

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
 * Custom Event Meta
 */
if( !function_exists( 'anp_get_event_meta_list' ) ) {

  function anp_get_event_taxonomy( $event_id = 0 ) {

    $event_id = (int) ( empty( $event_id ) ? get_the_ID() : $event_id );

    if( empty( $event_id ) ){
      return false;
    }

    $html  = '<div class="entry-meta event-meta">';
    $venue = get_taxonomy( 'event-venue' );

    if( get_the_terms( $event_id, 'event-category' ) ) {
      $html .= get_the_term_list( $event_id, 'event-category', '<ul class="category event-category"><li>','</li><li class="cat-item">', '</li></ul>' );
    }

    if( get_the_terms( $event_id, 'event-tag' ) && !is_wp_error( get_the_terms( $event_id, 'event-tag' ) ) ) {
      $html .= get_the_term_list( $event_id, 'event-tag', '<ul class="event-tags tags"><li class="tag-item">','</li><li>', '</li></ul>' );
    }

    $html .='</div>';

    return $html;
  }

}

?>
