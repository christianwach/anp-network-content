<?php

/**
 * Network Content Display Constructors
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   WP_Network_Content_Display
 */



/**
 * NETWORK POSTS MAIN FUNCTION.
 *
 * Get (or render) posts from sites across the network.
 *
 * 1/5/2016: Updated to allow for custom post types.
 *
 * Editable Templates
 * ---
 * Display of Network Content can be customized by adding a custom template to your theme in 'plugins/wp-network-content-display/'
 * block-event.php
 * list-event.php
 * block-post.php
 * highlights-post.php
 * list-post.php
 * list-sites.php
 *
 * @param array $parameters An array of settings with the following options:
 *    post_type (string) - post type to display ( default: 'post' )
 *    event_scope (string) - timeframe of events, 'future', 'past', 'all' (default: 'future') - ignored if post_type !== 'event'
 *    number_posts (int) - the total number of posts to display ( default: 10 )
 *    posts_per_site (int) - the number of posts for each site ( default: no limit )
 *    include_categories (array) - the categories of posts to include ( default: all categories )
 *    exclude_sites (array) - the sites from which posts should be excluded ( default: all sites ( public sites, except archived, deleted and spam ) )
 *    output (string) - HTML or array ( default: HTML )
 *    style - (string) normal ( list ), block or highlights ( default: normal ) - ignored if @output is 'array'
 *    id (int) - ID used in list markup ( default: network-posts-RAND ) - ignored if @output is 'array'
 *    class (string) - class used in list markup ( default: post-list ) - ignored if @output is 'array'
 *    title (string) - title displayed for list ( default: Posts ) - ignored unless @style is 'highlights'
 *    title_image (string) - image displayed behind title ( default: home-highlight.png ) - ignored unless @style is 'highlights'
 *    show_thumbnail (bool) - display post thumbnail ( default: False ) - ignored if @output is 'array'
 *    show_meta (bool) - if meta info should be displayed ( default: True ) - ignored if @output is 'array'
 *    show_excerpt (bool) - if excerpt should be displayed ( default: True ) - ignored if @output is 'array' or if @show_meta is False
 *    excerpt_length (int) - number of words to display for excerpt ( default: 50 ) - ignored if @show_excerpt is False
 *    show_site_name (bool) - if site name should be displayed ( default: True ) - ignored if @output is 'array'
 * @return array $posts_list The array of posts.
 */
function glocal_networkwide_posts_module( $parameters = [] ) {

    // Default parameters
    $defaults = array(
        'post_type' => (string) 'post', // (string) - post, event
        'number_posts' => (int) 10, // (int)
        'exclude_sites' => array(),
        'include_categories' => array(),
        'posts_per_site' => (int) null, // (int)
        'output' => (string) 'html', // (string) - html, array
        'style' => (string) 'normal', // (string) - normal
        'id' => (string) 'network-posts-' . rand(), // (string)
        'class' => (string) 'post-list', // (string)
        'title' => (string) 'Posts', // (string)
        'title_image' => (string) null, // (string)
        'show_meta' => (bool) True, // (bool)
        'show_thumbnail' => (bool) False, // (bool)
        'show_excerpt' => (bool) True, // (bool)
        'excerpt_length' => (int) 55, // (int)
        'show_site_name' => (bool) True, // (bool)
        'event_scope' => (string) 'future', // (string) - future, past, all
        'include_event_categories' => array(), // (array) - event-category (term name) to include
        'include_event_tags' => array(), // (array) - event-tag (term name) to include
    );

    // SANITIZE INPUT
    $parameters = WP_Network_Content_Display_Helpers::sanitize_input( $parameters );

    if ( isset( $parameters['exclude_sites'] ) && !empty( $parameters['exclude_sites'] ) ) {
        $parameters['exclude_sites'] = explode( ',', $parameters['exclude_sites'] );
    }

    if ( isset( $parameters['include_event_categories'] ) && !empty( $parameters['include_event_categories'] ) ) {
        $parameters['include_event_categories'] = explode( ',', $parameters['include_event_categories'] );
    }

    if ( isset( $parameters['include_event_tags'] ) && !empty( $parameters['include_event_tags'] ) ) {
        $parameters['include_event_tags'] = explode( ',', $parameters['include_event_tags'] );
    }

    // CALL MERGE FUNCTION
    $settings = WP_Network_Content_Display_Helpers::get_merged_settings( $parameters, $defaults );

    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );

    // CALL SITES FUNCTION
    $sites_list = get_sites_list( $settings );

    // CALL GET POSTS FUNCTION
    $posts_list = get_posts_list( $sites_list, $settings );

    if ( $output == 'array' ) {

        // Return an array
        return $posts_list;

        // Debug
        //return '<pre>glocal_networkwide_posts_module $posts_list ' . var_dump( $posts_list ) . '</pre>';

    } else {

        // CALL RENDER FUNCTION
        return render_html( $posts_list, $settings );

    }

}


/**
 * NETWORK SITES MAIN FUNCTION.
 *
 * Gets (or renders) a list of sites.
 *
 * @param array $parameters An array of settings with the following options:
 *    return - Return ( display list of sites or return array of sites ) ( default: display )
 *    number_sites - Number of sites to display/return ( default: no limit )
 *    exclude_sites - ID of sites to exclude ( default: 1 ( usually, the main site ) )
 *    sort_by - newest, updated, active, alpha ( registered, last_updated, post_count, blogname ) ( default: alpha )
 *    default_image - Default image to display if site doesn't have a custom header image ( default: none )
 *    instance_id - ID name for site list instance ( default: network-sites-RAND )
 *    class_name - CSS class name( s ) ( default: network-sites-list )
 *    hide_meta - Select in order to update date and latest post. Only relevant when return = 'display'. ( default: false )
 *    show_image - Select in order to hide site image. ( default: false )
 *    show_join - Future
 *    join_text - Future
 * @return array $sites_list The array of sites.
 */
function glocal_networkwide_sites_module( $parameters = [] ) {

    /** Default parameters **/
    $defaults = array(
        'return' => (string) 'display',
        'number_sites' => (int) null,
        'exclude_sites' => array(),
        'sort_by' => (string) 'alpha',
        'default_image' => (string) null,
        'show_meta' => (bool) False,
        'show_image' => (bool) False,
        'id' => (string) 'network-sites-' . rand(),
        'class' => (string) 'network-sites-list',
    );

    // CALL MERGE FUNCTION
    $settings = wp_parse_args( $parameters, $defaults );

    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );

    // CALL GET SITES FUNCTION
    $sites_list = get_sites_list( $settings );

    // Sorting
    switch ( $sort_by ) {

        case 'newest':
            $sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'registered', 'DESC' );
            break;

        case 'updated':
            $sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'last_updated', 'DESC' );
            break;

        case 'active':
            $sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'post_count', 'DESC' );
            break;

        default:
            $sites_list = WP_Network_Content_Display_Helpers::sort_array_by_key( $sites_list, 'blogname' );

    }

    if ( $return == 'array' ) {
        return $sites_list;
    } else {
	    // CALL RENDER FUNCTION
        return render_sites_list( $sites_list, $settings );
    }

}
