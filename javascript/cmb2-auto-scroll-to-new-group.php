<?php
/*
 * Plugin Name: CMB2 Auto-scroll to new group
 * Description: This feature was removed in CMB2 2.0.3. Install this plugin to re-activate.
 * Author: jtsternberg
 * Author URI: http://dsgnwrks.pro
 * Version: 0.1.0
 */

function jt_cmb_group_autoscroll_js() {

	// If not cmb2 scripts on this page, bail
	if ( ! wp_script_is( 'cmb2-scripts', 'enqueued' ) ) {
		return;
	}

	?>
	<script type="text/javascript">
		window.CMB2 = window.CMB2 || {};
		(function(window, document, $, cmb, undefined){
			'use strict';

			// We'll keep it in the CMB2 object namespace
			cmb.initAutoScrollGroup = function(){
				cmb.metabox().find('.cmb-repeatable-group').on( 'cmb2_add_row', cmb.autoScrollGroup );
			};

			cmb.autoScrollGroup = function( evt, row ) {
				var $focus = $(row).find('input:not([type="button"]), textarea, select').first();
				if ( $focus.length ) {
					$( 'html, body' ).animate({
						scrollTop: Math.round( $focus.offset().top - 150 )
					}, 1000);
					$focus.focus();
				}
			};

			$(document).ready( cmb.initAutoScrollGroup );

		})(window, document, jQuery, CMB2);
	</script>
	<?php
}
add_action( 'admin_footer', 'jt_cmb_group_autoscroll_js', 999 );
