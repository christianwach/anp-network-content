<?php
/*
* Template for the output of the Network Sites as list
* Override by placing a file called plugins/wp-network-content-display/list-sites.php in your active theme
*/


$html .= '<li id="site-' . $site_id . '" data-posts="' . $site['post_count'] . '" data-slug="' . $slug . '" data-id="' . $site_id . '" data-updated="' . $site['last_updated'] . '" class="site-item">' ;
if( !empty( $show_image ) ) {
	$html .= '<a href="' . esc_url( $site['siteurl'] ) . '" class="item-image site-image" title="' . $site['blogname'] . '" style="background-image:url(\''. $site['site-image'] .' \')">';
	$html .= '</a>';
}
$html .= '<header class="entry-header">';
$html .= '<h3 class="entry-title">';
$html .= '<a href="' . esc_url( $site['siteurl'] ) . '">';
$html .= $site['blogname'];
$html .= '</a>';
$html .= '</h3>';
$html .= '</header>';
if( !empty( $show_meta ) ) {
	$html .= '<div class="entry-meta">';

	$html .= '<span class="meta-label">' . __( 'Last Updated', 'wp-network-content-display' ) . '</span> <time>';
	$html .= date_i18n( get_option( 'date_format' ), strtotime( $site['last_updated'] ) );
	$html .= '</time>';

	$html .= '<div class="recent-post">';
	$html .= '<span class="meta-label">' . __( 'Latest Post', 'wp-network-content-display' ) . '</span> ';
	$html .= '<a href="'. esc_url( $site['recent_post']['permalink'] ) .'">';
	$html .= $site['recent_post']['post_title'];
	$html .= '</a>';
	$html .= '<div class="entry-meta">';
	$html .= '<span class="meta-label">' . __( 'Posted On', 'wp-network-content-display' ) . '</span> ';
	$html .= '<time>';
	$html .= date_i18n( get_option( 'date_format' ), strtotime( $site['recent_post']['post_date'] ) );
	$html .= '</time>';
	$html .= '</div>';
	$html .= '</div>';

	$html .= '</div>';
}

$html .= '</li>';
