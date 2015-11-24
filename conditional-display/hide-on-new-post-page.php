<?php
/**
 * Remove metabox from appearing on post new screens before the post has been saved.
 * Can also add additional screens to exclude via the `'exclude_from'` property.
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters#example-exclude-on-new-post-screens wiki
 */

$cmb = new_cmb2_box( array(
	'id'           => 'exclude_for_ids',
	'title'        => 'Demo',
	'exclude_from' => array( 'post-new.php' ), // Exclude metabox on new-post screen
	'show_on_cb'   => 'tgm_exclude_from_new', // function should return a bool value
) );

/**
 * Removes metabox from appearing on post new screens before the post
 * ID has been set.
 * @author Thomas Griffin
 * @param  object $cmb CMB2 object
 * @return bool        True/false whether to show the metabox
 */
function tgm_exclude_from_new( $cmb ) {
	global $pagenow;

	$exclude_from = $cmb->prop( 'exclude_from', array( 'post-new.php' ) );
	$excluded = in_array( $pagenow, $exclude_from, true );

	return ! $excluded;
}
