<?php
//By Daniele Mte90 Scasciafratte
//Based on https://github.com/WebDevStudios/CMB2-Post-Search-field
//That field have a list separated by a comma of post id and allow to sort and remove
//render post_list

function cmb2_post_list_render_field( $field, $escaped_value, $object_id, $object_type, $field_type ) {
	$select_type = $field->args( 'select_type' );

	echo $field_type->input( array(
	    'autocomplete' => 'off',
	    'style' => 'display:none'
	) );
	echo '<ul style="cursor:move">';
	if ( !empty( $field->escaped_value ) ) {
		$list = explode( ',', $field->escaped_value );
		foreach ( $list as $value ) {
			echo '<li data-id="' . trim( $value ) . '"><b>' . __( 'Title' ) . ':</b> ' . get_the_title( $value );
			echo '<div title="' . __( 'Remove' ) . '" style="color: #999;margin: -0.1em 0 0 2px; cursor: pointer;" class="cmb-post-list-remove dashicons dashicons-no"></div>';
			echo '</li>';
		}
	}
	echo '</ul>';

	// JS needed for modal
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-sortable' );

	if ( !is_admin() ) {
		// Will need custom styling!
		// @todo add styles for front-end
		require_once( ABSPATH . 'wp-admin/includes/template.php' );
	}

	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
		  'use strict';

		  $('.cmb-type-post-list').on('click', '.cmb-post-list-remove', function () {
		    var ids = $('.cmb-type-post-list.cmb2-id-<?php echo str_replace( '_', '-', sanitize_html_class( $field->args( 'id' ) ) ) ?>').find('.cmb-td input[type="text"]').val();
		    var $choosen = $(this);
		    if (ids.indexOf(',') !== -1) {
		      ids = ids.split(',');
		      var loopids = ids.slice(0);
		      $.each(loopids, function (index, value) {
			var cleaned = value.trim().toString();
			if (String($choosen.parent().data('id')) === cleaned) {
			  $choosen.parent().remove();
			  ids.splice(index, 1);
			}
		      });
		      $('.cmb-type-post-list.cmb2-id-<?php echo str_replace( '_', '-', sanitize_html_class( $field->args( 'id' ) ) ) ?>').find('.cmb-td input[type="text"]').val(ids.join(','));
		    } else {
		      $choosen.parent().remove();
		      $('.cmb-type-post-list.cmb2-id-<?php echo str_replace( '_', '-', sanitize_html_class( $field->args( 'id' ) ) ) ?>').find('.cmb-td input[type="text"]').val('');
		    }
		  });

		  $(".cmb-type-post-list.cmb2-id-<?php echo str_replace( '_', '-', sanitize_html_class( $field->args( 'id' ) ) ) ?> ul").sortable({
		    update: function (event, ui) {
		      var ids = [];
		      $('.cmb-type-post-list.cmb2-id-<?php echo str_replace( '_', '-', sanitize_html_class( $field->args( 'id' ) ) ) ?> ul li').each(function (index, value) {
			ids.push($(this).data('id'));
		      });
		      $('.cmb-type-post-list.cmb2-id-<?php echo str_replace( '_', '-', sanitize_html_class( $field->args( 'id' ) ) ) ?>').find('.cmb-td input[type="text"]').val(ids.join(', '));
		    }
		  });

		});
	</script>
	<?php
}

add_action( 'cmb2_render_post_list', 'cmb2_post_list_render_field', 10, 5 );

