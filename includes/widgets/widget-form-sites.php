<!-- includes/widgets/widget-form-sites.php -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="title_label"><?php _e( 'Title', 'wp-network-content-display' ); ?></label>
	<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Enter Widget Title', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'number_sites' ); ?>" class="number_sites_label"><?php _e( 'Number of Sites', 'wp-network-content-display' ); ?></label>
	<input type="number" id="<?php echo $this->get_field_id( 'number_sites' ); ?>" name="<?php echo $this->get_field_name( 'number_sites' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '0-100', 'wp-network-content-display' ); ?>" value="<?php echo esc_attr( $number_sites ); ?>">
</p>

<p>
	<label for="exclude_sites" class="exclude_sites_label"><?php _e( 'Exclude Sites', 'wp-network-content-display' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'exclude_sites' ); ?>" name="<?php echo $this->get_field_name( 'exclude_sites' ); ?>[]" multiple="multiple" class="widefat">
		<option value="" <?php selected( empty( $exclude_sites ), '' ); ?>><?php _e( 'None', 'wp-network-content-display' ); ?></option>
		<?php
		$siteargs = array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		);
		$sites = get_sites( $siteargs );
		foreach( $sites as $site ) {
			$site_id = $site->blog_id;
			$site_name = get_blog_details( $site_id )->blogname;
			?>
			<option id="<?php echo $site_id; ?>" value="<?php echo $site_id; ?>"<?php ( ! empty( $exclude_sites ) && in_array( $site_id, $exclude_sites ) ) ? ' selected="selected"' : ''; ?>><?php echo $site_name; ?></option>
			<?php
		}
		?>
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
	<label for="<?php echo $this->get_field_id( 'show_image' ); ?>" class="show_image_label"><?php _e( 'Show Site Image', 'wp-network-content-display' ); ?></label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" class="widefat" placeholder="<?php esc_attr_e( '', 'wp-network-content-display' ); ?>" value="1" <?php checked( $show_image, true ); ?>>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'default_image' ); ?>" class="default_image_label"><?php _e( 'Default Image', 'wp-network-content-display' ); ?></label>
	<input type="text" id="<?php echo $this->get_field_id( 'default_image' ); ?>" name="<?php echo $this->get_field_name( 'default_image' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Enter path/url of default image', 'wp-network-content-display' ); ?>" value="<?php echo esc_url( $default_image ); ?>">
	<input id="<?php echo $this->get_field_id( 'default_image' ); ?>-button" class="upload_image_button button button-primary" type="button" value="<?php esc_attr_e( 'Upload Image', 'wp-network-content-display' ); ?>" />
	<script type="text/javascript">
	jQuery(document).ready( function($) {
		$("#<?php echo $this->get_field_id( 'default_image' ); ?>-button").click( function(e) {
			e.preventDefault();
			var image = wp.media({
				title: "<?php _e( 'Upload Image', 'wp-network-content-display' ); ?>",
				multiple: false
			}).open()
			.on( 'select', function(e) {
				var image_object, image_url;
				// get selected image object from the Media Uploader
				image_object = image.state().get( 'selection' ).first();
				// convert to JSON object and grab URL
				var image_url = image_object.toJSON().url;
				// assign URL to the input field
				$("#<?php echo $this->get_field_id( 'default_image' ); ?>").val( image_url );
			});

		});

	});
	</script>
</p>
