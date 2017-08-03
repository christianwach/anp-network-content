<?php
/**
 * ANP Network Sites Widget
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.0
 * @package   ANP_Network_Content
 */

/**
 * ANP_Network_Sites_Widget widget
 * Input: User input
 * Effect: Renders widget
 */
class ANP_Network_Sites_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct(
            'anp-network-sites',
            __( 'Network Sites', 'anp-network-content' ),
            array(
                'description' => __( 'Display list of sites in your network.', 'anp-network-content' ),
                'classname'   => 'widget__anp-network-sites',
            )
        );

        add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));

    }

    /**
     * Upload the Javascripts for the media uploader
     */
    public function upload_scripts( ) {

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('upload_media_widget', ANP_NETWORK_CONTENT_PLUGIN_URL . 'js/upload-media.js', array('jquery'));

        wp_enqueue_style('thickbox');
    }


    public function widget( $args, $instance ) {

        extract( $args );

        $title = apply_filters('widget_title', $instance['title']);

        // Convert array to comma-separated string
        if(is_array($instance['exclude_sites']) && (!empty($instance['exclude_sites'][0])) ) {
            $instance['exclude_sites'] = implode(',', $instance['exclude_sites'] );
        } else {
            unset( $instance['exclude_sites'] );
        }


        echo $before_widget;

        // if the title is set
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

        // Use glocal_networkwide_sites function to display sites
        if( function_exists( 'glocal_networkwide_sites_module' ) ) {
            echo glocal_networkwide_sites_module( $instance );
        }

        echo $after_widget;

    }


    public function form( $instance ) {

        // Set default values
        $instance = wp_parse_args( (array) $instance, array(
            'title' => '',
            'number_sites' => '',
            'exclude_sites' => '',
            'sort_by' => '',
            'id' => '',
            'class' => '',
            'show_meta' => true,
            'show_image' => false,
            'default_image' => '',
        ) );

        // Retrieve an existing value from the database
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        $number_sites = !empty( $instance['number_sites'] ) ? $instance['number_sites'] : '';
        $exclude_sites = !empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : '';
        $sort_by = !empty( $instance['sort_by'] ) ? $instance['sort_by'] : '';

        $id = !empty( $instance['id'] ) ? $instance['id'] : '';
        $class = !empty( $instance['class'] ) ? $instance['class'] : '';
        $show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
        $show_image = isset( $instance['show_image'] ) ? (bool) $instance['show_image'] : false;
        $default_image = !empty( $instance['default_image'] ) ? $instance['default_image'] : '';

        // Form fields
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'title' ) . '" class="title_label">' . __( 'Title', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Widget Title', 'anp-network-content' ) . '" value="' . esc_attr( $title ) . '">';
        echo '</p>';

        // Number of Sites
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'number_sites' ) . '" class="number_sites_label">' . __( 'Number of Sites', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'number_sites' ) . '" name="' . $this->get_field_name( 'number_sites' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $number_sites ) . '">';
        echo '</p>';

        // Exclude Sites
        echo '<p>';
        echo '  <label for="exclude_sites" class="exclude_sites_label">' . __( 'Exclude Sites', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'exclude_sites' ) . '" name="' . $this->get_field_name( 'exclude_sites' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $exclude_sites, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $siteargs = array(
            'archived'   => 0,
            'spam'       => 0,
            'deleted'    => 0,
            'public'     => 1,
        );

        $sites = wp_get_sites($siteargs);

        foreach( $sites as $site ) {
            $site_id = $site['blog_id'];
            $site_name = get_blog_details( $site_id )->blogname;
            echo '      <option id="' . $site_id . '" value="' . $site_id . '"', ( ! empty( $exclude_sites ) && in_array( $site_id,  $exclude_sites ) ) ? ' selected="selected"' : '','>' . $site_name . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Sort by
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'sort_by' ) . '" class="sort_by_label">' . __( 'Sort By', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'sort_by' ) . '" name="' . $this->get_field_name( 'sort_by' ) . '" class="widefat">';
        echo '      <option value="blogname" ' . selected( $sort_by, 'blogname', false ) . '> ' . __( 'Alphabetical', 'anp-network-content' );
        echo '      <option value="last_updated" ' . selected( $sort_by, 'last_updated', false ) . '> ' . __( 'Recently Active', 'anp-network-content' );
        echo '      <option value="post_count" ' . selected( $sort_by, 'post_count', false ) . '> ' . __( 'Most Active', 'anp-network-content' );
        echo '      <option value="registered" ' . selected( $sort_by, 'registered', false ) . '> ' . __( 'Newest', 'anp-network-content' );
        echo '  </select>';
        echo '</p>';

        // Widget ID
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'id' ) . '" class="id_label">' . __( 'ID', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter ID', 'anp-network-content' ) . '" value="' . esc_attr( $id ) . '">';
        echo '</p>';

        // Widget Class
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'class' ) . '" class="class_label">' . __( 'Class', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'class' ) . '" name="' . $this->get_field_name( 'class' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Class', 'anp-network-content' ) . '" value="' . esc_attr( $class ) . '">';
        echo '</p>';

        // Default Meta
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_meta' ) . '" class="show_meta_label">' . __( 'Show Meta', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_meta' ) . '" name="' . $this->get_field_name( 'show_meta' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_meta, true, false ) . '>';
        echo '</p>';

        // Show Image
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_image' ) . '" class="show_image_label">' . __( 'Show Site Image', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_image' ) . '" name="' . $this->get_field_name( 'show_image' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_image, true, false ) . '>';
        echo '</p>';

        // Default Image
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'default_image' ) . '" class="default_image_label">' . __( 'Default Image', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'default_image' ) . '" name="' . $this->get_field_name( 'default_image' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter path/url of default image', 'anp-network-content' ) . '" value="' . esc_url( $default_image ) . '">';
        echo '  <input class="upload_image_button button button-primary" type="button" value="Upload Image" />';
        echo '</p>';

    }

    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number_sites'] = !empty( $new_instance['number_sites'] ) ? strip_tags( $new_instance['number_sites'] ) : '';
        $instance['exclude_sites'] = !empty( $new_instance['exclude_sites'] ) ? $new_instance['exclude_sites'] : '';
        $instance['sort_by'] = !empty( $new_instance['sort_by'] ) ? $new_instance['sort_by'] : '';
        $instance['id'] = !empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
        $instance['class'] = !empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
        $instance['show_meta'] = !empty( $new_instance['show_meta'] ) ? true : false;
        $instance['show_image'] = !empty( $new_instance['show_image'] ) ? true : false;
        $instance['default_image'] = !empty( $new_instance['default_image'] ) ? strip_tags( $new_instance['default_image'] ) : '';

        return $instance;

    }

}
