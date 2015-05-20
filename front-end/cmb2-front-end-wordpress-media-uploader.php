<?php
/**
 * Use the WordPress Media Uploader on the frontend. Limit to only displaying current
 * user's uploaded media. Props [@vasikgreif](https://github.com/vasikgreif)
 *
 * @link https://github.com/WebDevStudios/CMB2/issues/283 Original concept
 */

/**
 * Allow the media uploader work on specific pages
 */
function nevestam_allow_contributor_uploads() {
	if ( is_admin() ) {
		return;
	}

	$path = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';

	if ( ! $path || '/page-slug' != $path ) {
		return;
	}

	$subscriber = get_role( 'subscriber' );
	// whoa, manage_options is pretty heavy-handed.
	// @todo find lowest cap to allow media uploader
	$subscriber->add_cap( 'manage_options' );

}
add_action( 'init', 'nevestam_allow_contributor_uploads' );

/**
 * Display only user-uploaded files to each user
 */
function nevestam_restrict_media_library( $wp_query_obj ) {
	global $current_user, $pagenow;

	if ( ! is_a( $current_user, 'WP_User') ) {
		return;
	}

	if ( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' ) {
		return;
	}

	if ( ! current_user_can( 'manage_media_library' ) ) {
		$wp_query_obj->set( 'author', $current_user->ID );
	}
}
add_action( 'pre_get_posts', 'nevestam_restrict_media_library' );
