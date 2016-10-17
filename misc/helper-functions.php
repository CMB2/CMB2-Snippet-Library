<?php
/**
 * Miscellaneous CMB2 Helper functions
 */


/**
 * Save fields data from an array of data (Likely $_POST data.)
 *
 * @link  https://wordpress.org/support/topic/sanitizing-data-outside-metabox-context Forum post
 *
 * @param  mixed  $meta_box_id  Metabox ID (or metabox config array)
 * @param  int    $object_id    ID of post/user/comment/options-page to save the data against
 * @param  array  $data_to_save Array of key => value data for saving. Likely $_POST data.
 */
function cmb2_save_metabox_fields_data( $meta_box_id, $object_id, array $data_to_save ) {
	$cmb = cmb2_get_metabox( $meta_box_id, $object_id );
	$cmb->save_fields( $object_id, $cmb->object_type(), $data_to_save );
}

/**
 * Returns array of sanitized field values (without saving them) from
 * provided array of values (Likely $_POST data.)
 *
 * Combined with the metabox `'save_fields' => false` config option, you can use this to
 * save the box data somewhere else.
 *
 * @link  https://wordpress.org/support/topic/sanitizing-data-outside-metabox-context Forum post
 *
 * @param  mixed  $meta_box_id  Metabox ID (or metabox config array)
 * @param  int    $object_id    ID of post/user/comment/options-page to save the data against
 * @param  array  $data_to_save Array of key => value data for saving. Likely $_POST data.
 */
function cmb2_get_metabox_sanitized_values( $meta_box_id, array $data_to_save ) {
	$cmb = cmb2_get_metabox( $meta_box_id );
	$cmb->get_sanitized_values( $data_to_save );
}
