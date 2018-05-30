<?php
/**
 * Register a wysiwyg field and add the wordcount below it.
 */

function yourprefix_feat_img_fields() {

	$cmb = new_cmb2_box( array(
		'id'           => 'hold-my-wysiwyg',
		'object_types' => array( 'post' ),
	) );

	$cmb->add_field( array(
		'name'  => __( 'WYSIWYG content', 'YOURTEXTDOMAIN' ),
		'id'    => 'wysiwyg_content',
		'type'  => 'wysiwyg',
		'after' => 'cmb2_wysiwyg_word_counter',
	) );

}
add_action( 'cmb2_admin_init', 'yourprefix_feat_img_fields' );

/**
 * Outputs wordcount for wysiwyg field.
 *
 * Basically copied from: https://github.com/WordPress/WordPress/blob/3099f4d9edc5f2f2b6ef8becc966135edde909a8/wp-admin/js/post.js#L1219-L1271
 * and: https://github.com/WordPress/WordPress/blob/3099f4d9edc5f2f2b6ef8becc966135edde909a8/wp-admin/edit-form-advanced.php#L714
 */
function cmb2_wysiwyg_word_counter( $args, $field ) {
	wp_enqueue_script( 'word-count', array( 'jquery', 'underscore', 'word-count' ) );
	?>

	<p id="<?php echo $field->id(); ?>-word-count" class="hide-if-no-js cmb2-wysiwyg-word-count"><?php printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' ); ?></p>

	<script type="text/javascript">
		jQuery( function( $ ) {
			var editorId = '<?php echo $field->id(); ?>';
			/**
			 * TinyMCE word count display
			 */
			( function( $, counter ) {
				$( function() {
					var $content = $( '#' + editorId ),
						$count = $( '#' + editorId + '-word-count' ).find( '.word-count' ),
						prevCount = 0,
						contentEditor;

					/**
					 * Get the word count from TinyMCE and display it
					 */
					function update() {
						var text, count;

						if ( ! contentEditor || contentEditor.isHidden() ) {
							text = $content.val();
						} else {
							text = contentEditor.getContent( { format: 'raw' } );
						}

						count = counter.count( text );

						if ( count !== prevCount ) {
							$count.text( count );
						}

						prevCount = count;
					}

					/**
					 * Bind the word count update triggers.
					 *
					 * When a node change in the main TinyMCE editor has been triggered.
					 * When a key has been released in the plain text content editor.
					 */
					$( document ).on( 'tinymce-editor-init', function( event, editor ) {
						if ( editor.id !== editorId ) {
							return;
						}

						contentEditor = editor;

						editor.on( 'nodechange keyup', _.debounce( update, 1000 ) );
					} );

					$content.on( 'input keyup', _.debounce( update, 1000 ) );

					update();
				} );
			} )( jQuery, new wp.utils.WordCounter() );

		});

	</script>
	<?php
}
