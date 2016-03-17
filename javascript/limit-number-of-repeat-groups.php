<?php
/**
 * Set a max limit on the number of times
 * a repeating group can be added.
 */

/**
 * Setup the group field metabox
 */
function js_limited_group_setup() {
	/**
	 * Repeatable Field Groups
	 */
	$cmb = new_cmb2_box( array(
		'id'           => 'field_group_test',
		'title'        => __( 'Repeating Field Group', 'cmb2' ),
		'object_types' => array( 'page', ),
		'rows_limit'   => 3, // custom attribute to use in our JS
	) );

	$group_id = $cmb->add_field( array(
		'id'          => '_cmb2_repeat_group',
		'type'        => 'group',
		'description' => __( 'Generates reusable form entries', 'cmb2' ),
		'options'     => array(
			'group_title'   => __( 'Entry {#}', 'cmb2' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add Another Entry', 'cmb2' ),
			'remove_button' => __( 'Remove Entry', 'cmb2' ),
			'sortable'      => true, // beta
		),
	) );

	$cmb->add_group_field( $group_id, array(
		'name'       => 'Entry Title',
		'id'         => 'title',
		'type'       => 'text',
	) );

	$cmb->add_group_field( $group_id, array(
		'name' => 'Description',
		'desc' => 'Write a short description for this entry',
		'id'   => 'description',
		'type' => 'textarea_small',
	) );
}
add_action( 'cmb2_admin_init', 'js_limited_group_setup', 9999 );

function js_limit_group_repeat( $post_id, $cmb ) {
	// Grab the custom attribute to determine the limit
	$limit = absint( $cmb->prop( 'rows_limit' ) );
	$limit = $limit ? $limit : 0;
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		// Only allow 3 groups
		var limit            = <?php echo $limit; ?>;
		var fieldGroupId     = '_cmb2_repeat_group';
		var $fieldGroupTable = $( document.getElementById( fieldGroupId + '_repeat' ) );

		var countRows = function() {
			return $fieldGroupTable.find( '> .cmb-row.cmb-repeatable-grouping' ).length;
		};

		var disableAdder = function() {
			$fieldGroupTable.find('.cmb-add-group-row.button').prop( 'disabled', true );
		};

		var enableAdder = function() {
			$fieldGroupTable.find('.cmb-add-group-row.button').prop( 'disabled', false );
		};

		$fieldGroupTable
			.on( 'cmb2_add_row', function() {
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
add_action( 'cmb2_after_post_form_field_group_test', 'js_limit_group_repeat', 10, 2 );
