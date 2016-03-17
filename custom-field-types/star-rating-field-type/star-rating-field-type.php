<?php
/*
 * Plugin Name: CMB2 Custom Field Type - Star Rating
 * Description: Makes available a 'star_rating' CMB2 Custom Field Type. Based on https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types#example-4-multiple-inputs-one-field-lets-create-an-address-field
 * Author: Evan Herman
 * Author URI: https://www.evan-herman.com
 * Version: 0.1.0
 */

/**
 * Template tag for displaying an star rating from the CMB2 star rating field type (on the front-end)
 *
 * @since  0.1.0
 *
 * @param  string  $metakey The 'id' of the 'star rating' field (the metakey for get_post_meta)
 * @param  integer $post_id (optional) post ID. If using in the loop, it is not necessary
 */
function eh_cmb2_star_rating_field( $metakey, $post_id = 0 ) {
	echo eh_cmb2_get_star_rating_field( $metakey, $post_id );
}

/**
 * Template tag for returning a star rating from the CMB2 star rating field type (on the front-end)
 *
 * @since  0.1.0
 *
 * @param  string  $metakey The 'id' of the 'star rating' field (the metakey for get_post_meta)
 * @param  integer $post_id (optional) post ID. If using in the loop, it is not necessary
 */
function eh_cmb2_get_star_rating_field( $metakey, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$rating = get_post_meta( $post_id, $metakey, 1 );
	$stars_container = '<section class="cmb2-star-container">';
	$x = 1;
	$total = 5;
		while( $x <= $rating ) {
			$stars_container .= '<span class="dashicons dashicons-star-filled"></span>';
			$x++;
		}
		if( $rating < $total ) {
			while( $rating < $total ) {
				$stars_container .= '<span class="dashicons dashicons-star-empty"></span>';
				$rating++;
			}
		}
	$stars_container .= '</section>';
	wp_enqueue_style( 'dashicons' );
	return $stars_container;
}

/**
 * Render 'star rating' custom field type
 *
 * @since 0.1.0
 *
 * @param array  $field           			The passed in `CMB2_Field` object
 * @param mixed  $value       				The value of this field escaped.
 *                                   					It defaults to `sanitize_text_field`.
 *                                   					If you need the unescaped value, you can access it
 *                                   					via `$field->value()`
 * @param int    $object_id          		The ID of the current object
 * @param string $object_type        	The type of object you are working with.
 *                                  				 	Most commonly, `post` (this applies to all post-types),
 *                                   					but could also be `comment`, `user` or `options-page`.
 * @param object $field_type_object  	The `CMB2_Types` object
 */
function eh_cmb2_render_star_rating_field_callback( $field, $value, $object_id, $object_type, $field_type_object ) {
	// enqueue styles
	wp_enqueue_style( 'star-rating-metabox-css', plugin_dir_url(__FILE__) . '/css/star-rating-field-type.css', array( 'cmb2-styles' ), 'all', false );
	?>
		<section id="cmb2-star-rating-metabox">
			<fieldset>
				<span class="star-cb-group">
					<?php
						$y = 5;
						while( $y > 0 ) {
							?>
								<input type="radio" id="rating-<?php echo $y; ?>" name="<?php echo $field_type_object->_id( false ); ?>" value="<?php echo $y; ?>" <?php checked( $value, $y ); ?>/>
								<label for="rating-<?php echo $y; ?>"><?php echo $y; ?></label>
							<?php
							$y--;
						}
					?>
				</span>
			</fieldset>
		</section>
	<?php
	echo $field_type_object->_desc( true );

}
add_filter( 'cmb2_render_star_rating', 'eh_cmb2_render_star_rating_field_callback', 10, 5 );