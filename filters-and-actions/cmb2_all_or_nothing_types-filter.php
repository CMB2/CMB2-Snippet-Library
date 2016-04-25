<?php

/**
 * CMB2 has a defined set of "all or nothing types".
 * This filter allows us to change that.
 */

function add_select2_to_all_or_nothing_types( $types ) {
	$types[] = 'select2';
	return $types;
}
add_filter( 'cmb2_all_or_nothing_types', 'add_select2_to_all_or_nothing_types' );
