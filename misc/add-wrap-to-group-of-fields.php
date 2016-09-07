<?php
/**
 * Example of adding an opening/closing wrap around a group of fields.
 * Gif: http://b.ustin.co/12Uba
 */

add_action( 'cmb2_admin_init', 'yourprefix_register_demo_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function yourprefix_register_demo_metabox() {
	$prefix = 'yourprefix_demo_';

	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Test Metabox', 'cmb2' ),
		'object_types'  => array( 'page', ), // Post type
	) );

	// Markup to add a metabox-like toggle box: http://b.ustin.co/12Uba
	$advanced_open = '
	<div class="advanced-toggle advanced-toggle-wrap postbox closed">
	<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Click to toggle</span><span class="toggle-indicator" aria-hidden="true"></span></button>
	<h3 class="hndle ui-sortable-handle"><span>Toggle Advanced Options</span></h3>
	<div class="inside">
	';
	$advanced_close = '</div></div>';

	$cmb->add_field( array(
		'before_row' => $advanced_open,
		'name' => esc_html__( 'Test Text', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'text',
		'type' => 'text',
	) );

	$cmb->add_field( array(
		'name' => esc_html__( 'Test Text Small', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textsmall',
		'type' => 'text_small',
	) );

	$cmb->add_field( array(
		'name' => esc_html__( 'Test Text Medium', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textmedium',
		'type' => 'text_medium',
		'after_row' => $advanced_close,
	) );

	// Optionally do other fields here...

}
