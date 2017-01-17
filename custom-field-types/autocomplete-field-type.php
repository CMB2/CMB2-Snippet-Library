<?php

/*
 * Plugin Name: CMB2 Custom Field Type - Autocomplete
 * Description: Makes available an autocomplete custom field type.
 * Author: johnsonpaul1014
 * Version: 1.1.0
 */

/**
 * It is a little complex but is very flexible, and it is a great option when you have
 * way too many things to put in a select.
 *
 * It uses a hidden field for the CMB2 data, and puts the value from the input used for the
 * autocomplete in the hidden field on blur of the autocomplete field so it always gets populated.
 *
 * There are two types available: options pre-built and one that uses a remote source.
 * To use the first option, simply build it like a select with the standard CMB2 "options" argument.
 *
 * For a remote source, you will use the "source" argument that corresponds to an AJAX function.
 * Then, you pass in a "mapping_function" to look up the selected autocomplete value using the
 * CMB2 field value.
 *
 * The example fields in this plugin demonstrate all types: pre-built single field,
 * pre-built repeatable field, remote single field and remote repeatable field.
 */

/**
 * Define the metabox and field configurations.
 */
function autocomplete_cmb2_meta_boxes() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_autocomplete_cmb2_';

	$cmb = new_cmb2_box( array(
		'id' => 'autocomplete_test',
		'title' => __('Autocomplete Field Examples', 'autocomplete_cmb2'),
		'object_types' => array('post'),
	) );

	$cmb->add_field( array(
		'name' => __('Related Fruit', 'autocomplete_cmb2'),
		'desc' => __('Fruit that is related to this post', 'autocomplete_cmb2'),
		'id' => $prefix.'related_fruit',
		'type' => 'autocomplete',
		'options' => array(
			1 => 'Apple',
			2 => 'Orange',
			3 => 'Grape'
		)
	) );
	$cmb->add_field( array(
		'name' => __('Related Fruits', 'autocomplete_cmb2'),
		'desc' => __('Repeatable related fruits', 'autocomplete_cmb2'),
		'id' => $prefix.'related_fruits',
		'type' => 'autocomplete',
		'repeatable' => true,
		'options' => array(
			1 => 'Apple',
			2 => 'Orange',
			3 => 'Grape'
		)
	) );
	$cmb->add_field( array(
		'name' => __('Related Post', 'autocomplete_cmb2'),
		'desc' => __('Post that is related to this one', 'autocomplete_cmb2'),
		'id' => $prefix.'related_post',
		'type' => 'autocomplete',
		'source' => 'get_post_options',
		'mapping_function' => 'autocomplete_cmb2_get_post_title_from_id'
	) );
	$cmb->add_field( array(
		'name' => __('Related Posts', 'autocomplete_cmb2'),
		'desc' => __('Posts that are related to this one', 'autocomplete_cmb2'),
		'id' => $prefix.'related_posts',
		'repeatable' => true,
		'type' => 'autocomplete',
		'source' => 'get_post_options',
		'mapping_function' => 'autocomplete_cmb2_get_post_title_from_id'
	) );
}

/**
 * Gets the post title from the ID for mapping purposes in autocompletes.
 *
 * @param int $id
 * @return string
 */
function autocomplete_cmb2_get_post_title_from_id($id) {
	if (empty($id)) {
		return '';
	}

	$post = get_post($id);

	return $post->post_title;
}

/**
 * Renders the autocomplete type
 *
 * @param CMB2_Field $field_object
 * @param string $escaped_value The value of this field passed through the escaping filter. It defaults to sanitize_text_field.
 *                 If you need the unescaped value, you can access it via $field_type_object->value().
 * @param string $object_id The id of the object you are working with. Most commonly, the post id.
 * @param string $object_type The type of object you are working with. Most commonly, post (this applies to all post-types),
 *                but could also be comment, user or options-page.
 * @param CMB2_Object $field_type_object This is an instance of the CMB2 object and gives you access to all of the methods that CMB2 uses to build its field types.
 */
