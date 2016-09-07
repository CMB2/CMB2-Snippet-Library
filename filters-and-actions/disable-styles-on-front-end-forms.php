<?php
/**
 * This file demonstrates how to disable CMB2 styles on all front end forms.
 * 
 * It’s important, when using the `cmb2_enqueue_*` filters to only disable for your
 * specific conditions/scenarios. It’s entirely possible that another plugin/theme is
 * using CMB2 and *needs* the styles on the front-end, so blindly disabling can cause issues.
 */

function yourprefix_disable_cmb2_front_end_styles( $enabled ) {

	if ( ! is_admin() /* && meets_your_specific_conditions() */ ) {
		$enabled = false;
	}

	return $enabled;
}
add_filter( 'cmb2_enqueue_css', 'yourprefix_disable_cmb2_front_end_styles' );
