<?php
/**
 * This is a sample widget to see if we can use CMB2 to power widgets
 *
 * @version 0.1.0
 */
class CMB2_Widget_Example extends WP_Widget {

	/**
	 * Setup the widget
	 * @since  0.1.0
	 * @return void
	 */
	public function __construct()
	{
		// Widget options
		$options = array(
			'customize_selective_refresh' => true,
		);

		// Set the options in the widget
	}

	/**
	 * Creates a new instance of CMB2 and adds some fields
	 * @since  0.1.0
	 * @return CMB2
	 */
	public function fields()
	{
		// Create a new box in the class
		$fields = new_cmb2_box(array(
			'id'				=> $this->option_name, // Option name is taken from the WP_Widget class.
			'title'				=> __( 'Widget Options', 'theme' ),
			'context'			=> 'normal',
			'priority'			=> 'high',
			'show_names'		=> true,
			'show_on'			=> array(
				'key' => 'options-page', // Tells CMB2 to handle this as an option
				'value' => array( $this->option_name )
			)
		));

		$fields->add_field(array(
			'name'		=> 'Image',
			'desc'		=> 'Upload an image or enter an URL.',
			'id'		=> 'image',
			'type'		=> 'file',
			'options'	=> array(
				'url'	=> false
			),
			'text'		=> array(
				'add_upload_file_text' => 'Upload An Image'
			),
		));

		$fields->add_field(array(
			'name'		=> 'Description',
			'id'		=> 'desc',
			'type'		=> 'textarea'
		));

		return $fields;
	}

	/**
	 * Renders the form
	 *
	 * @param  object $instance
	 * @return void
	 */
	public function form( $instance )
	{

		/**
		 * Handle field registration separate from CMB2 hooks.
		 * Unfortunately I couldn't figure out how to hook into cmb2_admin_init
		 * before the 'form' method ran, so we're just creating an instance of CMB2
		 */
		$cmb2 = $this->fields();

		// Remove the form and submit button, otherwise this won't work
		$args = array(
			'form_format' => '<input type="hidden" name="object_id" value="%2$s">',
		);

		// Echo the form
		cmb2_print_metabox_form( $cmb2, $this->number, $args );
	}

	/**
	 * Traditional way to save the widget settings
	 * @since  0.1.0
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return object WP_Widget
	 */
	public function old_update( $new_instance, $old_instance )
	{
		$instance = array();

		if( !empty( $new_instance ) ){
			foreach( $new_instance as $field => $value ) {
				$instance[$field] = $value;
			}
		}

		return $instance;
	}


	/**
	 * Save the widget settings using the $_POST global variable
	 * and checking that our fields have been registered first.
	 * 
	 * @since  0.1.0
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return object WP_Widget
	 */
	public function update( $new_instance, $old_instance )
	{
		$cmb2 = $this->fields();
		$check = array();

		foreach( $_POST as $field => $value ) {

			// Check if the posted data is a registered field
			if( $cmb2->get_field_ids( $field ) ) {
				$check[$field] = $value;
			}

		}
		return $check;
	}
}

/**
 * Register the widget
 *
 * @since  0.1.0
 * @return void
 */
function cmb2_widget_example_init()
{
	register_widget( 'CMB2_Widget_Example' );
}

// Hook into widgets_init
add_action( 'widgets_init', 'cmb2_widget_example_init' );
