<?php
/*
 * Button field type
 * Screenshot: https://github.com/CMB2/CMB2/assets/1098900/c8e1aa00-1947-480d-8c30-bd58f5e9dcbc
 */

/**
 * Example field registration:
 *
 * function yourprefix_register_demo_metabox() {
 *
 *    $cmb_demo = new_cmb2_box( array(
 *        'id'            => 'demo_metabox',
 *        'title'         => __( 'Test Metabox', 'yourprefix' ),
 *        'object_types'  => array( 'page', ),
 *    ) );
 *
 *    $cmb_demo->add_field( array(
 *        'type'     => 'button',
 *        'name'     => __( 'Button', 'yourprefix' ),
 *        'desc'     => __( 'Button description (optional)', 'yourprefix' ),
 *        'id'       => '_yourprefix_demo_button',
 *        'attributes' => array(
 *            'value' => 'Click Me',
 *            'onclick' => 'alert(\'You clicked the button!\');',
 *        ),
 *    ) );
 * }
 * add_action( 'cmb2_init', 'yourprefix_register_demo_metabox' );
 */

// render button
add_action( 'cmb2_render_button', 'jt_cmb2__cmb_render_button', 10, 5 );
function jt_cmb2__cmb_render_button( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	echo $field_type_object->input( array(
		'class' => 'button',
		'type' => 'button',
	) );
}
