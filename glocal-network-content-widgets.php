<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'glocal_network_content_load_widgets' );

/**
 * Register our widget.
 *
 * @since 0.1
 */
function glocal_network_content_load_widgets() {
    register_widget( 'Glocal_Network_Sites_Widget' );
	register_widget( 'Glocal_Network_Posts_Widget' );
    register_widget( 'Glocal_Network_Events_Widget' );
}
