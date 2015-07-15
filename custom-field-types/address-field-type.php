<?php
/*
 * Plugin Name: CMB2 Custom Field Type - Address
 * Description: Makes available an 'address' CMB2 Custom Field Type. Based on https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types#example-4-multiple-inputs-one-field-lets-create-an-address-field
 * Author: jtsternberg
 * Author URI: http://dsgnwrks.pro
 * Version: 0.1.0
 */

/**
 * Template tag for displaying an address from the CMB2 address field type (on the front-end)
 *
 * @since  0.1.0
 *
 * @param  string  $metakey The 'id' of the 'address' field (the metakey for get_post_meta)
 * @param  integer $post_id (optional) post ID. If using in the loop, it is not necessary
 */
function jt_cmb2_address_field( $metakey, $post_id = 0 ) {
	echo jt_cmb2_get_address_field( $metakey, $post_id );
}

/**
 * Template tag for returning an address from the CMB2 address field type (on the front-end)
 *
 * @since  0.1.0
 *
 * @param  string  $metakey The 'id' of the 'address' field (the metakey for get_post_meta)
 * @param  integer $post_id (optional) post ID. If using in the loop, it is not necessary
 */
function jt_cmb2_get_address_field( $metakey, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$address = get_post_meta( $post_id, $metakey, 1 );

	// Set default values for each address key
	$address = wp_parse_args( $address, array(
		'address-1' => '',
		'address-2' => '',
		'city'      => '',
		'state'     => '',
		'zip'       => '',
	) );

	$output = '<div class="cmb2-address">';
	$output .= '<p><strongAddress:</strong> ' . esc_html( $address['address-1'] ) . '</p>';
	if ( $address['address-2'] ) {
		$output .= '<p>' . esc_html( $address['address-2'] ) . '</p>';
	}
	$output .= '<p><strong>City:</strong> ' . esc_html( $address['city'] ) . '</p>';
	$output .= '<p><strong>State:</strong> ' . esc_html( $address['state'] ) . '</p>';
	$output .= '<p><strong>Zip:</strong> ' . esc_html( $address['zip'] ) . '</p>';
	$output = '</div><!-- .cmb2-address -->';

	return apply_filters( 'jt_cmb2_get_address_field', $output );
}

/**
 * Render 'address' custom field type
 *
 * @since 0.1.0
 *
 * @param array  $field              The passed in `CMB2_Field` object
 * @param mixed  $value              The value of this field escaped.
 *                                   It defaults to `sanitize_text_field`.
 *                                   If you need the unescaped value, you can access it
 *                                   via `$field->value()`
 * @param int    $object_id          The ID of the current object
 * @param string $object_type        The type of object you are working with.
 *                                   Most commonly, `post` (this applies to all post-types),
 *                                   but could also be `comment`, `user` or `options-page`.
 * @param object $field_type_object  The `CMB2_Types` object
 */
function jt_cmb2_render_address_field_callback( $field, $value, $object_id, $object_type, $field_type_object ) {

	// can override via the field options param
	$select_text = esc_html( $field_type_object->_text( 'address_select_state_text', 'Select a State' ) );

	$state_list = array( ''=>$select_text,'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District Of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming' );

	// make sure we specify each part of the value we need.
	$value = wp_parse_args( $value, array(
		'address-1' => '',
		'address-2' => '',
		'city'      => '',
		'state'     => '',
		'zip'       => '',
	) );

	$state_options = '';
	foreach ( $state_list as $abrev => $state ) {
		$state_options .= '<option value="'. $abrev .'" '. selected( $value['state'], $abrev, false ) .'>'. $state .'</option>';
	}

	?>
	<div><p><label for="<?php echo $field_type_object->_id( '_address_1' ); ?>"><?php echo esc_html( $field_type_object->_text( 'address_address_1_text', 'Address 1' ) ); ?></label></p>
		<?php echo $field_type_object->input( array(
			'name'  => $field_type_object->_name( '[address-1]' ),
			'id'    => $field_type_object->_id( '_address_1' ),
			'value' => $value['address-1'],
		) ); ?>
	</div>
	<div><p><label for="<?php echo $field_type_object->_id( '_address_2' ); ?>'"><?php echo esc_html( $field_type_object->_text( 'address_address_2_text', 'Address 2' ) ); ?></label></p>
		<?php echo $field_type_object->input( array(
			'name'  => $field_type_object->_name( '[address-2]' ),
			'id'    => $field_type_object->_id( '_address_2' ),
			'value' => $value['address-2'],
		) ); ?>
	</div>
	<div class="alignleft"><p><label for="<?php echo $field_type_object->_id( '_city' ); ?>'"><?php echo esc_html( $field_type_object->_text( 'address_city_text', 'City' ) ); ?></label></p>
		<?php echo $field_type_object->input( array(
			'class' => 'cmb_text_small',
			'name'  => $field_type_object->_name( '[city]' ),
			'id'    => $field_type_object->_id( '_city' ),
			'value' => $value['city'],
		) ); ?>
	</div>
	<div class="alignleft"><p><label for="<?php echo $field_type_object->_id( '_state' ); ?>'"><?php echo esc_html( $field_type_object->_text( 'address_state_text', 'State' ) ); ?></label></p>
		<?php echo $field_type_object->select( array(
			'name'    => $field_type_object->_name( '[state]' ),
			'id'      => $field_type_object->_id( '_state' ),
			'options' => $state_options,
		) ); ?>
	</div>
	<div class="alignleft"><p><label for="<?php echo $field_type_object->_id( '_zip' ); ?>'"><?php echo esc_html( $field_type_object->_text( 'address_zip_text', 'Zip' ) ); ?></label></p>
		<?php echo $field_type_object->input( array(
			'class' => 'cmb_text_small',
			'name'  => $field_type_object->_name( '[zip]' ),
			'id'    => $field_type_object->_id( '_zip' ),
			'value' => $value['zip'],
			'type'  => 'number',
		) ); ?>
	</div>
	<?php
	echo $field_type_object->_desc( true );

}
add_filter( 'cmb2_render_address', 'jt_cmb2_render_address_field_callback', 10, 5 );

/**
 * Optionally save the Address values into separate fields
 */
function cmb2_split_address_values( $override_value, $value, $object_id, $field_args ) {
	if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
		// Don't do the override
		return $override_value;
	}

	$address_keys = array( 'address-1', 'address-2', 'city', 'state', 'zip' );

	foreach ( $address_keys as $key ) {
		if ( ! empty( $value[ $key ] ) ) {
			update_post_meta( $object_id, $field_args['id'] . 'addr_'. $key, $value[ $key ] );
		}
	}

	// Tell CMB2 we already did the update
	return true;
}
add_filter( 'cmb2_sanitize_address', 'cmb2_split_address_values', 12, 4 );

/**
 * The following snippets are required for allowing the address field
 * to work as a repeatable field, or in a repeatable group
 */

function cmb2_sanitize_address_field( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

	// if not repeatable, bail out.
	if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$meta_value[ $key ] = array_map( 'sanitize_text_field', $val );
	}

	return $meta_value;
}
add_filter( 'cmb2_sanitize_address', 'cmb2_sanitize_address_field', 10, 5 );

function cmb2_types_esc_address_field( $check, $meta_value, $field_args, $field_object ) {
	// if not repeatable, bail out.
	if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$meta_value[ $key ] = array_map( 'esc_attr', $val );
	}

	return $meta_value;
}
add_filter( 'cmb2_types_esc_address', 'cmb2_types_esc_address_field', 10, 4 );
