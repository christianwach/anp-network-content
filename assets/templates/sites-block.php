<?php

/**
 * Outputs the Network Sites Shortcode and Widget as an HTML list.
 *
 * Override by placing a copy of this file in:
 * 'plugins/wp-network-content-display/sites-list.php'
 * in your active theme's directory.
 *
 * @since 2.0.0
 */

?>

<article id="site-<?php echo $site_id; ?>" class="site-item hentry" role="article">

	<header class="entry-header">

		<?php if ( ! empty( $show_image ) AND ! empty( $site['site-image'] ) ) { ?>
			<a href="<?php echo esc_url( $site['siteurl'] ); ?>" class="item-image site-image" title="<?php echo esc_attr( $site['blogname'] ); ?>"><img class="wp-site-image item-image" src="<?php echo $site['site-image']; ?>"></a>
		<?php } ?>

		<h3 class="entry-title"><a href="<?php echo esc_url( $site['siteurl'] ); ?>"><?php echo $site['blogname']; ?></a></h3>

	</header>

	<?php if ( ! empty( $show_meta ) ) { ?>
		<div class="entry-meta">
			<span class="meta-label"><?php _e( 'Last Updated', 'wp-network-content-display' ); ?></span> <time><?php echo date_i18n( get_option( 'date_format' ), strtotime( $site['last_updated'] ) ); ?></time>
			<div class="recent-post">
				<ul>
					<li>
						<span class="meta-label"><?php _e( 'Latest Post', 'wp-network-content-display' ); ?></span> <a href="<?php echo esc_url( $site['recent_post']['permalink'] ); ?>"><?php echo $site['recent_post']['post_title']; ?></a>
					</li>
					<li>
						<span class="meta-label"><?php _e( 'Posted On', 'wp-network-content-display' ); ?></span> <time><?php echo date_i18n( get_option( 'date_format' ), strtotime( $site['recent_post']['post_date'] ) ); ?> </time>
					</li>
				</ul>
			</div>
		</div>
	<?php } ?>

</article>
