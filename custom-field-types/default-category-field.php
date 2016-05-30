<?php
/**
 * CMB2 Default Category field/metabox
 *
 * @package CMB2 Default Category field/metabox
 */

/**
 * Adds a custom field type which uses the WordPress default post-categories metabox.
 *
 * @param  object $field             The CMB2_Field type object.
 * @param  string $value             The saved (and escaped) value.
 * @param  int    $object_id         The current post ID.
 * @param  string $object_type       The current object type.
 * @return void
 */
function cmb2_render_default_categories_field_type( $field, $escaped_value, $object_id, $object_type ) {

	$taxonomy = $field->args( 'taxonomy' );
	$taxonomy = $taxonomy ? $taxonomy : 'category';

	if ( 'post' !== $object_type ) {
		wp_die( 'This won\'t work for non-"post" object types!' );
	}

	remove_meta_box( "{$object_type}div", 'post', 'side' );

	post_categories_meta_box( get_post( $object_id ), array(
		'args' => array(
			'taxonomy' => $taxonomy,
		),
	) );
}
add_action( 'cmb2_render_default_categories', 'cmb2_render_default_categories_field_type', 10, 4 );

function cmb2_default_categories_let_wp_save( $null, $a, $field_args, $field ) {
	if ( 'default_categories' === $field->args( 'type' ) ) {
		// Let WP handle it.
		return false;
	}

	return $null;
}
add_filter( 'cmb2_override_meta_save', 'cmb2_default_categories_let_wp_save', 10, 4 );

function cmb2_remove_default_category_metabox_for_taxonomy() {
	foreach ( CMB2_Boxes::get_all() as $cmb ) {
		foreach ( $cmb->prop( 'fields' ) as $field ) {
			if ( 'default_categories' === $field['type'] ) {
				$taxonomy = isset( $field['taxonomy'] ) ? $field['taxonomy'] : 'category';
				remove_meta_box( "{$taxonomy}div", 'post', 'side' );
			}
		}
	}
}
add_action( 'admin_init', 'cmb2_remove_default_category_metabox_for_taxonomy' );
