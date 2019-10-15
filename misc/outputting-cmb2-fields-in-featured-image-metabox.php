<?php
/**
 * Display CMB2 fields in the featured-image metabox.
 */

function yourprefix_feat_img_fields() {

	$cmb = new_cmb2_box( array(
		'id'           => 'feat-image-fields',
		'object_types' => array( 'post' ),
	) );

	$cmb->add_field( array(
		'name' => 'Featured Image Position',
		'id'   => 'feat_img_placement',
		'type' => 'select',
		'options' => array(
			''      => 'Center', // The default -- no value. Keeps out of the database.
			'left'  => 'Left',
			'right' => 'Right',
		),
		'before' => '<style>
		#cmb2-metabox-feat-image-fields .cmb-th,
		#cmb2-metabox-feat-image-fields .cmb-td,
		#side-sortables .cmb2-wrap #cmb2-metabox-feat-image-fields .cmb-row {
			padding: 0;
		}
		</style>',
	) );

}
add_action( 'cmb2_admin_init', 'yourprefix_feat_img_fields' );

function yourprefix_feat_img_output_fields( $content, $post_id, $thumbnail_id ) {
	$cmb = cmb2_get_metabox( 'feat-image-fields' );

	if ( $cmb && in_array( get_post_type(), $cmb->prop( 'object_types' ), 1 ) ) {
		ob_start();
		$cmb->show_form();
		// grab the data from the output buffer and add it to our $content variable
		$content .= ob_get_clean();
	}

	return $content;
}
add_filter( 'admin_post_thumbnail_html', 'yourprefix_feat_img_output_fields', 10, 3 );
