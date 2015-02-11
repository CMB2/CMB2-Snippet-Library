<?php
/*
 * Plugin Name: CMB2 Template Tag - Output File List
 * Description: Custom functions that allow you to get or ouput the file_list images in a specified size
 * Author: jtsternberg
 * Author URI: http://dsgnwrks.pro
 * Version: 0.1.0
 */

/**
 * Outputs a cmb2 file_list
 *
 * @param  string  $file_list_meta_key The field meta key. ($prefix . 'file_list')
 * @param  string  $img_size           Size of image to show
 */
function jt_cmb2_file_list_images( $file_list_meta_key, $img_size = 'medium' ) {
	echo jt_cmb2_get_file_list_images( $file_list_meta_key, $img_size );
}

/**
 * Returns a cmb2 file_list
 *
 * @param  string  $file_list_meta_key The field meta key. ($prefix . 'file_list')
 * @param  string  $img_size           Size of image to show
 * @return string                      The html markup for the images
 */
function jt_cmb2_get_file_list_images( $file_list_meta_key, $img_size = 'medium' ) {

	// Get the list of files
	$files = get_post_meta( get_the_ID(), $file_list_meta_key, 1 );
	$images = '';
	// Loop through them and output an image
	foreach ( (array) $files as $attachment_id => $attachment_url ) {
		$images .= '<div class="file-list-image">';
		$images .= wp_get_attachment_image( $attachment_id, $img_size );
		$images .= '</div>';
	}

	return $images ? '<div class="file-list-wrap">' . $images . '</div>' : '';
}
