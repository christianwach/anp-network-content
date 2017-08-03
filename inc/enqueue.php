<?php

/**
 * ANP Network Content Enqueue
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */


// Inputs: uses global variable $styles
// Output: conditionally renders stylesheet using WP add_action() method
// Conditionals don't work. Loading on all pages...

if(! function_exists( 'load_highlight_styles' ) ) {

    function load_highlight_styles() {
        wp_enqueue_style( 'anp-network-posts', ANP_NETWORK_CONTENT_PLUGIN_URL . 'stylesheets/css/style.min.css' );
    }

    add_action('wp_enqueue_scripts','load_highlight_styles', 200);

}
