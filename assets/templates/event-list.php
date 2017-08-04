<?php

/**
 * Outputs the Network Events Shortcode and Widget as an HTML list.
 *
 * Override by placing a copy of this file in:
 * 'plugins/wp-network-content-display/event-list.php'
 * in your active theme's directory.
 *
 * @since 1.0.0
 */

?>

<li id="post-<?php echo $post_id; ?>" class="event <?php echo $post_class; ?>" role="article">

	<header class="entry-header">

		<?php if ( ! empty( $show_thumbnail ) && ! empty( $post_detail['post_image'] ) ) { ?>
			<div class="entry-image">
				<a href="<?php echo esc_url( $post_detail['permalink'] ); ?>" class="entry-image-link"><img class="attachment-post-thumbnail wp-post-image item-image" src="<?php echo esc_url( $post_detail['post_image'] ); ?>"></a>
			</div>
		<?php } ?>

		<h3 class="entry-title event-title"><a href="<?php echo esc_url( $post_detail['permalink'] ); ?>" class="post-link"><?php echo $post_detail['post_title']; ?></a></h3>

		<h4 class="entry-meta event-meta">
			<span class="event-day"><?php echo date_i18n( 'l, ', strtotime( $post_detail['event_start_date'] ) ); ?></span>
			<time class="event-date" itemprop="startDate" datetime="<?php echo date_i18n( 'Y-m-d H:i:s', strtotime( $post_detail['event_start_date'] ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $post_detail['event_start_date'] ) ); ?></time>
			<div class="event-time">
				<span class="start"><?php echo date_i18n( get_option( 'time_format' ), strtotime( $post_detail['event_start_date'] ) ); ?></span>
				<span class="end"><?php echo date_i18n( get_option( 'time_format' ), strtotime( $post_detail['event_end_date'] ) ); ?></span>
			</div>
		</h4>

	</header>

	<div class="entry-content event-content">

		<?php if ( ! empty( $venue_id ) ) { ?>
			<div class="event-location event-venue">
				<span class="location-name venue-name"><a href="<?php echo $venue_link; ?>"><?php echo $venue_name; ?></a></span>
				<span class="street-address"><?php echo $venue_address['address']; ?></span>
				<span class="city-state-postalcode">
					<span class="city"><?php echo $venue_address['city']; ?></span>
					<span class="state"><?php echo $venue_address['state']; ?></span>
					<span class="postal-code"><?php echo $venue_address['postcode']; ?></span>
				</span>
				<span class="country"><?php echo $venue_address['country']; ?></span>
			</div>
		<?php } ?>

		<div class="entry-excerpt" itemprop="articleBody">
			<?php echo $post_detail['post_excerpt']; ?>
		</div>

		<?php if ( ! empty( $show_meta ) ) { ?>
			<footer class="entry-footer">
				<div class="entry-meta event-meta">
					<?php if ( ! empty( $show_site_name ) ) { ?>
						<span class="site-name"><a href="<?php echo esc_url( $post_detail['site_link'] ); ?>"><?php echo $post_detail['site_name']; ?></a></span>
					<?php } ?>
					<span class="event-author"><a href="<?php echo esc_url( $post_detail['site_link'] . '/author/' . $post_detail['post_author'] ); ?>"><?php echo $post_detail['post_author']; ?></a></span>
					<?php if ( function_exists( 'glocal_get_event_taxonomy' ) ) : ?>
						<div class="category tags">
							<?php echo glocal_get_event_taxonomy( $post_id ); ?>
						</div>
					<?php endif; ?>
				</div>
			</footer>
		<?php } ?>

	</div><!-- /.entry-content -->

</li>
