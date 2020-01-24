<?php
/**
 * This file demonstrates adding replacing a registered field in the CMB2 metaboxe
 * with an id of `_yourprefix_demo_metaboxx`,  during its "cmb2_init_hookup_{$cmb_id}" hook.
 */

function yourprefix_replace_field_in_demo_metaboxx( $cmb ) {

	// Should we replace the field with our new one?
	$some_condition = true;

	if ( $some_condition ) {
		$cmb->remove_field( '_yourprefix_demo_textsmall' );

		$cmb->add_field(
			array(
				'name' => __( 'REPLACED Text Small Field', 'cmb2' ),
				'desc' => __( 'Using the "cmb2_init_{$cmb_id}" hook', 'your_textdomain' ),
				'id'   => '_yourprefix_demo_textsmall',
				'type' => 'text_money',
			),
			17 /* This needs to be the nth position of the original field */
		);
	}

}
add_action( 'cmb2_init_hookup__yourprefix_demo_metaboxx', 'yourprefix_replace_field_in_demo_metaboxx', 999 );
