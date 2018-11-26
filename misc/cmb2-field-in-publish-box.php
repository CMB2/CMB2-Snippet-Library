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
 * Register the CMB2 metabox.
 */
function yourprefix_cmb2_fields() {
	$prefix = '_yourprefix_';

	$cmb = new_cmb2_box( [
		'id'           => $prefix . 'publish_box',
		'object_types' => [ 'post' ],               // Any public post type.
		'show_names'   => false,                    // Disables the display for the CMB2 label.
	] );

	$cmb->add_field( [
		'before' => '<label for="' . $prefix . 'field">' . __( 'Your field name', 'cmb2' ) . '</label>', // Output the label w/o the default CMB2 styles.
		'id'   => $prefix . 'field',
		'desc' => __( 'Field description (optional)', 'cmb2' ),
		'type' => 'text', // Any valid CMB2 field type.
		'attributes' => [
			'style' => 'max-width: 80%;', // Cleans up the default CMB2 styles a bit. Move to stylesheet?
		]
	] );
}

add_action( 'post_submitbox_misc_actions', 'yourprefix_filter_publish_box' );

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

add_action( 'cmb2_admin_init', 'yourprefix_cmb2_fields' );
