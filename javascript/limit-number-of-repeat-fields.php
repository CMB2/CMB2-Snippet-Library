<?php
/**
 * Set a max limit on the number of times
 * a repeating field can be added.
 */

/**
 * Setup the metabox
 */
function js_limited_repeat_field_setup() {

	$cmb = new_cmb2_box( array(
		'id'           => 'test_limit_rows',
		'title'        => __( 'Repeating Field Group', 'cmb2' ),
		'object_types' => array( 'page', ),
		'rows_limit'   => 3,
	) );

	$cmb->add_field( array(
		'name' => 'Entry Title',
		'id'   => 'text_repeat_test',
		'type' => 'text',
		'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	) );
}
add_action( 'cmb2_admin_init', 'js_limited_repeat_field_setup', 9999 );

function js_limit_field_repeat( $post_id, $cmb ) {
	// Grab the custom attribute to determine the limit
	$limit = absint( $cmb->prop( 'rows_limit' ) );
	$limit = $limit ? $limit : 0;
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		// Only allow 3 groups
		var limit        = <?php echo $limit; ?>;
		var fieldGroupId = 'text_repeat_test';
		var $fieldTable  = $( document.getElementById( fieldGroupId + '_repeat' ) );
		var $repeatWrap  = $fieldTable.find( '.cmb-repeat-table.cmb-nested' ).first();

		var countRows = function() {
			return $repeatWrap.find( '.cmb-row.cmb-repeat-row' ).length;
		};

		var disableAdder = function() {
			$repeatWrap.parents( '.cmb-repeat.table-layout' ).find('.cmb-add-row-button.button').prop( 'disabled', true );
		};

		var enableAdder = function() {
			$repeatWrap.parents( '.cmb-repeat.table-layout' ).find('.cmb-add-row-button.button').prop( 'disabled', false );
		};

		$fieldTable
			.on( 'cmb2_add_row', function( evt, row ) {
				var $row = $( row );
				$repeatWrap = $row.parents( '.cmb-repeat-table.cmb-nested' );
				if ( countRows() >= limit ) {
					disableAdder();
				}
			})
			.on( 'cmb2_remove_row', function() {
				if ( countRows() < limit ) {
					enableAdder();
				}
			});
	});
	</script>
	<?php
}
add_action( 'cmb2_after_post_form_test_limit_rows', 'js_limit_field_repeat', 10, 2 );
