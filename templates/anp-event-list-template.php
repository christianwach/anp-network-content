<?php
/*
* Template for the output of the Network Event List
* Override by placing a file called plugins/wp-network-content-display/list-event.php in your active theme
*/

$venue_id = $post_detail['event_venue']['venue_id'];
$venue_name = $post_detail['event_venue']['venue_name'];
$venue_link = $post_detail['event_venue']['venue_link'];
$venue_address = $post_detail['event_venue']['venue_location'];

$post_class = ( !empty( $post_detail['post_class'] ) ) ? $post_detail['post_class'] : 'event list-item';

$html .= '<li id="post-' . $post_id . '" class="event ' . $post_class . '" role="article">';

$html .= '<header class="entry-header">';

if( !empty( $show_thumbnail ) && !empty( $post_detail['post_image'] ) ) {
	$html .= '<div class="entry-image">';
	$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '" class="entry-image-link">';
	$html .= '<img class="attachment-post-thumbnail wp-post-image item-image" src="' . $post_detail['post_image'] . '">';
	$html .= '</a>';
	$html .= '</div>';
}

$html .= '<h3 class="entry-title event-title">';
$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '" class="post-link">';
$html .= $post_detail['post_title'];
$html .= '</a>';
$html .= '</h3>';

$html .= '<h4 class="entry-meta event-meta">';
$html .= '<span class="event-day">';
$html .= date_i18n( 'l, ', strtotime( $post_detail['event_start_date'] ) );
$html .= '</span>';
$html .= '<time class="event-date" itemprop="startDate" datetime="' . date_i18n( 'Y-m-d H:i:s', strtotime( $post_detail['event_start_date'] ) ) . '">';
$html .= date_i18n( get_option( 'date_format' ), strtotime( $post_detail['event_start_date'] ) );
$html .= '</time> ';
$html .= '<div class="event-time">';
$html .= '<span class="start">';
$html .= date_i18n( get_option( 'time_format' ), strtotime( $post_detail['event_start_date'] ) );
$html .= '</span> ';
$html .= '<span class="end">';
$html .= date_i18n( get_option( 'time_format' ), strtotime( $post_detail['event_end_date'] ) );
$html .= '</span>';
$html .= '</div>';

$html .= '</h4>';
$html .= '</header>';
$html .= '<div class="entry-content event-content">';

if( !empty( $venue_id ) ) {
	$html .= '<div class="event-location event-venue">';

	$html .= '<span class="location-name venue-name">';
	$html .= '<a href="' . $venue_link . '">' . $venue_name . '</a>';
	$html .= '</span>';
	$html .= '<span class="street-address">';
	$html .= $venue_address['address'];
	$html .= '</span> ';
	$html .= '<span class="city-state-postalcode">';
	$html .= '<span class="city">';
	$html .= $venue_address['city'];
	$html .= '</span> ';
	$html .= '<span class="state">';
	$html .= $venue_address['state'];
	$html .= '</span> ';
	$html .= '<span class="postal-code">';
	$html .= $venue_address['postcode'];
	$html .= '</span> ';
	$html .= '</span>';
	$html .= '<span class="country">';
	$html .= $venue_address['country'];
	$html .= '</span>';

	$html .= '</div>';
}
$html .= '<div class="entry-excerpt" itemprop="articleBody">' . $post_detail['post_excerpt'] . '</div>';

if( !empty( $show_meta ) ) {

	$html .= '<footer class="entry-footer">';
	$html .= '<div class="entry-meta event-meta">';

	if( !empty( $show_site_name ) ) {
		$html .= '<span class="site-name"><a href="' . esc_url( $post_detail['site_link'] ) . '">';
		$html .= $post_detail['site_name'];
		$html .= '</a></span>';
	}

	$html .= '<span class="event-author"><a href="' . esc_url( $post_detail['site_link'] . '/author/' . $post_detail['post_author'] ) . '">';
	$html .= $post_detail['post_author'];
	$html .= '</a></span>';

	if( function_exists( 'glocal_get_event_taxonomy' ) ) :
		$html .= '<div class="category tags">';
		$html .= glocal_get_event_taxonomy( $post_id );
		$html .= '</div>';
	endif;

	$html .= '</div>';
	$html .= '</footer>';

}

$html .= '</div>';

$html .= '</li>';
