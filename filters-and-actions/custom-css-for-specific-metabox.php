<?php
/**
 * Add custom CSS which only loads for a particular metabox
 */

/**
 * Setup the metabox
 */
function js_custom_css_for_metabox() {
	$cmb = new_cmb2_box( array(
		'id'           => 'custom_css_test',
		'title'        => __( 'Custom CSS Test', 'cmb2' ),
		'object_types' => array( 'page', ),
	) );

	$cmb->add_field( array(
		'id'          => '_cmb2_test_text',
		'type'        => 'text',
	) );
}
add_action( 'cmb2_admin_init', 'js_custom_css_for_metabox' );

function js_add_custom_css_for_metabox( $post_id, $cmb ) {
	?>
	<style type="text/css" media="screen">
		#custom_css_test .regular-text {
		  width: 99%;
		}
	</style>
	<?php
}
add_action( 'cmb2_after_post_form_custom_css_test', 'js_add_custom_css_for_metabox', 10, 2 );
