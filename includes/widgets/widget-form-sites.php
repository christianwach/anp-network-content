<!-- includes/widgets/widget-form-sites.php -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="title_label"><?php _e( 'Title', 'wp-network-content-display' ); ?></label>
	<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Enter Widget Title', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'style' ); ?>" class="style_label"><?php _e( 'Display Style', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" class="widefat">
		<option value="" <?php selected( $style, '' ); ?>><?php _e( 'List (Default)', 'wp-network-content-display' ); ?></option>
		<option value="block" <?php selected( $style, 'block' ); ?>><?php _e( 'Block', 'wp-network-content-display' ); ?></option>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'number_sites' ); ?>" class="number_sites_label"><?php _e( 'Number of Sites', 'wp-network-content-display' ); ?></label>
	<input type="number" id="<?php echo $this->get_field_id( 'number_sites' ); ?>" name="<?php echo $this->get_field_name( 'number_sites' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '0-100', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $number_sites ); ?>">
</p>

<p>
	<label for="exclude_sites" class="exclude_sites_label"><?php _e( 'Exclude Sites', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'exclude_sites' ); ?>" name="<?php echo $this->get_field_name( 'exclude_sites' ); ?>[]" multiple="multiple" class="widefat">
		<option value=""<?php echo ( empty( $exclude_sites ) ) ? ' selected="selected"' : ''; ?>><?php _e( 'None', 'wp-network-content-display' ); ?></option>
		<?php foreach( $sites as $site ) { ?>
			<option id="<?php echo $site->blog_id; ?>" value="<?php echo $site->blog_id; ?>"<?php echo ( ! empty( $exclude_sites ) && in_array( $site->blog_id, $exclude_sites ) ) ? ' selected="selected"' : ''; ?>><?php echo esc_html( get_blog_details( $site->blog_id )->blogname ); ?></option>
		<?php } ?>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'sort_by' ); ?>" class="sort_by_label"><?php _e( 'Sort By', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'sort_by' ); ?>" name="<?php echo $this->get_field_name( 'sort_by' ); ?>" class="widefat">
		<option value="blogname" <?php selected( $sort_by, 'blogname' ); ?>><?php _e( 'Alphabetical', 'wp-network-content-display' ); ?></option>
		<option value="last_updated" <?php selected( $sort_by, 'last_updated' ); ?>><?php _e( 'Recently Active', 'wp-network-content-display' ); ?></option>
		<option value="post_count" <?php selected( $sort_by, 'post_count' ); ?>><?php _e( 'Most Active', 'wp-network-content-display' ); ?></option>
		<option value="registered" <?php selected( $sort_by, 'registered' ); ?>><?php _e( 'Newest', 'wp-network-content-display' ); ?></option>
	</select>
</p>

<p class="show_icon_container">
	<label for="<?php echo $this->get_field_id( 'show_icon' ); ?>" class="show_icon_label"><?php _e( 'Show Site Icon', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'show_icon' ); ?>" name="<?php echo $this->get_field_name( 'show_icon' ); ?>" class="widefat">
		<option value="none" <?php selected( $show_icon, 'none' ); ?>><?php _e( 'Do not show Site Icons', 'wp-network-content-display' ); ?></option>
		<option value="library" <?php selected( $show_icon, 'library' ); ?>><?php _e( 'Default from Media Library', 'wp-network-content-display' ); ?></option>
		<option value="url" <?php selected( $show_icon, 'url' ); ?>><?php _e( 'Default from URL', 'wp-network-content-display' ); ?></option>
	</select>
</p>

<p class="default_image_container">
	<label for="<?php echo $this->get_field_id( 'default_image' ); ?>" class="default_image_label"><?php _e( 'Default Image from URL', 'wp-network-content-display' ); ?></label>
	<input type="text" id="<?php echo $this->get_field_id( 'default_image' ); ?>" name="<?php echo $this->get_field_name( 'default_image' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Enter URL of default image', 'wp-network-content-display' ); ?>" value="<?php echo esc_url( $default_image ); ?>">
</p>

<p class="attachment_id_container">
	<label for="<?php echo $this->get_field_id( 'attachment_id' ); ?>" class="attachment_id_label"><?php _e( 'Default Image from Media Library', 'wp-network-content-display' ); ?></label><br>
	<input id="<?php echo $this->get_field_id( 'default_image' ); ?>-button" class="upload_image_button button button-primary" type="button" value="<?php esc_attr_e( 'Choose an image', 'wp-network-content-display' ); ?>" />
	<input id="<?php echo $this->get_field_id( 'attachment_id' ); ?>" name="<?php echo $this->get_field_name( 'attachment_id' ); ?>" class="wpncd_attachment_id" type="hidden" value="<?php echo esc_url( $attachment_id ); ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'show_meta' ); ?>" class="show_meta_label"><?php _e( 'Show Site Metadata', 'wp-network-content-display' ); ?></label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'show_meta' ); ?>" name="<?php echo $this->get_field_name( 'show_meta' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '', 'wp-network-content-display' ); ?>" value="1" <?php checked( $show_meta, true ); ?>>
</p>
