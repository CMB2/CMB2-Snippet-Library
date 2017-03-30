<?php
/**
 * Just a collection of random helper functions for CMB2.
 * If you borrow these, please replace "yourprefix_" with something unique to your project.
 */

/**
 * If it exists, handles uploading a file from the $_FILES array, and setting data to CMB2 file field.
 *
 * @param  string $files_key Array key in $_FILES array.
 * @param  int    $post_id   Post ID
 * @param  string $meta_key  The CMB2 'file' field ID.
 * @param  string $title     Override the default file title handling.
 *
 * @return array|WP_Error
 */
function yourprefix_upload_file_for_cmb2( $files_key, $post_id, $meta_key = '', $title = '' ) {
	if (
		empty( $_FILES[ $files_key ] )
		|| isset( $_FILES[ $files_key ]['error'] ) && 0 !== $_FILES[ $files_key ]['error']
	) {
		return false;
	}

	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	$post_data = array();

	if ( $title ) {
		$post_data['post_title'] = get_the_title( $post_id ) . ': Flyer';
	}

	$id = media_handle_upload( $files_key, $post_id, $post_dataarray );

	if ( is_wp_error( $id ) ) {
		return $id;
	}

	$src = wp_get_attachment_url( $id );

	if ( $meta_key ) {
		update_post_meta( $post_id, $meta_key, $src );
		update_post_meta( $post_id, "{$meta_key}_id", $id );
	}

	return compact( 'src', 'id' );
}


/**
 * Handles sideloading a file from given $file_url, and setting data to CMB2 file field.
 *
 * @param  string $file_url URL to file to sideload.
 * @param  int    $post_id  Post ID
 * @param  string $meta_key The CMB2 'file' field ID.
 * @param  string $title    Override the default file title handling.
 *
 * @return array|WP_Error
 */
function yourprefix_sideload_file_for_cmb2( $file_url, $post_id, $meta_key, = '', $title = null ) {
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	$parts = explode( '/', $file_url );

	$file_array = array();
	$file_array['name'] = end( $parts );

	// Download file to temp location.
	$file_array['tmp_name'] = download_url( $file_url );

	// If error storing temporarily, return the error.
	if ( is_wp_error( $file_array['tmp_name'] ) ) {
		return $file_array['tmp_name'];
	}

	// Do the validation and storage stuff.
	$id = media_handle_sideload( $file_array, $post_id, $title );

	// If error storing permanently, unlink.
	if ( is_wp_error( $id ) ) {
		@unlink( $file_array['tmp_name'] );
		return $id;
	}

	$src = wp_get_attachment_url( $id );

	if ( $meta_key ) {
		update_post_meta( $post_id, $meta_key, $src );
		update_post_meta( $post_id, "{$meta_key}_id", $id );
	}

	return compact( 'src', 'id' );
}

/**
 * If you want oembed filters to apply to the wysiwyg content, add this helper function to your theme or plugin.
 *
 * @link  https://github.com/WebDevStudios/CMB2/wiki/Field-Types#notes-2 Wiki
 *
 * @param  string  $meta_key The CMB2 text field (e.g. 'wysiwyg', 'textarea', etc) ID.
 * @param  int     $post_id  Post ID
 *
 * @return string            If the field has a value, returns the formatted value;
 */
function yourprefix_get_cmb2_wysiwyg_output( $meta_key, $post_id = 0 ) {
	global $wp_embed;

	$post_id = $post_id ? $post_id : get_the_id();

	$content = get_post_meta( $post_id, $meta_key, 1 );
	if ( ! $content ) {
		return '';
	}

	$content = $wp_embed->autoembed( $content );
	$content = $wp_embed->run_shortcode( $content );
	$content = wpautop( $content );
	$content = do_shortcode( $content );

	return $content;
}

/**
 * Sample template tag function for outputting a cmb2 file_list.
 *
 * @link  https://github.com/WebDevStudios/CMB2/wiki/Field-Types#sample-function-for-getting-and-outputting-file_list-images Wiki
 *
 * @param  string  $meta_key The 'file_list' field meta key.
 * @param  string  $img_size Size of image to display.
 */
function yourprefix_cmb2_output_file_list( $meta_key, $img_size = 'medium' ) {

	// Get the list of files
	$files = get_post_meta( get_the_ID(), $meta_key, 1 );

	echo '<div class="file-list-wrap">';
	// Loop through them and output an image
	foreach ( (array) $files as $attachment_id => $attachment_url ) {
		echo '<div class="file-list-image">';
		echo wp_get_attachment_image( $attachment_id, $img_size );
		echo '</div>';
	}
	echo '</div>';
}
