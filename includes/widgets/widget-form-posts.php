<!-- includes/widgets/widget-form-posts.php -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="title_label"><?php _e( 'Title', 'wp-network-content-display' ); ?></label>
	<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Enter Widget Title', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'number_posts' ); ?>" class="number_posts_label"><?php _e( 'Number of Posts', 'wp-network-content-display' ); ?></label>
	<input type="number" id="<?php echo $this->get_field_id( 'number_posts' ); ?>" name="<?php echo $this->get_field_name( 'number_posts' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '0-100', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $number_posts ); ?>">
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
	<label for="include_categories" class="include_categories_label"><?php _e( 'Include Categories', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'include_categories' ); ?>" name="<?php echo $this->get_field_name( 'include_categories' ); ?>[]" multiple="multiple" class="widefat">
		<option value=""<?php echo ( empty( $include_categories ) ) ? ' selected="selected"' : ''; ?>><?php _e( 'None', 'wp-network-content-display' ); ?></option>
		<?php foreach( $categories as $cat ) { ?>
			<option id="<?php echo $cat->slug; ?>" value="<?php echo $cat->slug; ?>"<?php echo ( ! empty( $include_categories ) && in_array( $cat->slug, $include_categories ) ) ? ' selected="selected"' : ''; ?>><?php echo $cat->name; ?></option>
		<?php } ?>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'posts_per_site' ); ?>" class="posts_per_site_label"><?php _e( 'Posts per Site', 'wp-network-content-display' ); ?></label>
	<input type="number" id="<?php echo $this->get_field_id( 'posts_per_site' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_site' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '0-100', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $posts_per_site ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'style' ); ?>" class="style_label"><?php _e( 'Display Style', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" class="widefat">
		<option value="" <?php selected( $style, '' ); ?>><?php _e( 'List (Default)', 'wp-network-content-display' ); ?></option>
		<option value="block" <?php selected( $style, 'block' ); ?>><?php _e( 'Block', 'wp-network-content-display' ); ?></option>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'id' ); ?>" class="id_label"><?php _e( 'ID', 'wp-network-content-display' ); ?></label>
	<input type="text" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Enter ID', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $id ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'class' ); ?>" class="class_label"><?php _e( 'Class', 'wp-network-content-display' ); ?></label>
	<input type="text" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Enter Class', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $class ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'show_meta' ); ?>" class="show_meta_label"><?php _e( 'Show Meta', 'wp-network-content-display' ); ?></label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'show_meta' ); ?>" name="<?php echo $this->get_field_name( 'show_meta' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '', 'wp-network-content-display' ); ?>" value="1" <?php checked( $show_meta, true ); ?>>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" class="show_thumbnail_label"><?php _e( 'Show Thumbnail', 'wp-network-content-display' ); ?></label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '', 'wp-network-content-display' ); ?>" value="1" <?php checked( $show_thumbnail, true ); ?>>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" class="show_excerpt_label"><?php _e( 'Show Excerpt', 'wp-network-content-display' ); ?></label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '', 'wp-network-content-display' ); ?>" value="1" <?php checked( $show_excerpt, true ); ?>>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" class="excerpt_length_label"><?php _e( 'Excerpt Length', 'wp-network-content-display' ); ?></label>
	<input type="number" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '0-100', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $excerpt_length ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'show_site_name' ); ?>" class="show_site_name_label"><?php _e( 'Show Site Name', 'wp-network-content-display' ); ?></label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'show_site_name' ); ?>" name="<?php echo $this->get_field_name( 'show_site_name' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '', 'wp-network-content-display' ); ?>" value="1" <?php checked( $show_site_name, true ); ?>>
</p>
