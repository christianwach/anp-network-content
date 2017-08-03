<?php
/*
* Template for the output of the Network Posts List
* Override by placing a file called plugins/wp-network-content-display/list-post.php in your active theme
*/

$html .= '<li class="type-post post-item siteid-' . $post_detail['site_id'] . '">';
$html .= '<header class="entry-header">';
if( $show_thumbnail && !empty( $post_detail['post_image'] ) ) {
	//Show image
	$html .= '<div class="entry-image">';
	$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '" class="entry-image-link">';
	$html .= '<img class="wp-post-image item-image" src="' . $post_detail['post_image'] . '">';
	$html .= '</a>';
	$html .= '</div>';
}
$html .= '<h4 class="entry-title">';
$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '">';
$html .= $post_detail['post_title'];
$html .= '</a>';
$html .= '</h4>';
$html .= '</header>';

if( !empty( $show_meta ) ) {
	$html .= '<div class="entry-meta">';
	if( !empty( $show_site_name ) ) {
		$html .= '<span class="site-name"><span class="meta-label">' . __( 'Posted In', 'wp-network-content-display' ) . '</span> <a href="' . esc_url( $post_detail['site_link'] ) . '">';
		$html .= $post_detail['site_name'];
		$html .= '</a></span>';
	}
	$html .= '<span class="post-date posted-on date"><span class="meta-label">' . __( 'Posted On', 'wp-network-content-display' ) . '</span> <time class="entry-date" datetime="' . $post_detail['post_date'] . '">';
	$html .= date_i18n( get_option( 'date_format' ), strtotime( $post_detail['post_date'] ) );
	$html .= '</time></span>';
	$html .= '<span class="entry-author"><span class="meta-label">' . __( 'Posted By', 'wp-network-content-display' ) . '</span> <a href="' . esc_url( $post_detail['site_link'] . '/author/' . $post_detail['post_author'] ) . '">';
	$html .= $post_detail['post_author'];
	$html .= '</a></span>';
	$html .= '</div>';
}

if( !empty( $show_excerpt ) ) {
	$html .= '<div class="entry-content" itemprop="articleBody">' . $post_detail['post_excerpt'] . '</div>';
}

if( !empty( $show_meta ) ) {
	$html .= '<div class="entry-meta"><span class="meta-label">' . __( 'Category', 'wp-network-content-display' ) . '</span>';
	$html .= ( !empty( $post_categories ) ) ? '<span class="category tags">' . $post_categories . '</span>' : '';
	$html .= '</div>';
}
$html .= '</li>';
