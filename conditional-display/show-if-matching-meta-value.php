<?php
/**
 * Only show contact metabox if status set to external.
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters#examples wiki
 */

add_action( 'cmb2_admin_init', 'cmb2_register_conditional_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function cmb2_register_conditional_metabox() {
	/**
	 * Metabox to save the 'status' where 'Internal' is the default.
	 */
	$cmb = new_cmb2_box( array(
		'id'           => 'wiki_status_metabox',
		'title'        => 'Status Metabox',
		'object_types' => array( 'page', ), // Post type
	) );

	$cmb->add_field( array(
		'name'    => 'Status',
		'id'      => 'wiki_status',
		'type'    => 'select',
		'default' => 'internal',
		'options' => array(
			'internal' => 'Internal',
			'external' => 'External',
		),
	) );

	/**
	 * Metabox to conditionally display if the 'status' is set to 'External'.
	 */
	$cmb = new_cmb2_box( array(
		'id'           => 'wiki_conditonal_metabox',
		'title'        => 'Contact Info',
		'object_types' => array( 'page', ), // Post type
		'show_on_cb' => 'cmb_only_show_for_external', // function should return a bool value
	) );

	$cmb->add_field( array(
		'name'       => 'Email',
		'id'         => 'wiki_email',
		'type'       => 'text_email',
	) );
}

/**
 * Only display a metabox if the page's 'status' is 'external'
 * @param  object $cmb CMB2 object
 * @return bool        True/false whether to show the metabox
 */
function cmb_only_show_for_external( $cmb ) {
	$status = get_post_meta( $cmb->object_id(), 'wiki_status', 1 );

	// Only show if status is 'external'
	return 'external' === $status;
}
