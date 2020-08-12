<?php
/**
 * If you want to replace the default WordPress excerpt field with CMB2 field,
 * it can be done with the following snippets.
 */

function cmb2_register_excerpt_replacement_box() {

	$cmb = new_cmb2_box( array(
		'id'           => 'cmb2_excerpt',
		'title'        => 'Excerpt',
		'object_types' => array( 'post', ), // Post type
		// 'context'      => 'side',
	) );

	$cmb->add_field( array(
		/*
		 * As long as the 'id' matches the name field of the regular WP field,
		 * WP will handle the saving for you.
		 */
		'id'        => 'excerpt',
		'name'      => 'Excerpt',
		'desc'      => 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="https://codex.wordpress.org/Excerpt" target="_blank">Learn more about manual excerpts.</a>',
		'type'      => 'textarea',
		'escape_cb' => false,
	) );

}
add_action( 'cmb2_admin_init', 'cmb2_register_excerpt_replacement_box' );


/**
 * Remove the default WordPress excerpt field.
 */
function cmb2_admin_hide_excerpt_field() {
	add_action( 'add_meta_boxes', '_cmb2_admin_hide_excerpt_field' );
}
add_filter( 'admin_init', 'cmb2_admin_hide_excerpt_field' );

function _cmb2_admin_hide_excerpt_field() {
	$screen = get_current_screen();

	if ( isset( $screen->post_type ) && 'post' === $screen->post_type ) {
		remove_meta_box( 'postexcerpt', null, 'normal' );
	}
}


/**
 * Override the WordPress Excerpt field
 */
function cmb2_override_excerpt_display( $data, $post_id ) {
	return get_post_field( 'post_excerpt', $post_id );
}
add_filter( 'cmb2_override_excerpt_meta_value', 'cmb2_override_excerpt_display', 10, 2 );

/*
 * WP will handle the saving for us, so don't save to meta.
 */
add_filter( 'cmb2_override_excerpt_meta_save', '__return_true' );
