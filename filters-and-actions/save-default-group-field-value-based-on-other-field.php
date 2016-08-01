<?php

/**
 * The following snippet is untested, but provides a proof of concept to how you can save a default value for a group field based on the value of another field.
 */

// Setup a group field... the group field is proceeded by a radio field.
function yourprefix_register_repeatable_group_field_metabox() {
	/**
	 * Repeatable Field Groups
	 */
	$cmb = new_cmb2_box( array(
		'id'           => 'yourprefix_group_metabox',
		'title'        => __( 'Repeating Field Group', 'cmb2' ),
		'object_types' => array( 'page', ),
	) );

	$cmb->add_field( $group_field_id, array(
		'name'             => __( 'Radio', 'cmb2' ),
		'id'               => 'yourprefix_group_radio',
		'type'             => 'radio',
		'options' => array(
			'standard' => __( 'Option One', 'cmb2' ),
			'custom'   => __( 'Option Two', 'cmb2' ),
			'none'     => __( 'Option Three', 'cmb2' ),
		),
	) );

	$group_field_id = $cmb->add_field( array(
		'id'          => 'yourprefix_group_demo',
		'type'        => 'group',
		'description' => __( 'Generates reusable form entries', 'cmb2' ),
		'options'     => array(
			'group_title'    => __( 'Entry {#}', 'cmb2' ),
			'add_button'     => __( 'Add Another Entry', 'cmb2' ),
			'remove_button'  => __( 'Remove Entry', 'cmb2' ),
			'sortable'       => true,
		),
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Entry Title',
		'id'   => 'title',
		'type' => 'text',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'        => 'Description',
		'description' => 'Write a short description for this entry',
		'id'          => 'description',
		'type'        => 'textarea_small',
	) );

}
add_action( 'cmb2_init', 'yourprefix_register_repeatable_group_field_metabox' );


// If the radio field is set to 'standard', then update the group field value to have one group filled-in.
function hook_in_and_add_default_group_value( $post_id, $updated, $cmb ) {
	// If 'my_meta_key' was updated, then proceed w/ my stuff.
	if ( in_array( 'yourprefix_group_radio', $updated ) ) {
		if ( 'standard' === get_post_meta( $post_id, 'yourprefix_group_radio', 1 ) ) {
			// do stuff
			update_post_meta( $post_id, 'yourprefix_group_demo', array(
				array(
					'title'       => 'Title of group 1',
					'description' => 'Description of group 1',
				)
			) );
		}
	}
}
add_action( 'cmb2_save_post_fields_yourprefix_group_metabox', 'hook_in_and_add_default_group_value', 10, 3 );
