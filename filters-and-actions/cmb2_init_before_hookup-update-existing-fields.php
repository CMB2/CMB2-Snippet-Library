<?php
/**
 * This file demonstrates modifying existing registered CMB2 metabox fields
 */

function yourprefix_update_fields_properties() {
	// Retrieve a CMB2 instance
	$cmb = cmb2_get_metabox( '_yourprefix_demo_metabox' );

	/**
	 * Update a property on the '_yourprefix_demo_text' field.
	 * In this case, we'll remove the show_on_cb conditional callback
	 * (to instead always display the field)
	 *
	 * If field exists, and property updated, it will return the field id
	 */
	$field_id = $cmb->update_field_property( '_yourprefix_demo_text', 'show_on_cb', false );

	/**
	 * Always need to compare this value strictly to false, as a field_id COULD be 0 or ''
	 */
	if ( false !== $field_id ) {

		/**
		 * Because we don't want to 'stomp' a field's 'attributes' property
		 * (It may already have some attributes), we're going to get
		 * the field's attributes property and append to it.
		 */

		// Get all fields for this metabox
		$fields = $cmb->prop( 'fields' );

		// Get the attributes array if it exists, or else create it
		$attributes = isset( $fields['_yourprefix_demo_text']['attributes'] )
			? $fields['_yourprefix_demo_text']['attributes']
			: array();

		// Add placeholder text
		$attributes['placeholder'] = __( "I'm some placeholder text", 'your_textdomain' );

		// Update the field's 'attributes' property
		$cmb->update_field_property( '_yourprefix_demo_text', 'attributes', $attributes );

	}

}
add_action( 'cmb2_init_before_hookup', 'yourprefix_update_fields_properties' );
