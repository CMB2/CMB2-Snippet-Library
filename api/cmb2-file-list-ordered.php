<?php

/**
 * By default, when fetching the value for the file_list field via the REST API, the order is
 * obliterated when the data is converted to JSON via json_encode. This replaces the default value
 * of the file_list field with an array of arrays, which will preserve the orginal order.
 *
 * @see https://github.com/CMB2/CMB2/issues/1150
 */

/**
 * Returns an array of file arrays with id/url paramaters, which preserves the original file ordering.
 *
 * @param mixed      $value The value from CMB2_Field::get_data()
 * @param CMB2_Field $field The field object.
 */
function cmb2_ordered_file_list_array_in_api( $value, $field ) {
	if ( ! empty( $value ) && is_array( $value ) ) {
		$files = $value;
		$value = array();
		foreach ( $files as $file_id => $file_url ) {
			$value[] = array(
				'id' => $file_id,
				'url' => $file_url,
			);
		}
	}

	return $value;
}

/**
 * Filters the value before it is sent to the REST request.
 *
 * "_yourprefix_demo_file_list" is a dynamic portion of the hook name, referring to the field id.
 */
add_filter( 'cmb2_get_rest_value_for__yourprefix_demo_file_list', 'cmb2_ordered_file_list_array_in_api', 10, 2 );