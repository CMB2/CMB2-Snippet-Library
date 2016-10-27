<?php
/**
 * This file demonstrates providing a default value for a group field from a JSON blob.
 * It is using the fields from the group field example in:
 * [example-functions.php](https://github.com/WebDevStudios/CMB2/blob/3b0cc0a696f70d1f3ce7c94af8318f4dd3f34688/example-functions.php#L460-L522).
 */

/**
 * Filters the value for our group field.
 *
 * @param mixed $value     The value get_metadata() should
 *                         return - a single metadata value,
 *                         or an array of values.
 *
 * @param int   $object_id Object ID.
 *
 * @param array $args {
 *     An array of arguments for retrieving data
 *
 *     @type string $type     The current object type
 *     @type int    $id       The current object ID
 *     @type string $field_id The ID of the field being requested
 *     @type bool   $repeat   Whether current field is repeatable
 *     @type bool   $single   Whether current field is a single database row
 * }
 *
 * @param CMB2_Field object $field This field object
 *
 * @return array The group field value array.
 */
function yourprefix_get_default_group_value_from_json( $value, $object_id, $args, $field ) {
	static $defaults = null;

	// Only set the default if the original value has not been overridden,
	// and if there is no existing value.
	if ( 'cmb2_field_no_override_val' !== $value ) {
		return $value;
	}

	// Get the value for the field.
	$data = 'options-page' === $args['type']
		? cmb2_options( $args['id'] )->get( $args['field_id'] )
		: get_metadata( $args['type'], $args['id'], $args['field_id'], ( $args['single'] || $args['repeat'] ) );

	// Get the default values from JSON
	if ( null === $defaults ) {
		// Get your JSON blob.. hard-coded for demo.
		$json = '[{"description":"This is a <strong>description<\/strong>","image":"\/wp-content\/uploads\/2016\/10\/default-image-1.jpg","image_id":663},{"title":"2nd Title","description":"This is a second <strong>description<\/strong>","image":"\/wp-content\/uploads\/2016\/10\/default-image-2.jpg","image_id":655,"image_caption":"This is an image caption."}]';

		$defaults = json_decode( $json, 1 );
	}

	// Set our group field value to the default.
	$value = $defaults;

	// If the group field's retrieved value is not empty...
	if ( ! empty( $data ) ) {
		$value = array();
		// Then loop the defaults and mash the field's value up w/ the default.
		foreach ( $defaults as $key => $default_group_val ) {
			$value[ $key ] = isset( $data[ $key ] )
				? wp_parse_args( $data[ $key ], $default_group_val )
				: $default_group_val;
		}
	}

	return $value;
}

add_filter( 'cmb2_override_yourprefix_group_demo_meta_value', 'yourprefix_get_default_group_value_from_json', 10, 4 );
