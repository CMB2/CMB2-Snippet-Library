<?php
/**
 * Exclude metabox on non top level posts
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters#example-exclude-on-non-top-level-posts wiki
 */

$cmb = new_cmb2_box( array(
	'id'         => 'exclude_for_ids',
	'title'      => 'Demo',
	'show_on_cb' => 'ba_metabox_add_for_top_level_posts_only', // function should return a bool value
) );

/**
 * Exclude metabox on non top level posts
 * @author Travis Northcutt
 * @param  object $cmb CMB2 object
 * @return bool        True/false whether to show the metabox
 */
function ba_metabox_add_for_top_level_posts_only( $cmb ) {
	$has_parent = $cmb->object_id() && get_post_ancestors( $cmb->object_id() );

	return ! $has_parent;
}
