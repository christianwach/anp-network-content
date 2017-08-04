<?php

/**
 * Outputs the Network Posts Shortcode and Widget as blocks.
 *
 * Override by placing a copy of this file in:
 * 'plugins/wp-network-content-display/post-block.php'
 * in your active theme's directory.
 *
 * @since 1.0.0
 */

?>

<article id="post-<?php echo get_the_ID(); ?>" class="post entry hentry" role="article">

	<header class="entry-header">

		<?php if ( $show_thumbnail && ! empty( $post_detail['post_image'] ) ) { ?>
			<div class="entry-image">
				<a href="<?php echo esc_url( $post_detail['permalink'] ) ; ?>" class="entry-image-link"><img class="attachment-post-thumbnail wp-post-image item-image" src="<?php echo esc_url( $post_detail['post_image'] ); ?>"></a>
			</div>
		<?php } ?>

		<h3 class="entry-title"><a href="<?php echo esc_url( $post_detail['permalink'] ) ; ?>"><?php echo $post_detail['post_title']; ?></a></h3>

		<?php if ( ! empty( $show_meta ) ) { ?>
			<div class="entry-meta">
				<?php if ( ! empty( $show_site_name ) ) { ?>
					<span class="site-name">
						<span class="meta-label"><?php _e( 'Posted In', 'wp-network-content-display' ) ; ?></span> <a href="<?php echo esc_url( $post_detail['site_link'] ); ?>"><?php echo $post_detail['site_name']; ?></a>
					</span>
				<?php } ?>

				<span class="post-date posted-on date">
					<span class="meta-label"><?php _e( 'Posted On', 'wp-network-content-display' ) ; ?></span> <time class="entry-date" datetime="<?php echo $post_detail['post_date'] ; ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $post_detail['post_date'] ) ); ?></time>
				</span>

				<span class="entry-author">
					<span class="label"><?php _e( 'Posted By', 'wp-network-content-display' ) ; ?></span> <a href="<?php echo esc_url( $post_detail['site_link'] . '/author/' . $post_detail['post_author'] ); ?>"><?php echo $post_detail['post_author']; ?></a>
				</span>
			</div>
		<?php } ?>

	</header>

	<?php if ( ! empty( $show_excerpt ) ) { ?>
		<div class="entry-content">
			<?php echo $post_detail['post_excerpt']; ?>
		</div><!-- /.entry-content -->
	<?php } ?>

	<?php if ( ! empty( $show_meta ) ) { ?>
		<footer class="entry-footer">
			<div class="entry-meta">
				<span class="category cat-links tags"><?php echo $post_categories; ?></span>
			</div>
		</footer>
	<?php } ?>

</article>
