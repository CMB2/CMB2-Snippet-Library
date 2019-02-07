<?php
/*
 * Plugin Name: CMB2 Custom Field Type - Form Fields
 * Description: Makes available a 'formfield' CMB2 Custom Field Type. Based on https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types#example-4-multiple-inputs-one-field-lets-create-an-formfield-field
 * Author: jtsternberg
 * Author URI: http://dsgnwrks.pro
 * Version: 0.1.0
 *
 * Example use with translation of strings:
 *
 * $cmb_demo->add_field( array(
 *     'name'       => 'Campos',
 *     'desc'       => 'Adiciona campos ao formulário',
 *     'id'         => '_form_fields',
 *     'type'       => 'formfield',
 *     'repeatable' => true,
 *     'text'       => array(
 *         'add_row_text'                       => 'Adicionar Campo',
 *         'formfield_field_id_label'           => 'ID do Campo',
 *         'formfield_field_label_label'        => 'Título do campo',
 *         'formfield_field_type_label'         => 'Tipo do campo',
 *         'formfield_field_size_label'         => 'Tamanho do campo',
 *         'formfield_text_field_option_label'  => 'Texto',
 *         'formfield_email_field_option_label' => 'Email',
 *         'formfield_money_field_option_label' => 'Dinheiro',
 *         'formfield_date_field_option_label'  => 'Data',
 *     ),
 * ) );
 *
 */


/**
 * Render 'formfield' custom field type
 *
 * @since 0.1.0
 *
 * @param array  $field       The passed in `CMB2_Field` object
 * @param mixed  $value       The value of this field escaped.
 *                            It defaults to `sanitize_text_field`.
 *                            If you need the unescaped value, you can access it
 *                            via `$field->value()`
 * @param int    $object_id   The ID of the current object
 * @param string $object_type The type of object you are working with.
 *                            Most commonly, `post` (this applies to all post-types),
 *                            but could also be `comment`, `user` or `options-page`.
 * @param object $field_type  The `CMB2_Types` object
 */
function jt_cmb2_render_formfield_field_callback( $field, $value, $object_id, $object_type, $field_type ) {

	// make sure we specify each part of the value we need.
	$value = wp_parse_args( $value, array(
		'id'    => '',
		'label' => '',
		'type'  => 'text',
		'size'  => '',
	) );

	$type_options = array(
		'text'  => $field_type->_text( 'formfield_text_field_option_label', 'Text' ),
		'email' => $field_type->_text( 'formfield_email_field_option_label', 'Email' ),
		'money' => $field_type->_text( 'formfield_money_field_option_label', 'Money' ),
		'date'  => $field_type->_text( 'formfield_date_field_option_label', 'Date' ),
	);

	$types = '';
	foreach ( $type_options as $type => $label ) {
		$selected = selected( $value['type'], $type, false );
		$label    = esc_html( $label );
		$types    .= "<option value=\"{$type}\" {$selected}>{$label}</option>";
	}

	?>
	<table>
		<tr>
			<td>
				<label for="<?php echo $field_type->_id( '_formfield_1' ); ?>"><?php echo esc_html( $field_type->_text( 'formfield_field_id_label', 'Field ID' ) ); ?></label>
				<?php
				echo $field_type->input( array(
					'name'  => $field_type->_name( '[id]' ),
					'id'    => $field_type->_id( '_id' ),
					'value' => $value['id'],
					'desc'  => ''
				) )
				?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="<?php echo $field_type->_id( '_formfield_1' ); ?>"><?php echo esc_html( $field_type->_text( 'formfield_field_label_label', 'Field label' ) ); ?></label>
				<?php
				echo $field_type->input( array(
					'name'  => $field_type->_name( '[label]' ),
					'id'    => $field_type->_id( '_label' ),
					'value' => $value['label'],
					'desc'  => ''
				) )
				?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="<?php echo $field_type->_id( '_formfield_1' ); ?>"><?php echo esc_html( $field_type->_text( 'formfield_field_type_label', 'Field type' ) ); ?></label>
				<?php
				echo $field_type->select( array(
					'name'    => $field_type->_name( '[type]' ),
					'id'      => $field_type->_id( '_type' ),
					'options' => $types,
					'desc' => ''
				) )
				?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="<?php echo $field_type->_id( '_formfield_1' ); ?>"><?php echo esc_html( $field_type->_text( 'formfield_field_size_label', 'Field Size' ) ); ?></label>
				<?php
				echo $field_type->input( array(
					'name'  => $field_type->_name( '[size]' ),
					'id'    => $field_type->_id( '_size' ),
					'value' => $value['size'],
					'desc'  => ''
				) )
				?>
			</td>
		</tr>
	</table>
	<?php if ( $field_type->_desc() ) : ?>
		<p class="clear">
			<?php echo $field_type->_desc();?>
		</p>
	<?php endif;
}
add_filter( 'cmb2_render_formfield', 'jt_cmb2_render_formfield_field_callback', 10, 5 );

/**
 * The following snippets are required for allowing the formfield field
 * to work as a repeatable field, or in a repeatable group
 */
function jt_cmb2_sanitize_formfield_field( $check, $meta_value, $object_id, $field_args ) {

	// Nothing needed if not array value or not a repeatable field.
	if ( ! is_array( $meta_value ) || empty( $field_args['repeatable'] ) ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$val['type'] = isset( $val['type'] ) ? $val['type'] : 'text';
		if ( 'text' === $val['type'] ) {
			unset( $val['type'] );
			$val = array_filter( $val );
			if ( empty( $val ) ) {
				unset( $meta_value[ $key ] );
				continue;
			} else {
				$val['type'] = 'text';
			}
		}
		$meta_value[ $key ] = array_map( 'sanitize_text_field', $val );
	}

	return $meta_value;
}
add_filter( 'cmb2_sanitize_formfield', 'jt_cmb2_sanitize_formfield_field', 10, 4 );

function jt_cmb2_types_esc_formfield_field( $check, $meta_value, $field_args ) {

	// Nothing needed if not array value or not a repeatable field.
	if ( ! is_array( $meta_value ) || empty( $field_args['repeatable'] ) ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$meta_value[ $key ] = array_map( 'esc_attr', $val );
	}

	return $meta_value;
}
add_filter( 'cmb2_types_esc_formfield', 'jt_cmb2_types_esc_formfield_field', 10, 3 );