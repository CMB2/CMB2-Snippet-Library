<?php
/**
 * You can remove an entire CMB2 metabox. But be polite.
 */
function yourprefix_remove_metabox() {
	// to remove a cmb metabox.. use wisely
	CMB2_Boxes::remove( '_yourprefix_demo_metabox' );
}
add_action( 'cmb2_init_before_hookup', 'yourprefix_remove_metabox' );
