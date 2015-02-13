<?php

/**
 * A demo of the helper function used to 
 * localise any date picker form in CMB2.
 * See http://api.jqueryui.com/datepicker/
 * for more info.
 * Refer to the CMB Field Types Wiki entry
 * if you wish to implement a different date format
 * per meta field using date_format.
 * This snippet was modified from one at the following link:
 * https://github.com/WebDevStudios/CMB2/issues/73
 */

add_filter( 'cmb2_localized_data', 'prefix_cmb_set_date_format' );
function prefix_cmb_set_date_format( $l10n ) {
	// Set your date format replacing 'dd-mm-yy' (UK format)
    $l10n['defaults']['date_picker']['dateFormat'] = 'dd-mm-yy';
    return $l10n;
}