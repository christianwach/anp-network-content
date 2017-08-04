<!-- includes/widgets/widget-form-events.php -->
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
		<option value="" <?php selected( empty( $exclude_sites ), '' ); ?>><?php _e( 'None', 'wp-network-content-display' ); ?></option>
		<?php
		$sites = get_sites( array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		) );
		foreach( $sites as $site ) {
			$site_id = $site->blog_id;
			$site_name = get_blog_details( $site_id )->blogname;
			?>
			<option id="<?php echo $site_id; ?>" value="<?php echo $site_id; ?>"<?php ( ! empty( $exclude_sites ) && in_array( $site_id, $exclude_sites) ) ? ' selected="selected"' : ''; ?>><?php echo $site_name; ?></option>
			<?php
		}
		?>
	</select>
</p>

<p>
	<label for="include_event_categories" class="include_event_categories_label"><?php _e( 'Include Categories', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'include_event_categories' ); ?>" name="<?php echo $this->get_field_name( 'include_event_categories' ); ?>[]" multiple="multiple" class="widefat">
		<option value="" <?php selected( empty( $include_event_categories ), '' ); ?>><?php _e( 'None', 'wp-network-content-display' ); ?></option>
		<?php
		$categories = get_sitewide_taxonomy_terms( 'event-category' );
		foreach( $categories as $key => $value ) {
			?>
			<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php ( ! empty( $include_event_categories ) && in_array( $key, $include_event_categories ) ) ? ' selected="selected"' : ''; ?>><?php echo $value; ?></option>
			<?php
		}
		?>
	</select>
</p>

<p>
	<label for="include_event_tags" class="include_event_tags_label"><?php _e( 'Include Tags', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'include_event_tags' ); ?>" name="<?php echo $this->get_field_name( 'include_event_tags' ); ?>[]" multiple="multiple" class="widefat">
		<option value="" <?php selected( empty( $include_event_tags ), '' ); ?>><?php _e( 'None', 'wp-network-content-display' ); ?></option>
		<?php
		$tags = get_sitewide_taxonomy_terms( 'event-tag' );
		foreach( $tags as $key => $value ) {
			?>
			<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php ( ! empty( $include_event_tags ) && in_array( $key,	$include_event_tags ) ) ? ' selected="selected"' : ''; ?>><?php echo $value; ?></option>
			<?php
		}
		?>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'event_scope' ); ?>" class="event_scope_label"><?php _e( 'Event Scope', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'event_scope' ); ?>" name="<?php echo $this->get_field_name( 'event_scope' ); ?>" class="widefat">
		<?php
		$scopes = array(
			'future' => __( 'Future', 'wp-network-content-display' ),
			'past'	 => __( 'Past', 'wp-network-content-display' ),
			'all'	=> __( 'All', 'wp-network-content-display' ),
		);
		foreach( $scopes as $key => $value ) {
			?>
			<option value="<?php echo $key; ?>" <?php selected( $event_scope, $key ); ?>><?php echo $value; ?></option>
			<?php
		}
		?>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'style' ); ?>" class="style_label"><?php _e( 'Display Style', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" class="widefat">
		<?php
		$styles = array(
			''		=> __( 'List (Default)', 'wp-network-content-display' ),
			'block' => __( 'Block', 'wp-network-content-display' )
		);
		foreach( $styles as $key => $value ) {
			?>
			<option value="<?php echo $key; ?>" <?php selected( $style, $key ); ?>><?php echo $value; ?></option>
		}
		?>
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
	<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" class="excerpt_length_label"><?php _e( 'Excerpt Length', 'wp-network-content-display' ); ?></label>
	<input type="number" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '0-100', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $excerpt_length ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'show_site_name' ); ?>" class="show_site_name_label"><?php _e( 'Show Site Name', 'wp-network-content-display' ); ?></label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'show_site_name' ); ?>" name="<?php echo $this->get_field_name( 'show_site_name' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '', 'wp-network-content-display' ); ?>" value="1" <?php checked( $show_site_name, true ); ?>>
</p>
