<?php
/**
 * Use CMB2 fields to replace WordPress title/content/thumbnail fields.
 */

/**
 * Register a dummy cpt for demonstration
 */
function register_test_cpt() {
	$labels = array(
		'name'                => 'CPT',
		'singular_name'       => 'CPT',
		'menu_name'           => 'CPT',
		'parent_item_colon'   => 'Parent CPT',
		'all_items'           => 'All CPT',
		'view_item'           => 'View CPT',
		'add_new_item'        => 'Add New CPT',
		'add_new'             => 'Add New',
		'edit_item'           => 'Edit CPT',
		'update_item'         => 'Update CPT',
		'search_items'        => 'Search CPT',
		'not_found'           => 'Not Found',
		'not_found_in_trash'  => 'Not found in Trash',
	);

  register_post_type( 'cpt', array(
		'labels' => $labels,
		'supports' => array( 'title' ),
		'supports' => array( '' ),
		'public' => true,
		'has_archive' => true,
	) );
}
add_action( 'init', 'register_test_cpt' );

function register_test_cpt_metabox() {

	$cmb_subsub = new_cmb2_box( array (
		'id'           => 'metabox',
		'title'        => 'Edit',
		'object_types' => array( 'cpt' ),
	) );

	$cmb_subsub->add_field( array(
		'id'      => 'post_title', // Saves to WP post title, allows the_title()
		'name'    => 'Title',
		'desc'    => 'Provide a title.',
		'default' => '',
		'type'    => 'text',
	) );

	$cmb_subsub->add_field( array(
		'id'   => 'post_content', // Saves to WP post content, allows the_content()
		'name' => 'Description',
		'desc' => 'Enter a brief description',
		'type' => 'textarea', // wysiwyg is problematic when replacing post_content
	) );

	$cmb_subsub->add_field( array(
		'id'      => '_thumbnail', // Saves to WP post thumbnail, allows the_post_thumbnail()
		'name'    => 'Image',
		'desc'    => 'Upload/Select an image.',
		'type'    => 'file',
		'options' => array(
			'url' => false,
		),
		'text' => array(
			'add_upload_file_text' => 'Add Image'
		),
	) );

};
add_action( 'cmb2_admin_init', 'register_test_cpt_metabox' );

/*
 * Override the title/content field retrieval so CMB2 doesn't look in post-meta.
 */
function cmb2_override_post_title_display( $data, $post_id ) {
	return get_post_field( 'post_title', $post_id );
}
function cmb2_override_post_content_display( $data, $post_id ) {
	return get_post_field( 'post_content', $post_id );
}
add_filter( 'cmb2_override_post_title_meta_value', 'cmb2_override_post_title_display', 10, 2 );
add_filter( 'cmb2_override_post_content_meta_value', 'cmb2_override_post_content_display', 10, 2 );

/*
 * WP will handle the saving for us, so don't save title/content to meta.
 */
add_filter( 'cmb2_override_post_title_meta_save', '__return_true' );
add_filter( 'cmb2_override_post_content_meta_save', '__return_true' );
