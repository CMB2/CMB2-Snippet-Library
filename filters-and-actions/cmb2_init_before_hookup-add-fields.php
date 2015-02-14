<?php
/**
 * This file demonstrates adding new fields to registered CMB2 metaboxes/objects
 */

function yourprefix_add_new_field_in_3rd_position() {

	$prefix = '_yourprefix_demo_';

	// Retrieve a CMB2 instance
	$cmb = cmb2_get_metabox( '_yourprefix_demo_metabox' );

	// This should return false because we don't have a '_yourprefix_demo_text2' field
	$field_id = $cmb->update_field_property( '_yourprefix_demo_text2', 'type', 'text' );

	/**
	 * Since '_yourprefix_demo_text2' doesn't exist, Let's create it.
	 * Always need to compare this value strictly to false, as a field_id COULD be 0 or ''
	 */
	if ( false === $field_id ) {
		$cmb->add_field(
			// Normal field setup
			array(
				'name'       => __( 'Test Text 2', 'your_textdomain' ),
				'desc'       => __( 'Test Text 2 description', 'your_textdomain' ),
				'id'         => '_yourprefix_demo_text2',
				'type'       => 'text',
				'attributes' => array( 'placeholder' => __( "I'm some placeholder text", 'your_textdomain' ) ),
			),
			'', // This field should not be appended to any group field
			3 // Insert this field in the third position
		);

	}

}
add_action( 'cmb2_init_before_hookup', 'yourprefix_add_new_field_in_3rd_position' );

function yourprefix_add_new_field_to_group() {
	// Try to get a metabox w/ the id of '_yourprefix_group_metabox'
	if ( $cmb_group_demo = cmb2_get_metabox( '_yourprefix_group_metabox' ) ) {

		$cmb_group_demo->add_field(
			array(
				'name'       => __( 'Test Text 2', 'your_textdomain' ),
				'desc'       => __( 'field description (optional)', 'your_textdomain' ),
				'id'         => 'text2',
				'type'       => 'text',
				'attributes' => array( 'placeholder' => __( "I'm some placeholder text", 'your_textdomain' ) ),
			),
			'_yourprefix_group_demo', // Add this to the _yourprefix_group_demo group field
			2 // And insert it into the 2nd position
		);

	}
 }
add_action( 'cmb2_init_before_hookup', 'yourprefix_add_new_field_to_group' );
