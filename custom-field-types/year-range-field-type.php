<?php
/*
 * Year Range field type.. two year-pickers, start and end.
 * Screenshot: http://b.ustin.co/15lKk
 */

/**
 * Example field registration:
 *
 * function yourprefix_register_demo_metabox() {
 *
 * 	$cmb_demo = new_cmb2_box( array(
 * 		'id'            => 'demo_metabox',
 * 		'title'         => __( 'Test Metabox', 'cmb2' ),
 * 		'object_types'  => array( 'page', ),
 * 	) );
 *
 * 	$cmb_demo->add_field( array(
 * 		'name'     => __( 'Date Year Range', 'cmb2' ),
 * 		'desc'     => __( 'field description (optional)', 'cmb2' ),
 * 		'id'       => 'yourprefix_demo_date_year_range',
 * 		'type'     => 'date_year_range',
 * 		'earliest' => 1930, // Set the earliest year that should be shown.
 *   		// Optionally set default values.
 * 		'default'  => array(
 * 			'start'  => 1930,
 * 			'finish' => 'current',
 * 		),
 * 		// 'split_values' => true, // Split values to sep. meta fields.
 * 		// 'start_label'  => 'Start', // Optionally change start text
 * 		// 'finish_label' => 'Finish', // Optionally change finish text
 * 	) );
 *
 * }
 * add_action( 'cmb2_init', 'yourprefix_register_demo_metabox' );
 */


/**
 * Render 'date_year_range' custom field type
 *
 * @since 0.1.0
 *
 * @param array  $field        The passed in `CMB2_Field` object
 * @param mixed  $value        The value of this field escaped.
 *                             It defaults to `sanitize_text_field`.
 *                             If you need the unescaped value, you can access it
 *                             via `$field->value()`
 * @param int    $object_id    The ID of the current object
 * @param string $object_type  The type of object you are working with.
 *                             Most commonly, `post` (this applies to all post-types),
 *                             but could also be `comment`, `user` or `options-page`.
 * @param object $type_object The `CMB2_Types` object
 */
function jt_cmb2_date_year_range( $field, $value, $object_id, $object_type, $type_object ) {
	$earliest = $field->args( 'earliest' );
	$earliest = $earliest ? absint( $earliest ) : 1900;

	$start_label = false !== $field->args( 'start_label' )
		? $field->args( 'start_label' )
		: __( 'Starting Year' );

	$finish_label = false !== $field->args( 'finish_label' )
		? $field->args( 'finish_label' )
		: __( 'Final Year' );

	$separator = false !== $field->args( 'separator' )
		? $field->args( 'separator' )
		: __( ' &mdash; ' );

	$value = wp_parse_args( $value, array(
		'start'  => '',
		'finish' => '',
	) );

	$desc = $field->args( 'description' );
	$field->args['description'] = '';
	$type_object->type = new CMB2_Type_Select( $type_object );

	echo '<em>'. $start_label . '</em> ';
	// echo $type_object->type->render();
	echo $type_object->select( array(
		'name'    => $type_object->_name( '[start]' ),
		'id'      => $type_object->_id( '_start' ),
		'value'   => $value['start'],
		'options' => jt_cmb2_date_year_range_options( $type_object, $earliest, $value['start'] ),
		'desc'    => '',
	) );

	echo $separator;

	echo $type_object->select( array(
		'name'    => $type_object->_name( '[finish]' ),
		'id'      => $type_object->_id( '_finish' ),
		'value'   => $value['finish'],
		'options' => jt_cmb2_date_year_range_options( $type_object, $earliest, $value['finish'] ),
		'desc'    => '',
	) );
	echo ' <em>'. $finish_label . '</em>';

	$field->args['description'] = $desc;

	$type_object->_desc( true, true );

}
add_filter( 'cmb2_render_date_year_range', 'jt_cmb2_date_year_range', 10, 5 );

function jt_cmb2_date_year_range_options( $type_object, $earliest, $value ) {
	$options = array();

	$a = array(
		'value' => '',
		'label' => __( 'Not Set' ),
	);

	if ( cmb2_utils()->isempty( $value ) ) {
		$a['checked'] = 'checked';
	}

	$options[] = $a;

	for ( $i = $earliest; $i <= date( 'Y' ); $i++ ) {

		$a = array( 'value' => $i, 'label' => $i );
		if ( absint( $value ) === $i ) {
			$a['checked'] = 'checked';
		}

		$options[] = $a;
	}

	$a = array(
		'value' => 'current',
		'label' => __( 'Current' ),
	);

	if ( 'current' === $value ) {
		$a['checked'] = 'checked';
	}

	$options[] = $a;

	return implode( "\n", array_map( array( $type_object, 'select_option' ), $options ) );
}

/**
 * Optionally save the values into separate fields.
 */
function jt_cmb2_date_year_range_split_values( $override_value, $value, $object_id, $field_args ) {
	if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
		// Don't do the override
		return $override_value;
	}

	$keys = array( 'start', 'finish' );

	foreach ( $keys as $key ) {
		if ( ! empty( $value[ $key ] ) ) {
			update_post_meta( $object_id, $field_args['id'] . '_'. $key, $value[ $key ] );
		}
	}

	// Tell CMB2 we already did the update
	return true;
}
add_filter( 'cmb2_sanitize_date_year_range', 'jt_cmb2_date_year_range_split_values', 12, 4 );

/**
 * Optionally fetch the values from separate fields as well.
 */
function jt_cmb2_date_year_range_get_split_values( $no_override, $object_id, $args, $field ) {
	if ( 'date_year_range' !== $field->args( 'type' ) || ! $field->args( 'split_values' ) ) {
		return $no_override;
	}

	$value = array(
		'start'  => get_post_meta( $object_id, $args['field_id'] . '_start', 1 ),
		'finish' => get_post_meta( $object_id, $args['field_id'] . '_finish', 1 ),
	);

	return $value;
}
add_filter( 'cmb2_override_meta_value', 'jt_cmb2_date_year_range_get_split_values', 10, 4 );

/**
 * The following snippets are required for allowing the date_year_range field
 * to work as a repeatable field, or in a repeatable group
 */

function jt_cmb2_sanitize_date_year_range( $check, $meta_value, $object_id, $field_args, $sanitizer ) {

	// if not repeatable, bail out.
	if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
	}

	return array_filter( $meta_value );
}
add_filter( 'cmb2_sanitize_date_year_range', 'jt_cmb2_sanitize_date_year_range', 10, 5 );

function jt_cmb2_esc_date_year_range( $check, $meta_value, $field_args, $field_object ) {

	// if not repeatable, bail out.
	if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
	}

	return array_filter( $meta_value );
}
add_filter( 'cmb2_types_esc_date_year_range', 'jt_cmb2_esc_date_year_range', 10, 4 );
