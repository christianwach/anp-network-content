<?php

/**
 * Network Content Shortcake UI
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.6.2
 * @package   WP_Network_Content_Display
 */



/**
 * Provide compatibility with Shortcake UI plugin.
 *
 * @link https://github.com/wp-shortcake/shortcake/wiki/Registering-Shortcode-UI
 */
function glocal_network_posts_shortcode_ui() {

	// Bail if Shortcake plugin is not present
	if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		return;
	}

	shortcode_ui_register_for_shortcode( 'embed_network_posts', array(
		'label' => __( 'Network Posts', 'wp-network-content-display' ),
		'listItemImage' => 'dashicons-admin-post',
		'attrs' => array(
			array(
				'label' => __( 'Number of Posts', 'wp-network-content-display' ),
				'attr' => 'number_posts',
				'type' => 'number',
			)
		)
	) );

	// Requires that Event Organizer plugin is active
	if ( function_exists( 'eventorganiser_register_script' ) ) {

		shortcode_ui_register_for_shortcode( 'embed_network_events', array(
			'label' => __( 'Network Events', 'wp-network-content-display' ),
			'listItemImage' => 'dashicons-calendar-alt',
			'attrs' => array(
				array(
					'label' => __( 'Number of Events', 'wp-network-content-display' ),
					'attr' => 'number_posts',
					'type' => 'number',
				),
				array(
					'label' => __( 'Event Scope', 'wp-network-content-display' ),
					'attr' => 'event_scope',
					'type' => 'select',
					'options' => array(
						'future' => __( 'Future', 'wp-network-content-display' ),
						'past' => __( 'Past', 'wp-network-content-display' ),
						'all' => __( 'All', 'wp-network-content-display' ),
					),
				),
			)
		)
		);

	}

}

add_action( 'init', 'glocal_network_posts_shortcode_ui' );
