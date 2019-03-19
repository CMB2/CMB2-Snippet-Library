<?php
/**
 * This file demonstrates using the following hooks:
 * cmb2_before_form and cmb2_before_{$object_type}_form_{$cmb_id}
 * Docs: https://github.com/CMB2/CMB2/blob/a7b93886e56a9c9f6e78ea0efbe1b01bcbb1a248/includes/CMB2.php#L245-L270
 * cmb2_after_form and cmb2_after_{$object_type}_form_{$cmb_id}
 * Docs: https://github.com/CMB2/CMB2/blob/a7b93886e56a9c9f6e78ea0efbe1b01bcbb1a248/includes/CMB2.php#L361-L385
 */

function yourprefix_add_html_before_cmb2_output( $cmb_id, $object_id, $object_type, $cmb ) {
	// Only output above the _yourprefix_demo_metabox metabox.
	if ( '_yourprefix_demo_metabox' !== $cmb_id ) {
		return;
	}

	echo '<div class="my-custom-wrapper">';
	echo '<h4>Welcome to my metabox!</h4>';

	add_action( 'cmb2_after_form', 'yourprefix_add_html_after_cmb2_output', 10, 4 );
}
add_action( 'cmb2_before_form', 'yourprefix_add_html_before_cmb2_output', 10, 4 );

function yourprefix_add_html_after_cmb2_output( $cmb_id, $object_id, $object_type, $cmb ) {
	echo '</div><!-- .my-custom-wrapper-->';
}


// This can be done a bit more simply this way:

function add_html_before_yourprefix_demo_metabox_output( $cmb_id, $object_id, $object_type, $cmb ) {
	echo '<div class="my-custom-wrapper">';
	echo '<h4>Welcome to my metabox!</h4>';

	add_action( "cmb2_after_{$object_type}_form_{$cmb_id}", 'add_html_after_yourprefix_demo_metabox_output', 10, 4 );
}
// Only output above the _yourprefix_demo_metabox metabox.
add_action( 'cmb2_before_post_form__yourprefix_demo_metabox', 'add_html_before_yourprefix_demo_metabox_output', 10, 4 );

function add_html_after_yourprefix_demo_metabox_output( $cmb_id, $object_id, $object_type, $cmb ) {
	echo '</div><!-- .my-custom-wrapper-->';
}
