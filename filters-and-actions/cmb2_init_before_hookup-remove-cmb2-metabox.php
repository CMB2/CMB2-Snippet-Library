<?php
/**
 * You can remove an entire CMB2 metabox. But be polite.
 */
function _yourprefix_remove_metabox() {
	// to remove a cmb metabox.. use wisely
	CMB2_Boxes::remove( '_yourprefix_demo_metabox' );
}
add_action( 'cmb2_init_before_hookup', '_yourprefix_remove_metabox' );