function autocomplete_cmb2_render_autocomplete($field_object, $escaped_value, $object_id, $object_type, $field_type_object) {

	// Used to get the current one being rendered.  This isn't stored anywhere unfortunately.
	static $current_field_nums = array();

	$id = $field_object->args['id'];

	if (isset($current_field_nums[$id])) {
		$current_field_num = ++$current_field_nums[$id];
	} else {
		$current_field_num = $current_field_nums[$id] = 1;
	}

	// Get rid of notice.
	if (is_array($field_object->escaped_value)) {
		$field_object->escaped_value = '';
	}

	// Store the value in a hidden field.
	echo $field_type_object->hidden();

	$options = $field_object->args['options'];

	// Set up the options or source PHP variables.
	if (empty($options)) {
		$source = $field_object->args['source'];
		$value = $field_object->args['mapping_function']($field_object->escaped_value);
	} else {

		// Set the value.
		if (empty($field_object->escaped_value)) {
			$value = '';
		} else {
			foreach ($options as $option_value => $option_name) {
				if ($option_value == $field_object->escaped_value) {
					$value = $option_name;
					break;
				}
			}
		}
	}

	// Don't use the ID on repeatable elements as it won't change; use the class instead.
	echo '<input size="50" value="'.htmlspecialchars($value).'" class="'.$id.'-autocomplete"/>';

	if (!$field_object->args['repeatable'] && isset($field_object->args['desc'])) {
		echo '<p class="cmb2-metabox-description">'.$field_object->args['desc'].'</p>';
	}

	// Now, set up the script.  Only output it for the first field, because the rest can use the same options.
	if ($current_field_num === 1) {
		?>
		<script>
			jQuery(document).ready(function($) {
				var options = [];
				var nameToValue = [];

				<?php

				if (!empty($options)) {
					foreach ($options as $option_value => $option_name) {
						echo "options.push('".addcslashes($option_name, "'")."');\r\n";
						echo "nameToValue['".addcslashes($option_name, "'")."'] = '".$option_value."';\r\n";
					}
				}

				// Bind to the parent grouping a focus in event on the inputs to build the autocompletes if they aren't there in the
				// repeatable case.
				if ($field_object->args['repeatable']) { ?>
				$('.<?php echo $id; ?>-autocomplete').eq(0).parents('.cmb-repeat-table').on('focusin', 'input', function() {
					if (typeof($(this).data('ui-autocomplete')) === 'undefined') {
						<?php
						} else { // Non repeatable ?>
						$('.<?php echo $id; ?>-autocomplete').each(function(i, el) {
							<?php
							}
							?>
							$(this).autocomplete({
								delay: 500,
								source: <?php if (empty($options)) { ?>
									function(request, response) {
										$.ajax(
											{url: ajaxurl,
												data: {
													action: '<?php echo $source; ?>',
													nonce: '<?php echo wp_create_nonce($source); ?>',
													q: request.term
												},
												success: function(data) {

													// Set up options and name to value for this returned set.
													var values = $.parseJSON(data);
													options = [];
													nameToValue = [];

													for (optionValue in values) {
														var option = optionValue;
														options.push(values[optionValue]);
														nameToValue[values[optionValue]] = optionValue;
													}

													response(options);
												}
											});
									} <?php } else {
									echo 'options';
								} ?>
							});

							// Also set up a blur function to update the ID.
							$(this).blur(function(e) {
								if (typeof nameToValue[$(this).val()] === 'undefined') {
									$(this).prev('input').val('').trigger('change');
								} else {
									$(this).prev('input').val(nameToValue[$(this).val()]).trigger('change');
								}
							});
							<?php

							// Finish the if block.
							if ($field_object->args['repeatable']) {
								echo '}';
							}

							?>
						});
					});
		</script>
		<?php
	}
}

/**
 * Gets post options using a post type
 *
 * @param mixed $post_type array or string of post type(s)
 * @param string $like_test used to query for posts like a certain value
 * @return array
 */
function autocomplete_cmb2_get_post_options_using_post_type($post_type, $like_test = '%%') {

	// Use a query instead of "get_posts" to save on a ton of memory.
	global $wpdb;

	if (is_array($post_type)) {
		$post_type_query = "('".implode("', '", $post_type)."')";
	} else {
		$post_type_query = "('".$post_type."')";
	}

	$posts = $wpdb->get_results($wpdb->prepare("
		SELECT ID, post_title
    FROM $wpdb->posts
    WHERE post_status = 'publish' AND post_type IN $post_type_query AND post_title LIKE %s
    ORDER BY post_title ASC
 	", $like_test), OBJECT);

	foreach ($posts as $post) {
		$post_options[$post->ID] = $post->post_title;
	}

	return $post_options;
}


/**
 * Gets the jQuery autocomplete widget ready.
 */
function autocomplete_cmb2_admin_enqueue_scripts() {
	wp_enqueue_script('wp-jquery-ui-autocomplete');
	wp_enqueue_style('wp-jquery-ui-autocomplete');
}

/**
 * Gets the post options in JSON format for the autocomplete
 */
function autocomplete_cmb2_get_post_autocomplete_options() {
	die(json_encode(autocomplete_cmb2_get_post_options_using_post_type('post', '%'.$_GET['q'].'%')));
}

add_action('cmb2_render_autocomplete', 'autocomplete_cmb2_render_autocomplete', 10, 5);
add_action('admin_enqueue_scripts', 'autocomplete_cmb2_admin_enqueue_scripts');
add_action('wp_ajax_get_post_options', 'autocomplete_cmb2_get_post_autocomplete_options');
add_filter('cmb2_admin_init', 'autocomplete_cmb2_meta_boxes');