<?php
/**
 * @link http://webdevstudios.com/2015/03/30/using-cmb2-to-create-a-post-submission-form Original tutorial
 */


/**
 * Register the form and fields for our front-end submission form
 */
function wds_frontend_form_register() {
	$cmb = new_cmb2_box( array(
		'id'           => 'front-end-post-form',
		'object_types' => array( 'post' ),
		'hookup'       => false,
		'save_fields'  => false,
	) );

	$cmb->add_field( array(
		'name'    => __( 'New Post Title', 'wds-post-submit' ),
		'id'      => 'submitted_post_title',
		'type'    => 'text',
		'default' => __( 'New Post', 'wds-post-submit' ),
	) );

	$cmb->add_field( array(
		'name'    => __( 'New Post Content', 'wds-post-submit' ),
		'id'      => 'submitted_post_content',
		'type'    => 'wysiwyg',
		'options' => array(
			'textarea_rows' => 12,
			'media_buttons' => false,
		),
	) );

	$cmb->add_field( array(
		'name'       => __( 'Featured Image for New Post', 'wds-post-submit' ),
		'id'         => 'submitted_post_thumbnail',
		'type'       => 'text',
		'attributes' => array(
			'type' => 'file', // Let's use a standard file upload field
		),
	) );

	$cmb->add_field( array(
		'name' => __( 'Your Name', 'wds-post-submit' ),
		'desc' => __( 'Please enter your name for author credit on the new post.', 'wds-post-submit' ),
		'id'   => 'submitted_author_name',
		'type' => 'text',
	) );

	$cmb->add_field( array(
		'name' => __( 'Your Email', 'wds-post-submit' ),
		'desc' => __( 'Please enter your email so we can contact you if we use your post.', 'wds-post-submit' ),
		'id'   => 'submitted_author_email',
		'type' => 'text_email',
	) );

}
add_action( 'cmb2_init', 'wds_frontend_form_register' );

/**
 * Handle the cmb-frontend-form shortcode
 *
 * @param  array  $atts Array of shortcode attributes
 * @return string       Form html
 */
function wds_do_frontend_form_submission_shortcode( $atts = array() ) {

	// Current user
	$user_id = get_current_user_id();

	// Use ID of metabox in wds_frontend_form_register
	$metabox_id = 'front-end-post-form';

	// since post ID will not exist yet, just need to pass it something
	$object_id  = 'fake-oject-id';

	// Get CMB2 metabox object
	$cmb = cmb2_get_metabox( $metabox_id, $object_id );

	// Get $cmb object_types
	$post_types = $cmb->prop( 'object_types' );

	// Parse attributes
	$atts = shortcode_atts( array(
		'post_author' => $user_id ? $user_id : 1, // Current user, or admin
		'post_status' => 'pending',
		'post_type'   => reset( $post_types ), // Only use first object_type in array
	), $atts, 'cmb-frontend-form' );

	// Initiate our output variable
	$output = '';

	// Handle form saving (if form has been submitted)
	$new_id = wds_handle_frontend_new_post_form_submission( $cmb, $atts );

	if ( $new_id ) {

		if ( is_wp_error( $new_id ) ) {

			// If there was an error with the submission, add it to our ouput.
			$output .= '<h3>' . sprintf( __( 'There was an error in the submission: %s', 'wds-post-submit' ), '<strong>'. $new_id->get_error_message() .'</strong>' ) . '</h3>';

		} else {

			// Get submitter's name
			$name = isset( $_POST['submitted_author_name'] ) && $_POST['submitted_author_name']
				? ' '. $_POST['submitted_author_name']
				: '';

			// Add notice of submission
			$output .= '<h3>' . sprintf( __( 'Thank you %s, your new post has been submitted and is pending review by a site administrator.', 'wds-post-submit' ), esc_html( $name ) ) . '</h3>';
		}

	}

	// Get our form
	$output .= cmb2_get_metabox_form( $cmb, $object_id, array( 'save_button' => __( 'Submit Post', 'wds-post-submit' ) ) );

	return $output;
}
add_shortcode( 'cmb-frontend-form', 'wds_do_frontend_form_submission_shortcode' );

/**
 * Handles form submission on save
 *
 * @param  CMB2  $cmb       The CMB2 object
 * @param  array $post_data Array of post-data for new post
 * @return mixed            New post ID if successful
 */
function wds_handle_frontend_new_post_form_submission( $cmb, $post_data = array() ) {

	// If no form submission, bail
	if ( empty( $_POST ) ) {
		return false;
	}

	// check required $_POST variables and security nonce
	if (
		! isset( $_POST['submit-cmb'], $_POST['object_id'], $_POST[ $cmb->nonce() ] )
		|| ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() )
	) {
		return new WP_Error( 'security_fail', __( 'Security check failed.' ) );
	}

	if ( empty( $_POST['submitted_post_title'] ) ) {
		return new WP_Error( 'post_data_missing', __( 'New post requires a title.' ) );
	}

	/**
	 * Fetch sanitized values
	 */
	$sanitized_values = $cmb->get_sanitized_values( $_POST );

	// Set our post data arguments
	$post_data['post_title']   = $sanitized_values['submitted_post_title'];
	unset( $sanitized_values['submitted_post_title'] );
	$post_data['post_content'] = $sanitized_values['submitted_post_content'];
	unset( $sanitized_values['submitted_post_content'] );

	// Create the new post
	$new_submission_id = wp_insert_post( $post_data, true );

	// If we hit a snag, update the user
	if ( is_wp_error( $new_submission_id ) ) {
		return $new_submission_id;
	}

	/**
	 * Other than post_type and post_status, we want
	 * our uploaded attachment post to have the same post-data
	 */
	unset( $post_data['post_type'] );
	unset( $post_data['post_status'] );

	// Try to upload the featured image
	$img_id = wds_frontend_form_photo_upload( $new_submission_id, $post_data );

	// If our photo upload was successful, set the featured image
	if ( $img_id && ! is_wp_error( $img_id ) ) {
		set_post_thumbnail( $new_submission_id, $img_id );
	}

	// Loop through remaining (sanitized) data, and save to post-meta
	foreach ( $sanitized_values as $key => $value ) {
		update_post_meta( $new_submission_id, $key, $value );
	}

	return $new_submission_id;
}

/**
 * Handles uploading a file to a WordPress post
 *
 * @param  int   $post_id              Post ID to upload the photo to
 * @param  array $attachment_post_data Attachement post-data array
 */
function wds_frontend_form_photo_upload( $post_id, $attachment_post_data = array() ) {
	// Make sure the right files were submitted
	if (
		empty( $_FILES )
		|| ! isset( $_FILES['submitted_post_thumbnail'] )
		|| isset( $_FILES['submitted_post_thumbnail']['error'] ) && 0 !== $_FILES['submitted_post_thumbnail']['error']
	) {
		return;
	}

	// Filter out empty array values
	$files = array_filter( $_FILES['submitted_post_thumbnail'] );

	// Make sure files were submitted at all
	if ( empty( $files ) ) {
		return;
	}

	// Make sure to include the WordPress media uploader API if it's not (front-end)
	if ( ! function_exists( 'media_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
	}

	// Upload the file and send back the attachment post ID
	return media_handle_upload( 'submitted_post_thumbnail', $post_id, $attachment_post_data );
}
