<?php

/**
 * The 'file' type accepts any type of file which WordPress allows.
 * We want to change our text to indicate that they should be images.
 */
$modified_text_file_field = array(
	'name' => 'Image',
	'desc' => 'Upload an image.',
	'id'   => '_jt_cmb2_image',
	'type' => 'file',
	'options' => array(
		'add_upload_file_text' => __( 'Add or Upload Image', 'jt_cmb2' ),
	),
);
