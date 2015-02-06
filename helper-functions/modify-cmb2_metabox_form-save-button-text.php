<?php

/**
 * To change just the text of the save button,
 * pass the third parameter (the arguments array),
 * and specify the 'save_button' param
 */
cmb2_metabox_form( $meta_box, $object_id, array(
	'save_button' => __( 'Save Settings', 'your-textdomain' ),
) );
