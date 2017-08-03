<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'anp_network_content_load_widgets' );

/**
 * Register our widget.
 *
 * @since 0.1
 */
function anp_network_content_load_widgets() {
    register_widget( 'ANP_Network_Sites_Widget' );
	register_widget( 'ANP_Network_Posts_Widget' );
    register_widget( 'ANP_Network_Events_Widget' );
}
