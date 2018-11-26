<?php
/**
 * Add a CMB2 field to the WordPress publish box.
 *
 * @author Chris Reynolds <chris@hmn.md>
 * @link   https://joebuckle.me/quickie/wordpress-add-options-to-post-admin-publish-meta-box/
 * @link   https://github.com/CMB2/CMB2-Snippet-Library/blob/master/misc/outputting-forms-outside-metaboxes.php
 * @link   https://codex.wordpress.org/Plugin_API/Action_Reference/post_submitbox_misc_actions
 */
 
 /**
  * Kick off all the things.
  */
 function yourprefix_bootstrap() {
	add_action( 'post_submitbox_misc_actions', 'yourprefix_filter_publish_box' );
	add_action( 'save_post', 'yourprefix_save_publish_conditions', 10, 3 );
	add_action( 'cmb2_admin_init', 'yourprefix_cmb2_fields' );
 }
 
/**
 * Register the CMB2 metabox.
 */
function yourprefix_cmb2_fields() {
	$prefix = '_yourprefix_';
	
	$cmb = new_cmb2_box( [
		'id'           => $prefix . 'publish_box',
		'object_types' => [ 'post' ],               // Any public post type.
		'show_names'   => true,                     // Displays the CMB2 label.
	] );
	
	$cmb->add_field( [
		'name' => __( 'Your field name', 'cmb2' ),
		'id'   => $prefix . 'field',
		'desc' => __( 'Field description (optional)', 'cmb2' ),
		'type' => 'text', // Any valid CMB2 field type.
	] );
}
 
/**
 * Display the CMB2 form in the WordPress publish metabox.
 *
 * @param object $post The WP_Post object.
 */
function yourprefix_filter_publish_box( $post ) {
  	$cmb = cmb2_get_metabox( '_yourprefix_publish_box' ); // Must match the box ID.
	  
	if ( in_array( $post->post_type, $cmb->prop( 'object_types' ), true ) ) {
		$cmb->show_form();
	}
}

/**
 * Save the CMB2 publish condition metadata.
 *
 * @param  int    $post_id The WP_Post ID.
 * @param  object $post    The WP_Post object.
 * @param  bool   $update  Whether the post is a revision.
 * @return void
 */
function yourprefix_save_publish_conditions( $post_id, $post, $update ) {

	// Check if the current post type is in the array of allowed object types for the CMB2 field.
	if ( ! in_array( $post->post_type, [ 'post' ] true ) ) {
		return;
	}

	if ( wp_is_post_revision( $post ) ) {
		return;
	}

	$prefix = '_yourprefix_';
	$cmb    = cmb2_get_metabox( $prefix . 'publish_box' );

	// Save the post meta.
	if (
		isset( $_POST[ $prefix . 'state' ] ) &
		isset( $_POST[ $cmb->nonce() ] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $cmb->nonce() ] ) ), $cmb->nonce() )
	) {
		update_post_meta( $post_id, $prefix . 'state', sanitize_text_field( wp_unslash( $_POST[ $prefix . 'field' ] ) ) );
	}
}
 
 // Engage!
 yourprefix_bootstrap();
 
