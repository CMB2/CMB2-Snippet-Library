<?php
/*
 * Frontend editor, including title/content
 */

function jt_add_edit_form_to_frontend( $content ) {
	if ( isset( $_GET['edit'] ) ) {
		if ( current_user_can( 'edit_posts' ) && wp_verify_nonce( $_GET['edit'], 'edit' ) ) {
			$content = cmb2_get_metabox_form( '_yourprefix_demo_metabox', get_the_ID() );
		} else {
			$content = '<h2 class="edit-error">You do not have permission to edit this post.</h2>';
		}
	}

	return $content;
}
add_filter( 'the_content', 'jt_add_edit_form_to_frontend' );

/**
 * Modify the edit links to point to the front-end editor.
 */
function jt_modify_edit_link( $link ) {
	if ( ! is_admin() ) {
		$link = esc_url_raw( wp_nonce_url( remove_query_arg( 'edit' ), 'edit', 'edit' ) );
	}
	return $link;
}
add_filter( 'get_edit_post_link', 'jt_modify_edit_link' );

/**
 * Hook in later and prepend our title/content fields to our existing metabox
 */
function jt_edit_core_fields() {
	if ( ! is_admin() ) { // only if on front-end

		// Get existing metabox
		$cmb = cmb2_get_metabox( '_yourprefix_demo_metabox' );

		// and prepend title
		$cmb->add_field( array(
			'name'   => __( 'Title', 'cmb2' ),
			'id'     => 'post_title',
			'type'   => 'text',
			'before' => 'jt_edit_core_maybe_redirect',
		), 1 );

		// and content fields
		$cmb->add_field( array(
			'name'  => __( 'Content', 'cmb2' ),
			'id'    => 'post_content',
			'type'  => 'wysiwyg',
		), 2 );
	}
}
add_action( 'cmb2_init', 'jt_edit_core_fields', 99 );

/**
 * If edit was saved, redirect to non-edit page
 */
function jt_edit_core_maybe_redirect() {
	if ( isset( $_POST['post_content'] ) ) {
		$url = esc_url_raw( remove_query_arg( 'edit' ) );
		echo "<script type='text/javascript'>window.location.href = '$url';</script>";
	}
}

/**
 * We don't want CMB2 to fetch data from meta for post title and post content
 */
function jt_cmb2_override_core_field_get( $val, $object_id, $a, $field ) {
	global $post;

	if ( in_array( $field->id(), array( 'post_title', 'post_content' ), true ) ) {
		if ( isset( $post->ID ) ) {
			$val = get_post_field( $field->id(), $post );
		} else {
			$val = '';
		}
	}

	return $val;
}
add_filter( 'cmb2_override_meta_value', 'jt_cmb2_override_core_field_get', 10, 4 );

/**
 * We don't want CMB2 to save data to meta for post title and post content
 */
function jt_cmb2_override_core_field_set( $status, $a, $args, $field ) {
	global $post;

	if ( in_array( $field->id(), array( 'post_title', 'post_content' ), true ) ) {
		if ( isset( $post->ID ) ) {
			$status = wp_update_post( array(
				$field->id() => $a['value'],
				'ID' => $post->ID,
			) );

		} else {
			$status = false;
		}
	}

	return $status;
}
add_filter( 'cmb2_override_meta_save', 'jt_cmb2_override_core_field_set', 10, 4 );
