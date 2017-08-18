<?php
/**
 * @link http://webdevstudios.com/2015/03/30/use-cmb2-to-create-a-new-post-submission-form/ Original tutorial
 */


/**
 * Register the form and fields for our front-end submission form
 */
function yourprefix_frontend_form_register() {
	$cmb = new_cmb2_box( array(
		'id'           => 'front-end-post-form',
		'object_types' => array( 'post' ),
		'hookup'       => false,
		'save_fields'  => false,
	) );

	$cmb->add_field( array(
		'name'    => __( 'New Post Title', 'YOURTEXTDOMAIN' ),
		'id'      => 'submitted_post_title',
		'type'    => 'text',
		'default' => ! empty( $_POST['submitted_post_title'] )
			? $_POST['submitted_post_title']
			: __( 'New Post', 'YOURTEXTDOMAIN' ),
	) );

	$cmb->add_field( array(
		'default_cb' => 'yourprefix_maybe_set_default_from_posted_values',
		'name'       => __( 'New Post Content', 'YOURTEXTDOMAIN' ),
		'id'         => 'submitted_post_content',
		'type'       => 'wysiwyg',
		'options'    => array(
			'textarea_rows' => 12,
			'media_buttons' => false,
		),
	) );

	$cmb->add_field( array(
		'default_cb' => 'yourprefix_maybe_set_default_from_posted_values',
		'name'       => __( 'Featured Image for New Post', 'YOURTEXTDOMAIN' ),
		'id'         => 'submitted_post_thumbnail',
		'type'       => 'text',
		'attributes' => array(
			'type' => 'file', // Let's use a standard file upload field
		),
	) );

	$cmb->add_field( array(
		'default_cb' => 'yourprefix_maybe_set_default_from_posted_values',
		'name'       => __( 'Categories', 'YOURTEXTDOMAIN' ),
		'id'         => 'submitted_categories',
		'type'       => 'taxonomy_multicheck',
		'taxonomy'   => 'category', // Taxonomy Slug
	) );

	$cmb->add_field( array(
		'default_cb' => 'yourprefix_maybe_set_default_from_posted_values',
		'name'       => __( 'Your Name', 'YOURTEXTDOMAIN' ),
		'desc'       => __( 'Please enter your name for author credit on the new post.', 'YOURTEXTDOMAIN' ),
		'id'         => 'submitted_author_name',
		'type'       => 'text',
	) );

	$cmb->add_field( array(
		'default_cb' => 'yourprefix_maybe_set_default_from_posted_values',
		'name'       => __( 'Your Email', 'YOURTEXTDOMAIN' ),
		'desc'       => __( 'Please enter your email so we can contact you if we use your post.', 'YOURTEXTDOMAIN' ),
		'id'         => 'submitted_author_email',
		'type'       => 'text_email',
	) );

}
add_action( 'cmb2_init', 'yourprefix_frontend_form_register' );

/**
 * Sets the front-end-post-form field values if form has already been submitted.
 *
 * @return string
 */
function yourprefix_maybe_set_default_from_posted_values( $args, $field ) {
	if ( ! empty( $_POST[ $field->id() ] ) ) {
		return $_POST[ $field->id() ];
	}

	return '';
}

/**
 * Gets the front-end-post-form cmb instance
 *
 * @return CMB2 object
 */
function yourprefix_frontend_cmb2_get() {
	// Use ID of metabox in yourprefix_frontend_form_register
	$metabox_id = 'front-end-post-form';

	// Post/object ID is not applicable since we're using this form for submission
	$object_id  = 'fake-oject-id';

	// Get CMB2 metabox object
	return cmb2_get_metabox( $metabox_id, $object_id );
}

/**
 * Handle the cmb_frontend_form shortcode
 *
 * @param  array  $atts Array of shortcode attributes
 * @return string       Form html
 */
