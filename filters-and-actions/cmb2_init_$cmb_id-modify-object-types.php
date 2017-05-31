<?php
/**
 * This snippet demonstrates modifying (during its "cmb2_init_{$cmb_id}" hook) the object-types
 * registered to a CMB2 box.
 *
 * The "cmb2_init_{$cmb_id}" hook occurs during the initiation of the CMB2 object.
 *
 * The dynamic portion of the hook name, `$cmb_id`, is the registered cmb2 box id,
 * so in our example case: "yourprefix_demo_metabox".
 */
function yourprefix_demo_metabox_modify_object_types( $cmb ) {

	$types = $cmb->box_types();

	$types[] = 'books'; // Your custom post type slug.

	// Bam.
	$cmb->set_prop( 'object_types', $types );
}
add_action( 'cmb2_init_yourprefix_demo_metabox', 'yourprefix_demo_metabox_modify_object_types' );
