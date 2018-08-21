<?php
/**
 * This file demonstrates adding new fields dynamically, based on the existing post meta.
 * @link https://geek.hellyer.kiwi/2018/08/11/dynamically-controlling-cmb2-metaboxes/
 */

add_action( 'cmb2_admin_init', 'register_dynamic_fields_box' );
function register_dynamic_fields_box() {
	$cmb = new_cmb2_box( array(
		'id'           => 'dynamic_fields_box',
		'title'        => 'Some test metaboxes',
		'object_types' => array( 'page', 'post' ),
	) );

	$cmb->add_field( array(
		'name' => 'Set number of next item',
		'id'   => 'number_of_next_item',
		'type' => 'text',
		'default' => '1',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'sanitization_cb' => 'absint',
		'escape_cb'       => 'absint',
	) );

	// Add dynamic fields during normal view.
	add_action( 'cmb2_init_hookup_dynamic_fields_box', 'add_fields_dynamically_to_box' );

	// Add dynamic fields during save process.
	add_action( 'cmb2_post_process_fields_dynamic_fields_box', 'add_fields_dynamically_to_box' );
}

function add_fields_dynamically_to_box( $cmb ) {
	if ( $cmb->object_id() ) {
		$position = 2;

		// Loop through however many items are selected in previous field
		$number_of_items = get_post_meta( $cmb->object_id(), 'number_of_next_item', true );
		$number = 1;
		while ( $number <= $number_of_items ) {

			$cmb->add_field( array(
				'name' =>  'Item #' . $number,
				'desc'   => 'item_' . $number,
				'id'   => 'item_' . $number,
				'type' => 'text',
			), $position++ );

			$number++;
		}
	}
}