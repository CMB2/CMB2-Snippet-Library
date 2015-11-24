<?php
/**
 * Allow metabox to show up everywhere except a specified list of page IDs.
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters#example-exclude-on-ids wiki
 */

$cmb = new_cmb2_box( array(
	'id'           => 'exclude_for_ids',
	'title'        => 'Demo',
	'exclude_ids' => array( 1, 2, 3, 55 ), // Exclude metabox on these post-ids
	'show_on_cb' => 'cmb2_exclude_for_ids', // function should return a bool value
) );

/**
 * Exclude metabox on specific IDs
 * @param  object $cmb CMB2 object
 * @return bool        True/false whether to show the metabox
 */
function cmb2_exclude_for_ids( $cmb ) {
	$ids_to_exclude = $cmb->prop( 'exclude_ids', array() );
	$excluded = in_array( $cmb->object_id(), $ids_to_exclude, true );

	return ! $excluded;
}
