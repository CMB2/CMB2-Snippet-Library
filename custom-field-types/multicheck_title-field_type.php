<?php
//By Daniele Mte90 Scasciafratte
//Is multicheck but with section title

/* 
//How to use 
$array[ 'id_of_the_key' ] = __( 'ID of the key' );
$fields[ 'Title of the section' ] = $array;
$cmb->add_field( array(
	'name' => __( 'Fields Extra', $this->plugin_slug ),
	'id' => 'extra_fields',
	'type' => 'multicheck_title',
	'data' => $fields
) );
*/

// render Title multicheck
add_action( 'cmb2_render_multicheck_title', 'cmb_render_multicheck_title', 10, 5 );

function cmb_render_multicheck_title( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	$data_field = $field->args[ 'data' ];
	$values = ( array ) $escaped_value;
	$i = 0;

	if ( $data_field ) {
		foreach ( $data_field as $title => $extra_fields ) {
			$options = '';
			foreach ( $extra_fields as $extra_field => $value ) {
				$args = array(
				    'value' => $extra_field,
				    'label' => $value,
				    'type' => 'checkbox',
				    'name' => $field->args[ '_name' ] . '[]',
				);

				if ( in_array( $extra_field, $values) ) {
					$args[ 'checked' ] = 'checked';
				}
				$options .= $field_type_object->list_input( $args, $i );
				$i++;
			}
			echo '<h2>'.$title.'</h2>';
			$classes = false === $field->args( 'select_all_button' ) ? 'cmb2-checkbox-list no-select-all cmb2-list' : 'cmb2-checkbox-list cmb2-list';
			echo $field_type_object->radio( array( 'class' => $classes, 'options' => $options ), 'title_multicheck' );
		}
	} else {
		echo __( 'Nothing' );
	}
} 
