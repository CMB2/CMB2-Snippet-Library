<?php
/**
 * Demonstrates using an alternate data source for storing/retrieving data for a CMB2 box.
 * In this case, we're simply storing/retrieving/removing from an option in the options table,
 * but this can be extended to handle data storage to/from custom tables, Redis, REST APIs, etc.
 */

add_action( 'cmb2_init', 'yourprefix_register_yourprefix_group_alt_data_metabox' );
/**
 * Hook in and add a metabox to demonstrate repeatable grouped fields
 */
function yourprefix_register_yourprefix_group_alt_data_metabox() {

	$default_values = get_option( 'yourprefix_group_alt_data_demo' );
	if ( ! is_array( $default_values ) ) {
		add_option( 'yourprefix_group_alt_data_demo', array(
			// Default group 1.
			array(
				'title' => 'Hey, this is your first entry',
				'description' => 'Go ahead and delete this entry, or update its contents',
			),
			// Default group 2.
			array(
				'title' => 'Hey, this is your 2nd entry',
				'description' => '#2',
			),
		) );
	}

	$cmb_group = new_cmb2_box( array(
		'id'           => 'yourprefix_group_alt_data_metabox',
		'title'        => __( 'Repeating Field Group', 'cmb2' ),
		'object_types' => array( 'post', ),
		'show_on'      => array( 'id' => array( 1000, ) ),
		'show_in_rest' => WP_REST_Server::ALLMETHODS,
	) );

	// $group_field_id is the field id string, so in this case: '_yourprefix_group_demo'
	$group_field_id = $cmb_group->add_field( array(
		'id'          => 'yourprefix_group_alt_data_demo',
		'type'        => 'group',
		'options'     => array(
			'group_title'   => __( 'Entry {#}', 'cmb2' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add Another Entry', 'cmb2' ),
			'remove_button' => __( 'Remove Entry', 'cmb2' ),
			'sortable'      => true, // beta
		),
	) );

	/**
	 * Group fields works the same, except ids only need
	 * to be unique to the group. Prefix is not needed.
	 *
	 * The parent field's id needs to be passed as the second argument.
	 */
	$cmb_group->add_group_field( $group_field_id, array(
		'name' => 'Entry Title',
		'id'   => 'title',
		'type' => 'text',
	) );

	$cmb_group->add_group_field( $group_field_id, array(
		'name'        => 'Description',
		'description' => 'Write a short description for this entry',
		'id'          => 'description',
		'type'        => 'wysiwyg',
		'options'     => array(
			'textarea_rows' => 3,
		),
	) );
};

add_filter( 'cmb2_override_yourprefix_group_alt_data_demo_meta_value', 'yourprefix_group_alt_data_demo_override_meta_value', 10, 4 );
function yourprefix_group_alt_data_demo_override_meta_value( $data, $object_id, $args, $field ) {

	// Here, we're pulling from the options table, but you can query from any data source here.
	// If from a custom table, you can use the $object_id to query against.
	return get_option( 'yourprefix_group_alt_data_demo', array() );
}

add_filter( 'cmb2_override_yourprefix_group_alt_data_demo_meta_save', 'yourprefix_group_alt_data_demo_override_meta_save', 10, 4 );
function yourprefix_group_alt_data_demo_override_meta_save( $override, $args, $field_args, $field ) {

	// Here, we're storing the data to the options table, but you can store to any data source here.
	// If to a custom table, you can use the $args['id'] as the reference id.
	$updated = update_option( 'yourprefix_group_alt_data_demo', $args['value'] );
	return !! $updated;
}

add_filter( 'cmb2_override_yourprefix_group_alt_data_demo_meta_remove', 'yourprefix_group_alt_data_demo_override_meta_remove', 10, 4 );
function yourprefix_group_alt_data_demo_override_meta_remove( $override, $args, $field_args, $field ) {

	// Here, we're removing from the options table, but you can query to remove from any data source here.
	// If from a custom table, you can use the $args['id'] to query against.
	// (If we do "delete_option", then our default value will be re-applied, which isn't desired.)
	$updated = update_option( 'yourprefix_group_alt_data_demo', array() );
	return !! $updated;
}
