<?php
/**
 * Display CMB2 fields in other areas of the post screen (not in metaboxes)
 */

/**
 * Hook in and add a demo metabox.
 */
function yourprefix_register_cmb2_fields() {

	$cmb = new_cmb2_box( array(
		'id'            => '_yourprefix_display_title',
		'object_types'  => array( 'page' ),
		//'title' => '', omit the 'title' field to keep the normal wp metabox from displaying
	) );

	$cmb->add_field( array(
		'name' => 'Display title for this page?',
		'id'   => '_yourprefix_display_title',
		'type' => 'checkbox',
	) );

	$cmb->add_field( array(
		'name' => 'A textarea',
		'id'   => '_yourprefix_display_title_text',
		'type' => 'textarea',
	) );

}
add_action( 'cmb2_admin_init', 'yourprefix_register_cmb2_fields' );


/**
 * Display checkbox metabox below title field
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L560-L567
 */
function yourprefix_output_custom_mb_location() {
	cmb2_get_metabox( '_yourprefix_display_title' )->show_form();
}
add_action( 'edit_form_after_editor', 'yourprefix_output_custom_mb_location' );

/**
 * More hooks in the post-editor screen as of 4.1
 */

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L217-L225
 */
// add_action( 'dbx_post_advanced', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L241-L249
 */
// add_action( 'add_meta_boxes', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L416-L423
 */
// add_action( 'post_edit_form_tag', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L447-L456
 */
// add_action( 'edit_form_top', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L480-L487
 */
// add_action( 'edit_form_before_permalink', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L516-L523
 */
// add_action( 'edit_form_after_title', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L560-L567
 */
// add_action( 'edit_form_after_editor', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L574-L597
 */
// add_action( 'submitpost_box', 'yourprefix_output_custom_mb_location' );
// add_action( 'submitpage_box', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L609-L628
 */
// add_action( 'edit_form_advanced', 'yourprefix_output_custom_mb_location' );
// add_action( 'edit_page_form', 'yourprefix_output_custom_mb_location' );

/**
 * @link https://github.com/WordPress/WordPress/blob/56d6682461be82da1a3bafc454dad2c9da451a38/wp-admin/edit-form-advanced.php#L636-L643
 */
// add_action( 'dbx_post_sidebar', 'yourprefix_output_custom_mb_location' );
