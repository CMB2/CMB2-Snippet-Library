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
 *    $cmb_demo = new_cmb2_box( array(
 *        'id'            => 'demo_metabox',
 *        'title'         => __( 'Test Metabox', 'cmb2' ),
 *        'object_types'  => array( 'page', ),
 *    ) );
 *
 *    $cmb_demo->add_field( array(
 *        'name'     => __( 'Date Year Range', 'cmb2' ),
 *        'desc'     => __( 'field description (optional)', 'cmb2' ),
 *        'id'       => 'yourprefix_demo_date_year_range',
 *        'type'     => 'date_year_range',
 *        // Optionally set default values.
 *        'options'  => array(
 *             'earliest' => 1930, // Set the earliest year that should be shown.
 *            // 'start_reverse_sort' => true,
 *            // 'finish_reverse_sort' => false,
 *        ),
 *        'default'  => array(
 *             'start'  => 1930,
 *             'finish' => 'current',
 *        ),
 *        // 'text'     => array(
 *        //    'start_label'  => 'Start', // Optionally change start text.
 *        //    'finish_label' => 'Finish', // Optionally change finish text.
 *        //    'separator' => ' to ', // Optionally change separator string/text.
 *        // ),
 *        // 'split_values' => true, // Split values to sep. meta fields.
 *    ) );
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
	$earliest = $field->options( 'earliest' );
	$earliest = $earliest ? absint( $earliest ) : 1900;

	$start_reverse_sort = $field->options( 'start_reverse_sort' );
	$start_reverse_sort = $start_reverse_sort ? true : false;

	$finish_reverse_sort = $field->options( 'finish_reverse_sort' );
	$finish_reverse_sort = $finish_reverse_sort ? true : false;

	$value = wp_parse_args( $value, array(
		'start'  => '',
		'finish' => '',
	) );

	$desc = $field->args( 'description' );
	$field->args['description'] = '';
	$type_object->type = new CMB2_Type_Select( $type_object );

	echo '<em>'. $type_object->_text( 'start_label', 'Starting Year' ) . '</em> ';

	$start_options = jt_cmb2_date_year_range_options( $type_object, $earliest, $value['start'], $start_reverse_sort );
	echo $type_object->select( array(
		'name'    => $type_object->_name( '[start]' ),
		'id'      => $type_object->_id( '_start' ),
		'value'   => $value['start'],
		'class'   => 'cmb2_select cmb2-year-range-start',
		'options' => $start_options,
		'desc'    => '',
	) );

	echo $type_object->_text( 'separator', ' &mdash; ' );

	$end_options = jt_cmb2_date_year_range_options( $type_object, $earliest, $value['finish'], $finish_reverse_sort  );
	echo $type_object->select( array(
		'name'    => $type_object->_name( '[finish]' ),
		'id'      => $type_object->_id( '_finish' ),
		'value'   => $value['finish'],
		'class'   => 'cmb2_select cmb2-year-range-end',
		'options' => $end_options,
		'desc'    => '',
	) );
	echo ' <em>'. $type_object->_text( 'finish_label', 'Final Year' ) . '</em>';

	$field->args['description'] = $desc;

	$type_object->_desc( true, true );

	add_action( is_admin() ? 'admin_footer' : 'wp_footer', 'jt_cmb2_date_year_range_js' );

}
add_filter( 'cmb2_render_date_year_range', 'jt_cmb2_date_year_range', 10, 5 );

function jt_cmb2_date_year_range_js() {
	static $done = false;
	if ( ! $done ) {
		$done = true;
	}
	?>
	<script type="text/javascript">
		jQuery( function( $ ) {
			var $options = {};

			function makeSelected( $newoptions, selected ) {
				var $selected = $newoptions.filter( '[value="'+ selected +'"]' );

				if ( ! $selected.length ) {
					$selected = $newoptions.filter( '[value=""]' );
				}

				// Clear out previous selected
				$newoptions.filter( ':selected' ).prop( 'selected', false );

				$selected.prop( 'selected', true );

				return $newoptions;
			}

			function getNewOptions( $options, start, selected ) {
				if ( start ) {
					$options = $options.filter( function() {
						var val = $( this ).val();
						return'current' === start ? 'current' === val || '' === val : ( val >= start || ! val );
					} );
				}

				return makeSelected( $options.clone(), selected );
			}

			$( document.body ).on( 'change', '.cmb2-year-range-start', function() {
				var $this        = $( this );
				var start        = $this.find( ':selected' ).val();
				var $endPicker   = $this.parent().find( '.cmb2-year-range-end' );
				var id           = $endPicker.attr( 'id' );
				var selectedEnd  = $endPicker.find( ':selected' ).val();

				if ( ! $options[ id ] ) {
					// Store a cached version of the unmodified options.
					$options[ id ] = $endPicker.find( 'option' ).clone();
				}

				$endPicker.html( getNewOptions( $options[ id ], start, selectedEnd ) );
			} );

			// Kick it off.
			$( '.cmb2-year-range-start' ).trigger( 'change' );
		});
	</script>
	<?php
}

function jt_cmb2_date_year_range_options( $type_object, $earliest, $value, $reverse = false ) {
	$options = array();

	$not_set = array(
		'value' => '',
		'label' => __( 'Not Set' ),
	);

	if ( cmb2_utils()->isempty( $value ) ) {
		$not_set['checked'] = 'checked';
	}

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

	if ( $reverse ) {
		$options = array_reverse( $options );
	}

	array_unshift( $options, $not_set );

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
