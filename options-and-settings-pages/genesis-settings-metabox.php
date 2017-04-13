<?php

/**
 * CMB2 Genesis Settings Metabox
 *
 * To fetch these options, use `genesis_get_option()`, e.g.
 *      $color = genesis_get_option( 'test_colorpicker' );
 *
 * @version 0.1.0
 */
class Myprefix_Genesis_Settings_Metabox {

	/**
	 * Option key. Could maybe be 'genesis-seo-settings', or other section?
	 *
	 * @var string
	 */
	protected $key = 'genesis-settings';

	/**
	 * The admin page slug.
	 *
	 * @var string
	 */
	protected $admin_page = 'genesis';

	/**
	 * Options page metabox id
	 *
	 * @var string
	 */
	protected $metabox_id = 'myprefix_genesis_settings';

	/**
	 * Admin page hook
	 *
	 * @var string
	 */
	protected $admin_hook = 'toplevel_page_genesis';

	/**
	 * Holds an instance of CMB2
	 *
	 * @var CMB2
	 */
	protected $cmb = null;

	/**
	 * Holds an instance of the object
	 *
	 * @var Myprefix_Genesis_Settings_Metabox
	 */
	protected static $instance = null;

	/**
	 * Returns the running object
	 *
	 * @return Myprefix_Genesis_Settings_Metabox
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'admin_hooks' ) );
		add_action( 'cmb2_admin_init', array( $this, 'init_metabox' ) );
	}

	/**
	 * Add menu options page
	 *
	 * @since 0.1.0
	 */
	public function admin_hooks() {
		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->admin_hook}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );

		// Hook into the genesis cpt setttings save and add in the CMB2 sanitized values.
		add_filter( "sanitize_option_{$this->key}", array( $this, 'add_sanitized_values' ), 999 );

		// Hook up our Genesis metabox.
		add_action( 'genesis_theme_settings_metaboxes', array( $this, 'add_meta_box' ) );
	}


	/**
	 * Hook up our Genesis metabox.
	 *
	 * @since 0.1.0
	 */
	public function add_meta_box() {
		$cmb = $this->init_metabox();
		add_meta_box(
			$cmb->cmb_id,
			$cmb->prop( 'title' ),
			array( $this, 'output_metabox' ),
			$this->admin_hook,
			$cmb->prop( 'context' ),
			$cmb->prop( 'priority' )
		);
	}

	/**
	 * Output our Genesis metabox.
	 *
	 * @since 0.1.0
	 */
	public function output_metabox() {
		$cmb = $this->init_metabox();
		$cmb->show_form( $cmb->object_id(), $cmb->object_type() );
	}

	/**
	 * If saving the cpt settings option, add the CMB2 sanitized values.
	 *
	 * @since 0.1.0
	 *
	 * @param array $new_value Array of values for the setting.
	 *
	 * @return array Updated array of values for the setting.
	 */
	public function add_sanitized_values( $new_value ) {
		if ( ! empty( $_POST ) ) {
			$cmb = $this->init_metabox();

			$new_value = array_merge(
				$new_value,
				$cmb->get_sanitized_values( $_POST )
			);
		}

		return $new_value;
	}

	/**
	 * Register our Genesis metabox and return the CMB2 instance.
	 *
	 * @since  0.1.0
	 *
	 * @return CMB2 instance.
	 */
	public function init_metabox() {
		if ( null !== $this->cmb ) {
			return $this->cmb;
		}

		$this->cmb = cmb2_get_metabox( array(
			'id'           => $this->metabox_id,
			'title'        => __( 'I\'m a Genesis Settings CMB2 metabox', 'myprefix' ),
			'hookup'       => false, // We'll handle ourselves. (add_sanitized_values())
			'cmb_styles'   => false, // We'll handle ourselves. (admin_hooks())
			'context'      => 'main', // Important for Genesis.
			// 'priority'     => 'low', // Defaults to 'high'.
			'object_types' => array( $this->admin_hook ),
			'show_on'      => array(
				// These are important, don't remove.
				'key'   => 'options-page',
				'value' => array( $this->key ),
			),
		), $this->key, 'options-page' );

		// Set our CMB2 fields.
		$this->cmb->add_field( array(
			'name' => __( 'Test Text', 'myprefix' ),
			'desc' => __( 'field description (optional)', 'myprefix' ),
			'id'   => 'test_text',
			'type' => 'text',
			// 'default' => 'Default Text',
		) );

		$this->cmb->add_field( array(
			'name'    => __( 'Test Color Picker', 'myprefix' ),
			'desc'    => __( 'field description (optional)', 'myprefix' ),
			'id'      => 'test_colorpicker',
			'type'    => 'colorpicker',
			'default' => '#bada55',
		) );

		return $this->cmb;
	}

	/**
	 * Public getter method for retrieving protected/private variables.
	 *
	 * @since 0.1.0
	 *
	 * @param string $field Field to retrieve.
	 *
	 * @throws Exception Throws an exception if the field is invalid.
	 *
	 * @return mixed Field value or exception is thrown
	 */
	public function __get( $field ) {
		if ( 'cmb' === $field ) {
			return $this->init_metabox();
		}

		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'admin_page', 'metabox_id', 'admin_hook' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Myprefix_Genesis_Settings_Metabox object
 *
 * @since 0.1.0
 *
 * @return Myprefix_Genesis_Settings_Metabox object
 */
function myprefix_genesis_settings_metabox() {
	return Myprefix_Genesis_Settings_Metabox::get_instance();
}

// Get it started.
myprefix_genesis_settings_metabox();
