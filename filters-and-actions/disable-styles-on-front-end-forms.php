<?php
/**
 * This file demonstrates how to disable CMB2 styles on all front end forms.
 */

function yourprefix_disable_cmb2_front_end_styles( $enabled ) {

	if ( ! is_admin() ) {
		$enabled = false;
	}

	return $enabled;
}
add_filter( 'cmb2_enqueue_css', 'yourprefix_disable_cmb2_front_end_styles' );
