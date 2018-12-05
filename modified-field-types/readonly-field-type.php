<?php

// Do your $cmb = new_cmb2_box(), then add this field.
// See https://github.com/WebDevStudios/CMB2/blob/develop/example-functions.php for more.

/**
 * A CMB2 Readonly Field Type
 */
$cmb->add_field( array(
	'name'        => 'Read Only',
	'description' => 'The value of this input should be saved somewhere else.',
	'id'          => '_jtcmb2_readonly',
	'type'        => 'text',
	'save_field'  => false, // Otherwise CMB2 will end up removing the value.
	'attributes'  => array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	),
) );
