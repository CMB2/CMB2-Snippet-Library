<?php
/**
 * CMB2 Multicheck by Post Type
 *
 * @package CMB2 Default Tags field/metabox
 * @author Daniele Mte90 Scasciafratte
 */

add_action( 'cmb2_render_multicheck_posttype', 'ds_cmb_render_multicheck_posttype', 10, 5 );

function ds_cmb_render_multicheck_posttype( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	if ( version_compare( CMB2_VERSION, '2.2.2', '>=' ) ) {
		$field_type_object->type = new CMB2_Type_Radio( $field_type_object );
	}

	$cpts = get_post_types();
	// To disable the avalaibility of post types
	unset( $cpts[ 'nav_menu_item' ] );
	unset( $cpts[ 'revision' ] );
	$cpts = apply_filters( 'multicheck_posttype_' . $field->args[ '_id' ], $cpts );
	$options = '';
	$i = 1;
	$values = (array) $escaped_value;

	if ( $cpts ) {
		foreach ( $cpts as $cpt ) {
			$args = array(
			    'value' => $cpt,
			    'label' => $cpt,
			    'type' => 'checkbox',
			    'name' => $field->args['_name'] . '[]',
			);

			if ( in_array( $cpt, $values ) ) {
				$args[ 'checked' ] = 'checked';
			}
			$options .= $field_type_object->list_input( $args, $i );
			$i++;
		}
	}

	$classes = false === $field->args( 'select_all_button' ) ? 'cmb2-checkbox-list no-select-all cmb2-list' : 'cmb2-checkbox-list cmb2-list';
	echo $field_type_object->radio( array( 'class' => $classes, 'options' => $options ), 'multicheck_posttype' );
}