function yourprefix_do_frontend_form_submission_shortcode( $atts = array() ) {

	// Get CMB2 metabox object
	$cmb = yourprefix_frontend_cmb2_get();

	// Get $cmb object_types
	$post_types = $cmb->prop( 'object_types' );

	// Current user
	$user_id = get_current_user_id();

	// Parse attributes
	$atts = shortcode_atts( array(
		'post_author' => $user_id ? $user_id : 1, // Current user, or admin
		'post_status' => 'pending',
		'post_type'   => reset( $post_types ), // Only use first object_type in array
	), $atts, 'cmb_frontend_form' );

	/*
	 * Let's add these attributes as hidden fields to our cmb form
	 * so that they will be passed through to our form submission
	 */
	foreach ( $atts as $key => $value ) {
		$cmb->add_hidden_field( array(
			'field_args'  => array(
				'id'    => "atts[$key]",
				'type'  => 'hidden',
				'default' => $value,
			),
		) );
	}

	// Initiate our output variable
	$output = '';

	// Get any submission errors
	if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {
		// If there was an error with the submission, add it to our ouput.
		$output .= '<h3>' . sprintf( __( 'There was an error in the submission: %s', 'YOURTEXTDOMAIN' ), '<strong>'. $error->get_error_message() .'</strong>' ) . '</h3>';
	}

	// If the post was submitted successfully, notify the user.
	if ( isset( $_GET['post_submitted'] ) && ( $post = get_post( absint( $_GET['post_submitted'] ) ) ) ) {

		// Get submitter's name
		$name = get_post_meta( $post->ID, 'submitted_author_name', 1 );
		$name = $name ? ' '. $name : '';

		// Add notice of submission to our output
		$output .= '<h3>' . sprintf( __( 'Thank you%s, your new post has been submitted and is pending review by a site administrator.', 'YOURTEXTDOMAIN' ), esc_html( $name ) ) . '</h3>';
	}

	// Get our form
	$output .= cmb2_get_metabox_form( $cmb, 'fake-oject-id', array( 'save_button' => __( 'Submit Post', 'YOURTEXTDOMAIN' ) ) );

	return $output;
}
add_shortcode( 'cmb_frontend_form', 'yourprefix_do_frontend_form_submission_shortcode' );

/**
 * Handles form submission on save. Redirects if save is successful, otherwise sets an error message as a cmb property
 *
 * @return void
 */
function yourprefix_handle_frontend_new_post_form_submission() {

	// If no form submission, bail
	if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return false;
	}

	// Get CMB2 metabox object
	$cmb = yourprefix_frontend_cmb2_get();

	$post_data = array();

	// Get our shortcode attributes and set them as our initial post_data args
	if ( isset( $_POST['atts'] ) ) {
		foreach ( (array) $_POST['atts'] as $key => $value ) {
			$post_data[ $key ] = sanitize_text_field( $value );
		}
		unset( $_POST['atts'] );
	}

	// Check security nonce
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'security_fail', __( 'Security check failed.' ) ) );
	}

	// Check title submitted
	if ( empty( $_POST['submitted_post_title'] ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', __( 'New post requires a title.' ) ) );
	}

	// And that the title is not the default title
	if ( $cmb->get_field( 'submitted_post_title' )->default() == $_POST['submitted_post_title'] ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', __( 'Please enter a new title.' ) ) );
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
		return $cmb->prop( 'submission_error', $new_submission_id );
	}

	$cmb->save_fields( $new_submission_id, 'post', $sanitized_values );

	/**
	 * Other than post_type and post_status, we want
	 * our uploaded attachment post to have the same post-data
	 */
	unset( $post_data['post_type'] );
	unset( $post_data['post_status'] );

	// Try to upload the featured image
	$img_id = yourprefix_frontend_form_photo_upload( $new_submission_id, $post_data );

	// If our photo upload was successful, set the featured image
	if ( $img_id && ! is_wp_error( $img_id ) ) {
		set_post_thumbnail( $new_submission_id, $img_id );
	}

	/*
	 * Redirect back to the form page with a query variable with the new post ID.
	 * This will help double-submissions with browser refreshes
	 */
	wp_redirect( esc_url_raw( add_query_arg( 'post_submitted', $new_submission_id ) ) );
	exit;
}
add_action( 'cmb2_after_init', 'yourprefix_handle_frontend_new_post_form_submission' );

/**
 * Handles uploading a file to a WordPress post
 *
 * @param  int   $post_id              Post ID to upload the photo to
 * @param  array $attachment_post_data Attachement post-data array
 */
function yourprefix_frontend_form_photo_upload( $post_id, $attachment_post_data = array() ) {
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
