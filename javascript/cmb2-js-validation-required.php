<?php
/*
 * Plugin Name: CMB2 JS require field
 * Description: This feature was removed in CMB2 2.0.3. Install this plugin to re-activate.
 * Author: jtsternberg
 * Author URI: http://dsgnwrks.pro
 * Version: 0.1.0
 */

/**
 * Hook in and add a validation demo metabox.
 */
function cmb2_sample_js_validation() {

	$prefix = 'js_validation_demo_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Test Metabox', 'cmb2' ),
		'object_types'  => array( 'page', ), // Post type
	) );

 	$cmb_demo->add_field( array(
		'name'       => __( 'Text', 'cmb2' ),
		'id'         => $prefix . 'text',
		'type'       => 'text',
		'attributes' => array(
			'data-validation' => 'required',
			// 'required' => 'required',
		),
	) );

	$cmb_demo->add_field( array(
		'name'       => __( 'Test Image', 'cmb2' ),
		'id'         => $prefix . 'image',
		'type'       => 'file',
		'options' => array(
			'url' => false,
		),
		'attributes' => array(
			'data-validation' => 'required',
			'required' => 'required',
		),
	) );

	$cmb_demo->add_field( array(
		'name'         => __( 'Multiple Files', 'cmb2' ),
		'desc'         => __( 'Upload or add multiple images/attachments.', 'cmb2' ),
		'id'           => $prefix . 'file_list',
		'type'         => 'file_list',
		'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		'attributes' => array(
			'data-validation' => 'required',
		),
	) );

}
add_action( 'cmb2_init', 'cmb2_sample_js_validation' );

function cmb2_after_form_do_js_validation( $post_id, $cmb ) {
	static $added = false;

	// Only add this to the page once (not for every metabox)
	if ( $added ) {
		return;
	}

	$added = true;
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {

		$form = $( document.getElementById( 'post' ) );
		$htmlbody = $( 'html, body' );

		function checkValidation( evt ) {
			var labels = [];
			var $first_error_row = null;
			var $row = null;

			function add_required( $row ) {
				$row.css({ 'background-color': 'rgb(255, 170, 170)' });
				$first_error_row = $first_error_row ? $first_error_row : $row;
				labels.push( $row.find( '.cmb-th label' ).text() );
			}

			function remove_required( $row ) {
				$row.css({ background: '' });
			}

			$( '[data-validation]' ).each( function() {
				var $this = $(this);
				var val = $this.val();
				$row = $this.parents( '.cmb-row' );

				if ( $this.is( '[type="button"]' ) || $this.is( '.cmb2-upload-file-id' ) ) {
					return true;
				}

				if ( 'required' === $this.data( 'validation' ) ) {
					if ( $row.is( '.cmb-type-file-list' ) ) {

						var has_LIs = $row.find( 'ul.cmb-attach-list li' ).length > 0;

						if ( ! has_LIs ) {
							add_required( $row );
						} else {
							remove_required( $row );
						}

					} else {
						if ( ! val ) {
							add_required( $row );
						} else {
							remove_required( $row );
						}
					}
				}

			});

			if ( $first_error_row ) {
				evt.preventDefault();
				alert( '<?php _e( 'The following fields are required and highlighted below:', 'cmb2' ); ?> ' + labels.join( ', ' ) );
				$htmlbody.animate({
					scrollTop: ( $first_error_row.offset().top - 200 )
				}, 1000);
			} else {
				// Feel free to comment this out or remove
				alert( 'submission is good!' );
			}

		}

		$form.on( 'submit', checkValidation );
	});
	</script>
	<?php
}

add_action( 'cmb2_after_form', 'cmb2_after_form_do_js_validation', 10, 2 );
