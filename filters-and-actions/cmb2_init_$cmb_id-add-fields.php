<?php
/**
 * This file demonstrates adding new fields to a registered CMB2 metaboxes during its "cmb2_init_{$cmb_id}" hook
 */

function yourprefix_add_new_field_to_top_of_demo_metabox( $cmb ) {

	$cmb->add_field(
		array(
			'name' => __( 'New at the top', 'your_textdomain' ),
			'desc' => __( 'Using the "cmb2_init_{$cmb_id}" hook', 'your_textdomain' ),
			'id'   => '_new_at_the_top',
			'type' => 'text',
		),
		'',
		1
	);

}
add_action( 'cmb2_init__yourprefix_demo_metabox', 'yourprefix_add_new_field_to_top_of_demo_metabox' );
