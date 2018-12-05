<?php

// Do your $cmb = new_cmb2_box(), then add this field.
// See https://github.com/WebDevStudios/CMB2/blob/develop/example-functions.php for more.

/**
 * The 'file' type accepts any type of file which WordPress allows.
 * We want to change our text to indicate that they should be images.
 */
$cmb->add_field( array(
	'name' => 'Image',
	'desc' => 'Upload an image.',
	'id'   => '_jt_cmb2_image',
	'type' => 'file',
	'options' => array(
		'add_upload_file_text' => __( 'Add or Upload Image', 'jt_cmb2' ),
	),
) );
