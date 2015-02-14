<?php
/**
 * This file demonstrates removing fields from a registered CMB2 metaboxes during its "cmb2_init_{$cmb_id}" hook
 */

function yourprefix_remove_field_from_demo_metabox( $cmb ) {
	$cmb->remove_field( '_yourprefix_demo_textsmall' );
}
add_action( 'cmb2_init__yourprefix_demo_metabox', 'yourprefix_remove_field_from_demo_metabox' );
