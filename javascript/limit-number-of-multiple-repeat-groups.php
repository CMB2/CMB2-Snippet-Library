<?php
/**
 * Set a max limit on the number of times
 * a repeating group can be added for various groups in one page
 */
/**
 * Setup the groups fields metabox
 */
function js_limited_group_setup() {
	/**
	 * Repeatable Field Groups
	 */
	$cmb_one = new_cmb2_box( array(
		'id'           => 'field_group_test_one',
		'title'        => __( 'Repeating Field Group One', 'cmb2' ),
		'object_types' => array( 'page', ),
		'rows_limit'   => 3, // custom attribute to use in our JS
    'groupId' => '_cmb2_repeat_group_one', // custom attribute to use in our JS to retrieve the ID of the group that should be handled
	) );
  
  $cmb_two = new_cmb2_box( array(
		'id'           => 'field_group_test_two',
		'title'        => __( 'Repeating Field Group Two', 'cmb2' ),
		'object_types' => array( 'page', ),
		'rows_limit'   => 2, // custom attribute to use in our JS
    'groupId' => '_cmb2_repeat_group_two', // custom attribute to use in our JS to retrieve the ID of the group that should be handled
	) );
	
  $group_one = $cmb_one->add_field( array(
		'id'          => '_cmb2_repeat_group_one',
		'type'        => 'group',
		'description' => __( 'Generates reusable form entries', 'cmb2' ),
		'options'     => array(
			'group_title'   => __( 'Entry {#}', 'cmb2' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add Another Entry', 'cmb2' ),
			'remove_button' => __( 'Remove Entry', 'cmb2' ),
			'sortable'      => true, // beta
		),
	) );
	
  $cmb_one->add_group_field( $group_one, array(
		'name'       => 'Entry Title',
		'id'         => 'title',
		'type'       => 'text',
	) );
	
  $cmb_one->add_group_field( $group_one, array(
		'name' => 'Description',
		'desc' => 'Write a short description for this entry',
		'id'   => 'description',
		'type' => 'textarea_small',
	) );
  
  $group_two = $cmb_two->add_field( array(
		'id'          => '_cmb2_repeat_group_two',
		'type'        => 'group',
		'description' => __( 'Generates reusable form entries', 'cmb2' ),
		'options'     => array(
			'group_title'   => __( 'Entry {#}', 'cmb2' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add Another Entry', 'cmb2' ),
			'remove_button' => __( 'Remove Entry', 'cmb2' ),
			'sortable'      => true, // beta
		),
	) );
	
  $cmb_two->add_group_field( $group_two, array(
		'name'       => 'Entry Title',
		'id'         => 'title',
		'type'       => 'text',
	) );
	
  $cmb_two->add_group_field( $group_two, array(
		'name' => 'Description',
		'desc' => 'Write a short description for this entry',
		'id'   => 'description',
		'type' => 'textarea_small',
	) );

}
add_action( 'cmb2_admin_init', 'js_limited_group_setup', 9999 );


$repeater_metaboxes = array('field_group_test_one','field_group_test_two'); // IDs of the metabox containing the repeater group

foreach ($repeater_metaboxe as $value) {
    add_action( 'cmb2_after_post_form_'.$value, 'js_limit_group_repeat', 10, 2);
}

function js_limit_group_repeat( $post_id, $cmb ) {
	// Grab the custom attribute to determine the limit
	$limit = absint( $cmb->prop( 'rows_limit' ) );
	$limit = $limit ? $limit : 0;
  $group =  $cmb->prop( 'groupId' )
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		// Only allow 3 groups
		var limit            = <?php echo $limit; ?>;
		var fieldGroupId     = '<?php echo $group; ?>'; 
		var $fieldGroupTable = $( document.getElementById( fieldGroupId + '_repeat' ) );
		var countRows = function() {
			return $fieldGroupTable.find( '> .cmb-row.cmb-repeatable-grouping' ).length;
		};
		var disableAdder = function() {
			$fieldGroupTable.find('.cmb-add-group-row.button-secondary').prop( {disabled: true} );
		};
		var enableAdder = function() {
			$fieldGroupTable.find('.cmb-add-group-row.button-secondary').prop( {disabled: false} );
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
