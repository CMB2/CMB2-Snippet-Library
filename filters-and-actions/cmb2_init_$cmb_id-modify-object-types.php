<?php
/**
 * This file demonstrates modifying (during its "cmb2_init_{$cmb_id}" hook) the object-types registered to a CMB2 box.
 */

function yourprefix_demo_metabox_modify_object_types( $cmb ) {

	$types = $cmb->box_types();

	$types[] = 'jt-books'; // Your custom post type slug.

	// Bam.
	$cmb->set_prop( 'object_types', $types );
}
add_action( 'cmb2_init__yourprefix_demo_metabox', 'yourprefix_demo_metabox_modify_object_types' );
